<?php
App::uses('AppController', 'Controller');
/**
 * Tags Controller
 *
 * @property Tag $Tag
 */
class TagsController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(
			'auto_complete',
			'get_name',
			'getnodes',
			'index',
			'view'
		);
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Tag->recursive = 0;
		$this->set('tags', $this->paginate());
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Tag->id = $id;
		if (!$this->Tag->exists()) {
			throw new NotFoundException(__('Invalid tag'));
		}
		$this->set('tag', $this->Tag->read(null, $id));
	}

/**
 * delete method
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Tag->id = $id;
		if (!$this->Tag->exists()) {
			throw new NotFoundException(__('Invalid tag'));
		}
		if ($this->Tag->delete()) {
			$this->Flash->set(__('Tag deleted'), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Flash->set(__('Tag was not deleted'), 'error');
		$this->redirect(array('action' => 'index'));
	}
	
	public function manage() {
		$this->set(array(
			'title_for_layout' => 'Manage Tags'
		));
	}
	
	public function getnodes() {
	    // find all the root nodes or nodes underneath the parent node
	    // the second parameter (true) means we only want direct children
		if (isset($_POST['node']) && $_POST['node'] != 0 && $_POST['node'] != 'root') {
			$parent_id = $_POST['node'];
		} else {
			$parent_id = null;
		}
		$nodes = $this->Tag->children($parent_id, true);
			
	    $rearranged_nodes = array('branches' => array(), 'leaves' => array());
	    foreach ($nodes as $key => &$node) {
	    	$tag_id = $node['Tag']['id'];
	    	
	    	// Check for children
	    	$has_children = $this->Tag->childCount($tag_id, true);
	    	if ($has_children) {
	    		$tag_name = $node['Tag']['name'];
	    		$rearranged_nodes['branches'][$tag_name] = $node;
	    	} else {
				$rearranged_nodes['leaves'][$tag_id] = $node;	
	    	}
	    }

	    // Sort nodes by alphabetical branches, then alphabetical leaves
    	ksort($rearranged_nodes['branches']);
    	ksort($rearranged_nodes['leaves']);
		$nodes = array_merge(
			array_values($rearranged_nodes['branches']),
			array_values($rearranged_nodes['leaves'])
		);
	    
	    // Visually note categories with no data
	    $showNoCommentaries = true;

	    // send the nodes to our view
	    $this->set(compact('nodes', 'showNoCommentaries'));

	    $this->layout = 'blank';
	}
	
	public function recover() {
		list($start_usec, $start_sec) = explode(" ", microtime());
		set_time_limit(3600);
		$this->Tag->recover();
		list($end_usec, $end_sec) = explode(" ", microtime());
		$start_time = $start_usec + $start_sec;
		$end_time = $end_usec + $end_sec;
		$loading_time = $end_time - $start_time;
		$minutes = round($loading_time / 60, 2);
		return $this->renderMessage(array(
			'message' => "Done recovering tag tree (took $minutes minutes).",
			'class' => 'success',
			'layout' => 'ajax'
		));
    }
    
	public function auto_complete() {
		App::uses('Sanitize', 'Utility');
		$string_to_complete = Sanitize::clean($_GET['term']);
		$limit = 10;
		
		// Tag.name will be compared via LIKE to each of these, 
		// in order, until $limit tags are found.
		$like_conditions = array(
			$string_to_complete,
			$string_to_complete.' %',
			$string_to_complete.'%',
			'% '.$string_to_complete.'%',
			'%'.$string_to_complete.'%'
		);
		
		// Collect tags up to $limit
		$tags = array();
		foreach ($like_conditions as $like) {
			if (count($tags) == $limit) {
				break;	
			}
			$results = $this->Tag->find('all', array(
				'fields' => array('Tag.id', 'Tag.name'),
				'conditions' => array('Tag.name LIKE' => $like),
				'contain' => false,
				'limit' => $limit - count($tags)
			));
			foreach ($results as $result) {
			    $tagName = $result['Tag']['name'];
                $tagId = $result['Tag']['id'];

                if (isset($tags[$tagName])) {
                    continue;
                }

                $tags[$tagName] = [
                    'label' => $tagName,
                    'value' => $tagName,
                ];
			}
		}

		$tags = array_values($tags);
		
		$this->set(compact('tags'));
		$this->layout = 'blank';
	}
	
	public function reorder() {

		// retrieve the node instructions from javascript
		// delta is the difference in position (1 = next node, -1 = previous node)

		$node = intval($_POST['node']);
		$delta = intval($_POST['delta']);

		if ($delta > 0) {
			$this->Tag->moveDown($node, abs($delta));
		} elseif ($delta < 0) {
			$this->Tag->moveUp($node, abs($delta));
		}

		// send success response
		exit('1');

	}

	public function reparent() {
		$node = intval($_POST['node']);
		$parent = ($_POST['parent'] == 'root') ? 0 : intval($_POST['parent']);
		$this->Tag->id = $node;
		
		// Move tag
		$this->Tag->saveField('parent_id', $parent);

		// If position == 0, then we move it straight to the top
		// otherwise we calculate the distance to move ($delta).
		// We have to check if $delta > 0 before moving due to a bug
		// in the tree behaviour (https://trac.cakephp.org/ticket/4037)
		$position = intval($_POST['position']);
		if ($position == 0) {
			$this->Tag->moveUp($node, true);
		} else {
			$count = $this->Tag->childCount($parent, true);
			$delta = $count-$position-1;
			if ($delta > 0) {
				$this->Tag->moveUp($node, $delta);
			}
		}

		// send success response
		exit('1');
	}
	
	/**
	 * Returns the name of the Tag with id $id, used by the tag manager
	 * @param int $id
	 */
	public function get_name($id) {
		$this->Tag->id = $id;
		if ($this->Tag->exists()) {
			$name = $this->Tag->field('name');
		} else {
			$name = "Error: Tag does not exist";	
		}
		$this->set(compact('name'));
		$this->layout = 'ajax';
	}
	
	public function remove($name) {
		$tag_id = $this->Tag->getIdFromName($name);
		if (! $tag_id) {
			$message = "The tag \"$name\" does not exist (you may have already deleted it).";
			$class = 'error';
		} elseif ($this->Tag->delete($tag_id)) {
			$message = "Tag \"$name\" deleted.";
			$class = 'success';
		} else {
			$message = "There was an unexpected error deleting the \"$name\" tag.";
			$class = 'error';
		}
		return $this->renderMessage(array(
			'message' => $message,
			'class' => $class,
			'layout' => 'ajax'
		));
	}
	
