<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorld component helper.
 *
 * @param   string  $submenu  The name of the active view.
 *
 * @return  void
 *
 * @since   1.6
 */
abstract class HelloWorldHelper extends JHelperContent
{
	/**
	 * Configure the Linkbar.
	 *
	 * @return Bool
	 */

	public static function addSubmenu($submenu) 
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_HELLOWORLD_SUBMENU_MESSAGES'),
			'index.php?option=com_helloworld',
			$submenu == 'helloworlds'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_HELLOWORLD_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&view=categories&extension=com_helloworld',
			$submenu == 'categories'
		);

		// Set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-helloworld ' .
										'{background-image: url(../media/com_helloworld/images/tux-48x48.png);}');
		if ($submenu == 'categories') 
		{
			$document->setTitle(JText::_('COM_HELLOWORLD_ADMINISTRATION_CATEGORIES'));
		}
		if (JComponentHelper::isEnabled('com_fields'))
		{
			JHtmlSidebar::addEntry(
				JText::_('JGLOBAL_FIELDS'),
				'index.php?option=com_fields&context=com_helloworld.helloworld',
				$submenu == 'fields.fields'
			);

			JHtmlSidebar::addEntry(
				JText::_('JGLOBAL_FIELD_GROUPS'),
				'index.php?option=com_fields&view=groups&context=com_helloworld.helloworld',
				$submenu == 'fields.groups'
			);
		}
	}
    
    /**
	 * Get the actions
	 */
	public static function getActions($component = '', $section = '', $messageId = 0)
	{	
		$result	= new JObject;

		if (empty($messageId)) {
			$assetName = 'com_helloworld';
		}
		else {
			$assetName = 'com_helloworld.message.'.(int) $messageId;
		}

		$actions = JAccess::getActions('com_helloworld', 'component');

		foreach ($actions as $action) {
            $value = JFactory::getUser()->authorise($action->name, $assetName);
			$result->set($action->name, $value);
		}

		return $result;
	}

	public static function getContexts()
	{
		JFactory::getLanguage()->load('com_helloworld', JPATH_ADMINISTRATOR);

		$contexts = array(
			'com_helloworld.helloworld' => JText::_('COM_HELLOWORLD_ITEMS'),
			'com_helloworld.categories' => JText::_('JCATEGORY')
		);

		return $contexts;
	}
	
	public static function validateSection($section, $item)
	{
		if (JFactory::getApplication()->isClient('site') && $section == 'form')
		{
			return 'helloworld';
		}
		if ($section != 'helloworld' && $section != 'form')
		{
			return null;
		}

		return $section;
	}
}