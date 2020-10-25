<?php

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

/**
 * Supports a modal for selecting a project record
 *
 */
class JFormFieldModal_Project extends FormField
{
	/**
	 * Method to get the html for the input field.
	 *
	 * @return  string  The field input html.
	 */
	protected function getInput()
	{
		// Load language
		Factory::getLanguage()->load('com_qwhelloworld', JPATH_ADMINISTRATOR);

		// $this->value is set if there's a default id specified in the xml file
		$value = (int) $this->value > 0 ? (int) $this->value : '';
        
		// $this->id will be jform_request_xxx where xxx is the name of the field in the xml file
		// or jform_associations_xx_yy where xx_yy is the language code (hyphen replaced by underscore) for associations
		$modalId = 'Project_' . $this->id;

		// Add the modal field script to the document head.
		HTMLHelper::_('jquery.framework');
		HTMLHelper::_('script', 'system/modal-fields.js', array('version' => 'auto', 'relative' => true));

		// our callback function from the modal to the main window:
		Factory::getDocument()->addScriptDeclaration("
			function jSelectProject_" . $this->id . "(id, title, catid, object, url, language) {
				window.processModalSelect('Project', '" . $this->id . "', id, title, catid, object, url, language);
			}
			");

		// if a default id is set, then get the corresponding project to display it
		if ($value)
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('title'))
				->from($db->quoteName('#__com_qwhelloworld_projects'))
				->where($db->quoteName('id') . ' = ' . (int) $value);
			$db->setQuery($query);

			try
			{
				$title = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JError::raiseWarning(500, $e->getMessage());
			}
		}
        
		// display the default project or "Select" if no default specified
		$title = empty($title) ? Text::_('COM_QWHELLOWORLD_MENUITEM_SELECT_PROJECT') : htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
		$html  = '<span class="input-append">';
		$html .= '<input class="input-medium" id="' . $this->id . '_name" type="text" value="' . $title . '" disabled="disabled" size="35" />';

		// html for the Select button
		$html .= '<a'
			. ' class="btn hasTooltip' . ($value ? ' hidden' : '') . '"'
			. ' id="' . $this->id . '_select"'
			. ' data-toggle="modal"'
			. ' role="button"'
			. ' href="#ModalSelect' . $modalId . '"'
			. ' title="' . HTMLHelper::tooltipText('COM_QWHELLOWORLD_MENUITEM_SELECT_BUTTON_TOOLTIP') . '">'
			. '<span class="icon-file" aria-hidden="true"></span> ' . Text::_('JSELECT')
			. '</a>';

		// html for the Clear button
		$html .= '<a'
			. ' class="btn' . ($value ? '' : ' hidden') . '"'
			. ' id="' . $this->id . '_clear"'
			. ' href="#"'
			. ' onclick="window.processModalParent(\'' . $this->id . '\'); return false;">'
			. '<span class="icon-remove" aria-hidden="true"></span>' . Text::_('JCLEAR')
			. '</a>';

		$html .= '</span>';

		// url for the iframe
		$linkProjects = 'index.php?option=com_qwhelloworld&amp;view=projects&amp;layout=modal&amp;tmpl=component&amp;' . Session::getFormToken() . '=1';
		$urlSelect = $linkProjects . '&amp;function=jSelectProject_' . $this->id;
        
		// title to go in the modal header
		$modalTitle    = Text::_('COM_QWHELLOWORLD_MENUITEM_SELECT_MODAL_TITLE');

		// if the form definition has a 'language' field then it's for the association
		// add the forcedLanguage parameter to the URL, and add the language to the modal title
		if (isset($this->element['language']))
		{
			$urlSelect .= '&amp;forcedLanguage=' . $this->element['language'];
			$modalTitle .= ' &#8212; ' . $this->element['label'];
		}

		// html to set up the modal iframe
		$html .= HTMLHelper::_(
			'bootstrap.renderModal',
			'ModalSelect' . $modalId,
			array(
				'title'       => $modalTitle,
				'url'         => $urlSelect,
				'height'      => '400px',
				'width'       => '800px',
				'bodyHeight'  => '70',
				'modalWidth'  => '80',
				'footer'      => '<a role="button" class="btn" data-dismiss="modal" aria-hidden="true">' . Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>',
			)
		);

		// class='required' for client side validation.
		$class = $this->required ? ' class="required modal-value"' : '';

		// hidden input field to store the project record id
		$html .= '<input type="hidden" id="' . $this->id . '_id" ' . $class 
			. ' data-required="' . (int) $this->required . '" name="' . $this->name
			. '" data-text="' . htmlspecialchars(Text::_('COM_QWHELLOWORLD_MENUITEM_SELECT_PROJECT', true), ENT_COMPAT, 'UTF-8') 
			. '" value="' . $value . '" />';

		return $html;
	}

	/**
	 * Method to get the html for the label field.
	 *
	 * @return  string  The field label html.
	 */
	protected function getLabel()
	{
		return str_replace($this->id, $this->id . '_id', parent::getLabel());
	}
}