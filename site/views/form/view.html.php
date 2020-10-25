<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_qwhelloworld
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Uri\Uri;

/**
 * Project View
 * This is the site view presenting the user with the ability to add a new Project record
 * 
 */
class QwhelloworldViewForm extends HtmlView
{

	protected $form = null;
	protected $canDo;

	/**
	 * Display the Project view
	 *
	 * @param   string  $tpl  The name of the layout file to parse.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Get the form to display
		$this->form = $this->get('Form');
		// Get the javascript script file for client-side validation
		$this->script = $this->get('Script'); 

		// Propose current language as default
		if (Multilanguage::isEnabled())
		{
			$lang = Factory::getLanguage()->getTag();
			$this->form->setFieldAttribute('language', 'default', $lang);
		}

		// Check that the user has permissions to create a new project record
		$this->canDo = ContentHelper::getActions('com_qwhelloworld');
		if (!($this->canDo->get('core.create'))) 
		{
			$app = Factory::getApplication(); 
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->setHeader('status', 403, true);
			return;
		}
        
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Call the parent display to display the layout file
		parent::display($tpl);

		// Set properties of the html document
		$this->setDocument();
	}

	/**
	 * Method to set up the html document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = Factory::getDocument();
		$document->setTitle(Text::_('COM_QWHELLOWORLD_PROJECT_CREATING'));
		$document->addScript(Uri::root() . $this->script);
		$document->addScript(Uri::root() . "/administrator/components/com_qwhelloworld"
		                                  . "/views/project/submitbutton.js");
		Text::script('COM_QWHELLOWORLD_PROJECT_ERROR_UNACCEPTABLE');
	}
}