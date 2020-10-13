<?php
/**
 * Layout file for the main body component of the modal showing the batch options
 * This layout displays the various html input elements relating to the batch processes
 */
defined('_JEXEC') or die;
$published = $this->state->get('filter.published');
?>

<div class="container-fluid">

	<div class="row-fluid">

		<div class="control-group span6">
			<div class="controls">
				<?php echo JLayoutHelper::render('joomla.html.batch.item', array('extension' => 'com_helloworld')); ?>
			</div>
			<div class="controls">
				<?php echo JLayoutHelper::render('position', array()); ?>
			</div>
		</div>

		<div class="control-group span6">
			<div class="controls">
				<?php echo JLayoutHelper::render('joomla.html.batch.language', array()); ?>
			</div>
			<div class="controls">
				<?php echo JLayoutHelper::render('joomla.html.batch.access', array()); ?>
			</div>
			<div class="controls">
				<?php echo JLayoutHelper::render('joomla.html.batch.tag', array()); ?>
			</div>
		</div>

	</div>
	
</div>