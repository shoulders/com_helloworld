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

use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;

/**
 * Qwhelloworld component helper.
 *
 * @param   string  $submenu  The name of the active view.
 *
 * @return  void
 *
 * @since   1.6
 */
abstract class QwhelloworldHelper extends ContentHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @return Bool
	 */

	public static function addSubmenu($submenu) 
	{
		JHtmlSidebar::addEntry(
			Text::_('COM_QWHELLOWORLD_SUBMENU_PROJECTS'),
			'index.php?option=com_qwhelloworld',
			$submenu == 'projects'
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_QWHELLOWORLD_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&view=categories&extension=com_qwhelloworld',
			$submenu == 'categories'
		);

		// Set some global property
		$document = Factory::getDocument();
		$document->addStyleDeclaration('.icon-48-qwhelloworld ' .
										'{background-image: url(../media/com_qwhelloworld/images/tux-48x48.png);}');
		if ($submenu == 'categories') 
		{
			$document->setTitle(Text::_('COM_QWHELLOWORLD_ADMINISTRATION_CATEGORIES'));
		}
		if (ComponentHelper::isEnabled('com_fields'))
		{
			JHtmlSidebar::addEntry(
				Text::_('JGLOBAL_FIELDS'),
				'index.php?option=com_fields&context=com_qwhelloworld.project',
				$submenu == 'fields.fields'
			);

			JHtmlSidebar::addEntry(
				Text::_('JGLOBAL_FIELD_GROUPS'),
				'index.php?option=com_fields&view=groups&context=com_qwhelloworld.project',
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
			$assetName = 'com_qwhelloworld';
		}
		else {
			$assetName = 'com_qwhelloworld.message.'.(int) $messageId;
		}

		$actions = Access::getActions('com_qwhelloworld', 'component');

		foreach ($actions as $action) {
            $value = Factory::getUser()->authorise($action->name, $assetName);
			$result->set($action->name, $value);
		}

		return $result;
	}

	public static function getContexts()
	{
		Factory::getLanguage()->load('com_qwhelloworld', JPATH_ADMINISTRATOR);

		$contexts = array(
			'com_qwhelloworld.project' => Text::_('COM_QWHELLOWORLD_ITEMS'),
			'com_qwhelloworld.categories' => Text::_('JCATEGORY')
		);

		return $contexts;
	}
	
	public static function validateSection($section, $item)
	{
		if (Factory::getApplication()->isClient('site') && $section == 'form')
		{
			return 'project';
		}
		if ($section != 'project' && $section != 'form')
		{
			return null;
		}

		return $section;
	}
}