/**
	 * Turns all associations with Tag $tag_id into associations with Tag $merge_into_id
	 * and deletes Tag $tag_id, and moves any child tags under Tag $merge_into_id.  
	 * @param int $tag_id
	 * @param int $merge_into_id
	 */
	public function merge($removed_tag_name = '', $retained_tag_name = '') {
		$this->layout = 'ajax';
		$removed_tag_name = trim($removed_tag_name);
		$retained_tag_name = trim($retained_tag_name);
		
		// Verify input
		if ($removed_tag_name == '') {
			return $this->renderMessage(array(
				'message' => 'No name provided for the tag to be removed.',
				'class' => 'error'
			));
		} else {
			$removed_tag_id = $this->Tag->getIdFromName($removed_tag_name);
			if (! $removed_tag_id) {
				return $this->renderMessage(array(
					'message' => "The tag \"$removed_tag_name\" could not be found.",
					'class' => 'error'
				));	
			}
		}
		if ($retained_tag_name == '') {
			return $this->renderMessage(array(
				'message' => 'No name provided for the tag to be retained.',
				'class' => 'error'
			));
		} else {
			$retained_tag_id = $this->Tag->getIdFromName($retained_tag_name);
			if (! $retained_tag_id) {
				return $this->renderMessage(array(
					'message' => "The tag \"$retained_tag_name\" could not be found.",
					'class' => 'error'
				));	
			}
		}
		if ($removed_tag_id == $retained_tag_id) {
			return $this->renderMessage(array(
				'message' => "Cannot merge \"$retained_tag_name\" into itself.",
				'class' => 'error'
			));
		}
		
		$message = '';
		$class = 'success';
		
		// Switch commentary associations
		$associated_count = $this->Tag->CommentariesTag->find('count', array(
			'conditions' => array('tag_id' => $removed_tag_id)
		));
		if ($associated_count) {
			$result = $this->Tag->query("
				UPDATE commentaries_tags 
				SET tag_id = $retained_tag_id 
				WHERE tag_id = $removed_tag_id
			");
			$message .= "Changed association with \"$removed_tag_name\" into \"$retained_tag_name\" in $associated_count commentary(s).<br />";
		} else {
			$message .= 'No associated commentaries to edit.<br />';
		}
		
		// Move child tags
		$children = $this->Tag->find('list', array(
			'conditions' => array('parent_id' => $removed_tag_id)
		));
		if (empty($children)) {
			$message .= 'No child-tags to move.<br />';
		} else {
			foreach ($children as $child_id => $child_name) {
				$this->Tag->id = $child_id;
				if ($this->Tag->saveField('parent_id', $retained_tag_id)) {
					$message .= "Moved \"$child_name\" from under \"$removed_tag_name\" to under \"$retained_tag_name\".<br />";
				} else {
					$class = 'error';
					$message .= "Error moving \"$child_name\" from under \"$removed_tag_name\" to under \"$retained_tag_name\".<br />";	
				}
			}
			// $message .= "Moved ".count($children)." child tag".(count($children) == 1 ? '' : 's')." of \"$removed_tag_name\" under tag \"$retained_tag_name\".<br />";
		}
		
		// Delete tag
		if ($class == 'success') {
			if ($this->Tag->delete($removed_tag_id)) {
				$message .= "Removed \"$removed_tag_name\".";
			} else {
				$message .= "Error trying to delete \"$removed_tag_name\" from the database.";
				$class = 'error';
			}
		} else {
			$message .= "\"$removed_tag_name\" not removed.";
		}
		
		return $this->renderMessage(array(
			'message' => $message,
			'class' => $class
		));
	}
	
	/**
	 * Removes entries from the commentaries_tags join table where either the tag or commentary no longer exists
	 */
	public function remove_broken_associations() {
		set_time_limit(120);
		$this->layout = 'ajax';
		
		$associations = $this->Tag->CommentariesTag->find('all', array('contain' => false));
		$tags = $this->Tag->find('list');
		$commentaries = $this->Tag->Commentary->find('list');
		foreach ($associations as $a) {
			// Note missing tags/commentaries for output message
			$t = $a['CommentariesTag']['tag_id'];
			if (! isset($tags[$t])) {
				$missing_tags[$t] = true;
			}
			$e = $a['CommentariesTag']['commentary_id'];
			if (! isset($commentaries[$e])) {
				$missing_commentaries[$e] = true;
			}
			
			// Remove broken association
			if (! isset($tags[$t]) || ! isset($commentaries[$e])) {
				$this->Tag->CommentariesTag->delete($a['CommentariesTag']['id']);
			}
		}
		$message = '';
		if (! empty($missing_tags)) {
			$message .= 'Removed associations with nonexistent tags: '.implode(', ', array_keys($missing_tags)).'<br />';	
		}
		if (! empty($missing_commentaries)) {
			$message .= 'Removed associations with nonexistent commentaries: '.implode(', ', array_keys($missing_commentaries)).'<br />';	
		}
		if ($message == '') {
			$message = 'No broken associations to remove.';	
		}
		return $this->renderMessage(array(
			'message' => $message,
			'class' => 'success',
			'layout' => 'ajax'
		));
	}
	
	public function edit($tag_name = null) {
		if ($this->request->is('ajax')) {
			$this->layout = 'ajax';
		}
		if ($this->request->is('put') || $this->request->is('post')) {
			$this->request->data['Tag']['name'] = strtolower(trim($this->request->data['Tag']['name']));
			$count = $this->Tag->find('count', array(
				'conditions' => array(
					'Tag.name' => $this->request->data['Tag']['name'],
					'Tag.id NOT' => $this->request->data['Tag']['id']
				)
			));
			if ($count > 0) {
				return $this->renderMessage(array(
					'message' => 'That tag\'s name cannot be changed to "'.$this->request->data['Tag']['name'].'" because another tag already has that name. You can, however, merge this tag into that tag.',
					'class' => 'error'
				));
			}
			if ($this->Tag->save($this->request->data)) {
				$message = 'Tag successfully edited.';
				return $this->renderMessage(array(
					'message' => $message,
					'class' => 'success'
				));
			}
			return $this->renderMessage(array(
				'message' => 'There was an error editing that tag.',
				'class' => 'error'
			));
		} else {
			if (! $tag_name) {
				return $this->renderMessage(array(
					'title' => 'Tag Name Not Provided',
					'message' => 'Please try again. But with a tag name provided this time.',
					'class' => 'error'
				));
			}
			$result = $this->Tag->find('all', array(
				'conditions' => array('Tag.name' => $tag_name),
				'contain' => false
			));
			if (empty($result)) {
				return $this->renderMessage(array(
					'title' => 'Tag Not Found',
					'message' => "Could not find a tag with the exact tag name \"$tag_name\".",
					'class' => 'error'
				));
			}
			if (count($result) > 1) {
				$tag_ids = array();
				foreach ($result as $tag) {
					$tag_ids[] = $tag['Tag']['id'];	
				}
				return $this->renderMessage(array(
					'title' => 'Duplicate Tags Found',
					'message' => "Tags with the following IDs are named \"$tag_name\": ".implode(', ', $tag_ids).'<br />You will need to merge them before editing.',
					'class' => 'error'
				));
			}
			$this->request->data = $result[0];
		}
	}
	
	public function add() {
		$this->layout = 'ajax';
		if (! $this->request->is('post')) {
			return;
		}
		if (trim($this->request->data['Tag']['name']) == '') {
			return $this->renderMessage(array(
				'title' => 'Error',
				'message' => "Tag name is blank",
				'class' => 'error'
			));
		}
		
		// Determine parent_id
		$parent_name = $this->request->data['Tag']['parent_name'];
		if ($parent_name == '') {
			$root_parent_id = null;
		} else {
			$root_parent_id = $this->Tag->getIdFromName($parent_name);
			if (! $root_parent_id) {
				return $this->renderMessage(array(
					'title' => 'Error',
					'message' => "Parent tag \"$parent_name\" not found",
					'class' => 'error'
				));	
			}
		}
		
		$class = 'success';
		$message = '';
		$inputted_names = explode("\n", trim(strtolower($this->request->data['Tag']['name'])));
		$level = 0;
		$parents = array($root_parent_id);
		foreach ($inputted_names as $line_num => $name) {
			$level = $this->Tag->getIndentLevel($name);
			
			// Discard any now-irrelevant data
			$parents = array_slice($parents, 0, $level + 1);
			
			// Determine this tag's parent_id
			if ($level == 0) {
				$parent_id = $root_parent_id;
			} elseif (isset($parents[$level])) {
				$parent_id = $parents[$level];
			} else {
				$class = 'error';
				$message .= "Error with nested tag structure. Looks like there's an extra indent in line $line_num: \"$name\".<br />";
				continue;
			}
			
			// Strip leading/trailing whitespace and hyphens used for indenting
			$name = trim(ltrim($name, '-'));
			
			// Confirm that the tag name is non-blank and non-redundant
			if (! $name) {
				continue;
			}
			$exists = $this->Tag->find('count', array(
				'conditions' => array('Tag.name' => $name)
			));
			if ($exists) {
				$class = 'error';
				$message .= "Cannot create the tag \"$name\" because a tag with that name already exists.<br />";
				continue;	
			}
			
			// Add tag to database
			$this->Tag->create();
			$save_result = $this->Tag->save(array('Tag' => array(
				'name' => $name, 
				'parent_id' => $parent_id,
				'listed' => 1,
				'selectable' => 1
			)));
			if ($save_result) {
				$message .= "Created tag #{$this->Tag->id}: $name<br />";
				$parents[$level + 1] = $this->Tag->id;
			} else {
				$class = 'error';
				$message .= "Error creating the tag \"$name\"<br />";
				print_r($this->Tag->validationErrors);
			}
		}
		
		return $this->renderMessage(array(
			'title' => 'Results:',
			'message' => $message,
			'class' => $class
		));
	}
}