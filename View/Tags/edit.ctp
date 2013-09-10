<?php echo $this->Form->create('Tag');?>
<?php echo $this->Form->input('id', array('type' => 'hidden')); ?>
<?php echo $this->Form->input('name'); ?>
<?php echo $this->Form->end('Update tag #'.$this->request->data['Tag']['id']); ?>