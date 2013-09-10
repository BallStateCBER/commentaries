function setupTagArranger() {
	Ext.BLANK_IMAGE_URL = '/ext-2.0.1/resources/images/default/s.gif';
	Ext.onReady(function(){
		var getnodesUrl = '/tags/getnodes/';
		var reorderUrl = '/tags/reorder/';
		var reparentUrl = '/tags/reparent/';
		var Tree = Ext.tree;
		var tree = new Tree.TreePanel({
			el:'tree-div',
			autoScroll:true,
			animate:true,
			enableDD:true,
			containerScroll: true,
			rootVisible: true,
			loader: new Ext.tree.TreeLoader({
				dataUrl:getnodesUrl,
				preloadChildren: true
			})
		});
		var root = new Tree.AsyncTreeNode({
			text:'Tags',
			draggable:false,
			id:'root'
		});
		tree.setRootNode(root);		
		var oldPosition = null;
		var oldNextSibling = null;
		tree.on('startdrag', function(tree, node, event){
			oldPosition = node.parentNode.indexOf(node);
			oldNextSibling = node.nextSibling;
		});
		tree.on('movenode', function(tree, node, oldParent, newParent, position){
			if (oldParent == newParent){
				var url = reorderUrl;
				var params = {'node':node.id, 'delta':(position-oldPosition)};
			} else {
				var url = reparentUrl;
				var params = {'node':node.id, 'parent':newParent.id, 'position':position};
			}
			// we disable tree interaction until we've heard a response from the server
			// this prevents concurrent requests which could yield unusual results
			tree.disable();
			Ext.Ajax.request({
				url:url,
				params:params,
				success:function(response, request) {
					// if the first char of our response is not 1, then we fail the operation,
					// otherwise we re-enable the tree
					if (response.responseText.charAt(0) != 1){
						alert(response.responseText);
						request.failure();
					} else {
						tree.enable();
					}
				},
				failure:function() {
					// we move the node back to where it was beforehand and
					// we suspendEvents() so that we don't get stuck in a possible infinite loop
					tree.suspendEvents();
					oldParent.appendChild(node);
					if (oldNextSibling){
						oldParent.insertBefore(node, oldNextSibling);
					}
					tree.resumeEvents();
					tree.enable();
					alert("Error: Your changes could not be saved");
				}
			});
		});
		// render the tree
		tree.render();
		root.expand();
	});
}

function setupTagManager() {
	$(function() {
		// Tabs
		$('#tag_management_tabs').tabs();
		
		// Aranger
		setupTagArranger();
		
		// Fix functions
		$('#tab-fix a').click(function (event) {
			event.preventDefault();
			$.ajax({
				url: this.href,
				success: function (data) {
					$('<div>'+data+'</div>').prependTo('#tab-fix .results');
				},
				error: function() {
					alert('The server returned an error.');
				}
			});
		});
		
		// Empty trash function
		$('#tab-remove a').click(function (event) {
			event.preventDefault();
			$.ajax({
				url: this.href,
				success: function (data) {
					$('<div>'+data+'</div>').prependTo('#tab-remove .results');
				},
				error: function() {
					alert('The server returned an error.');
				}
			});
		});
		
		// Autocomplete fields
		$('.search_field').autocomplete({
			// '/0/0' includes unlisted and unselectable tags
			source: '/tags/auto_complete/0/0'
		});
		
		// Find
		$('#tag_search_form').submit(function(event) {
			event.preventDefault();
			$.ajax({
				url: '/tags/trace/'+$(this).find('.search_field').val(),
				success: function(data) {
					$('#trace_results').html(data);
				},
				error: function() {
					alert('The server returned an error.');
				}
			});
		});
		
		// Edit
		$('#tag_edit_search_form').submit(function(event) {
			event.preventDefault();
			$.ajax({
				url: '/tags/edit/'+$(this).find('.search_field').val(),
				success: function(data) {
					$('#edit_results').html(data);
					
					// Set up resulting form (if any) to load results in same div 
					$('#edit_results form').ajaxForm({
						target: '#edit_results'
					});
				}
			});
		});
		
		// Remove
		$('#tag_remove_form').submit(function(event) {
			event.preventDefault();
			var tag_name = $('#tag_remove_field').val();
			$.ajax({
				url: '/tags/remove/'+tag_name,
				success: function(data) {
					$('<div>'+data+'</div>').prependTo('#tab-remove .results');
				},
				error: function() {
					alert('The server returned an error.');
				}
			});
		});
		
		// Merge
		$('#tag_merge_form').submit(function(event) {
			event.preventDefault();
			var removed_tag = $('#tag_merge_from_field').val();
			var retained_tag = $('#tag_merge_into_field').val();
			$.ajax({
				url: '/tags/merge/'+encodeURIComponent(removed_tag)+'/'+encodeURIComponent(retained_tag),
				success: function(data) {
					$('<div>'+data+'</div>').prependTo('#tab-merge .results');
				},
				error: function() {
					alert('The server returned an error.');
				}
			});
		});
		
		// Add
		$('#tab-add form').ajaxForm({
			target: '#add_results',
			beforeSend: function () {
				$('#add_results').empty();
		    	$('#tab-add input[type=submit]').attr('disabled', 'disabled');
			},
			complete: function () {
		    	$('#tab-add input[type=submit]').removeAttr('disabled');
			}
		});
	});
}

function isNumeric(input) {
	return (input - 0) == input && input.length > 0;
}