<?php
/**
 * Layout file for the footer component of the modal showing the batch options
 */
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
?>
<a class="btn" type="button" onclick="document.getElementById('batch-category-id').value='';document.getElementById('batch-access').value='';document.getElementById('batch-language-id').value='';document.getElementById('batch-user-id').value='';document.getElementById('batch-tag-id').value=''" data-dismiss="modal">
	<?php echo Text::_('JCANCEL'); ?>
</a>
<button class="btn btn-success" type="submit" onclick="Joomla.submitbutton('project.batch');">
	<?php echo Text::_('JGLOBAL_BATCH_PROCESS'); ?>
</button>