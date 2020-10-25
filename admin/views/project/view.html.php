<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_qwhelloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;

/**
 * Project View
 *
 * @since  0.0.1
 */
class QwhelloworldViewProject extends HtmlView
{
	/**
	 * View form
	 *
	 * @var         form
	 */
	protected $form = null;
    protected $canDo;

	/**
	 * Display the Project view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Get the Data
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
        $this->script = $this->get('Script');

        // What Access Permissions does this user have? What can (s)he do?
		$this->canDo = ContentHelper::getActions('com_qwhelloworld', 'project', $this->item->id);
        
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		// Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolBar()
	{
		$input = Factory::getApplication()->input;

		// Hide Joomla Administrator Main menu
		$input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);
		
		ToolbarHelper::title($isNew ? Text::_('COM_QWHELLOWORLD_MANAGER_PROJECT_NEW')
		                             : Text::_('COM_QWHELLOWORLD_MANAGER_PROJECT_EDIT'), 'project');
		// Build the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($this->canDo->get('core.create')) 
			{
				ToolbarHelper::apply('project.apply', 'JTOOLBAR_APPLY');
				ToolbarHelper::save('project.save', 'JTOOLBAR_SAVE');
				ToolbarHelper::custom('project.save2new', 'save-new.png', 'save-new_f2.png',
				                       'JTOOLBAR_SAVE_AND_NEW', false);
			}
			ToolbarHelper::cancel('project.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($this->canDo->get('core.edit'))
			{
				// We can save the new record
				ToolbarHelper::apply('project.apply', 'JTOOLBAR_APPLY');
				ToolbarHelper::save('project.save', 'JTOOLBAR_SAVE');
 
				// We can save this record, but check the create permission to see
				// if we can return to make a new one.
				if ($this->canDo->get('core.create')) 
				{
					ToolbarHelper::custom('project.save2new', 'save-new.png', 'save-new_f2.png',
					                       'JTOOLBAR_SAVE_AND_NEW', false);
				}
				$config = Factory::getConfig();
				$save_history = $config->get('save_history', true);
				if ($save_history) 
				{
					ToolbarHelper::versions('com_qwhelloworld.project', $this->item->id);
				}
			}
			if ($this->canDo->get('core.create')) 
			{
				ToolbarHelper::custom('project.save2copy', 'save-copy.png', 'save-copy_f2.png',
				                       'JTOOLBAR_SAVE_AS_COPY', false);
			}
			ToolbarHelper::cancel('project.cancel', 'JTOOLBAR_CLOSE');
		}
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$isNew = ($this->item->id < 1);
		$document = Factory::getDocument();
		$document->setTitle($isNew ? Text::_('COM_QWHELLOWORLD_PROJECT_CREATING') :
                Text::_('COM_QWHELLOWORLD_PROJECT_EDITING'));
        $document->addScript(Uri::root() . $this->script);
		$document->addScript(Uri::root() . "/administrator/components/com_qwhelloworld"
		                                  . "/views/project/submitbutton.js");
		Text::script('COM_QWHELLOWORLD_PROJECT_ERROR_UNACCEPTABLE');
	}
}