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

use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;

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
			Text::_('COM_HELLOWORLD_SUBMENU_MESSAGES'),
			'index.php?option=com_helloworld',
			$submenu == 'helloworlds'
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_HELLOWORLD_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&view=categories&extension=com_helloworld',
			$submenu == 'categories'
		);

		// Set some global property
		$document = Factory::getDocument();
		$document->addStyleDeclaration('.icon-48-helloworld ' .
										'{background-image: url(../media/com_helloworld/images/tux-48x48.png);}');
		if ($submenu == 'categories') 
		{
			$document->setTitle(Text::_('COM_HELLOWORLD_ADMINISTRATION_CATEGORIES'));
		}
		if (ComponentHelper::isEnabled('com_fields'))
		{
			JHtmlSidebar::addEntry(
				Text::_('JGLOBAL_FIELDS'),
				'index.php?option=com_fields&context=com_helloworld.helloworld',
				$submenu == 'fields.fields'
			);

			JHtmlSidebar::addEntry(
				Text::_('JGLOBAL_FIELD_GROUPS'),
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
		$result	= new CMSObject;

		if (empty($messageId)) {
			$assetName = 'com_helloworld';
		}
		else {
			$assetName = 'com_helloworld.message.'.(int) $messageId;
		}

		$actions = Access::getActions('com_helloworld', 'component');

		foreach ($actions as $action) {
            $value = Factory::getUser()->authorise($action->name, $assetName);
			$result->set($action->name, $value);
		}

		return $result;
	}

	public static function getContexts()
	{
		Factory::getLanguage()->load('com_helloworld', JPATH_ADMINISTRATOR);

		$contexts = array(
			'com_helloworld.helloworld' => Text::_('COM_HELLOWORLD_ITEMS'),
			'com_helloworld.categories' => Text::_('JCATEGORY')
		);

		return $contexts;
	}
	
	public static function validateSection($section, $item)
	{
		if (Factory::getApplication()->isClient('site') && $section == 'form')
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