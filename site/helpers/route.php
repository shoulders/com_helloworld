<?php

defined('_JEXEC') or die;

use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Router\Route;

/**
 * Qwhelloworld Component Helper file for generating the URL Routes
 *
 */
class QwhelloworldHelperRoute
{
	/**
	 * When the project is displayed then there is also shown a map with a Search Here button.
	 * This function generates the URL which the Ajax call will use to perform the search. 
	 * 
	 */
	public static function getAjaxURL()
	{
		if (!Multilanguage::isEnabled())
		{
			return null;
		}
        
		$lang = Factory::getLanguage()->getTag();
		$app  = Factory::getApplication();
		$sitemenu= $app->getMenu();
		$thismenuitem = $sitemenu->getActive();

		// if we haven't got an active menuitem, or we're currently on a menuitem 
		// with view=category or note = "Ajax", then just stay on it
		if (!$thismenuitem || strpos($thismenuitem->link, "view=category") !== false || $thismenuitem->note == "Ajax")
		{
			return null;
		}

		// look for a menuitem with the right language, and a note field of "Ajax"
		$menuitem = $sitemenu->getItems(array('language','note'), array($lang, "Ajax"));
		if ($menuitem)
		{
			$itemid = $menuitem[0]->id; 
			$url = Route::_("index.php?Itemid=$itemid&view=project&format=json");
			return $url;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Helper function for generating the URL to a project page
	 * This is needed for the Tags functionality
	 */
	public static function getQwhelloworldRoute($id, $catid = 0, $language = 0)
	{
		// Create the link
		$link = 'index.php?option=com_qwhelloworld&view=project&id=' . $id;

		if ((int) $catid > 1)
		{
			$link .= '&catid=' . $catid;
		}

		if ($language && $language !== '*' && Multilanguage::isEnabled())
		{
			$link .= '&lang=' . $language;
		}

		return $link;
	}

	/**
	 * Helper function for generating the URL to a Category page
	 * This is needed for the Tags functionality
	 */
	public static function getCategoryRoute($catid, $language = 0)
	{
		if ($catid instanceof CategoryNode)
		{
			$id = $catid->id;
		}
		else
		{
			$id = (int) $catid;
		}

		if ($id < 1)
		{
			$link = '';
		}
		else
		{
			$link = 'index.php?option=com_qwhelloworld&view=category&id=' . $id;

			if ($language && $language !== '*' && Multilanguage::isEnabled())
			{
				$link .= '&lang=' . $language;
			}
		}

		return $link;
	}
}