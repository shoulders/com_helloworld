<?php
/**
 * Helper file for Project Associations (on the site part)
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Multilanguage;

JLoader::register('CategoryHelperAssociation', JPATH_ADMINISTRATOR . '/components/com_categories/helpers/association.php');

/**
 * Qwhelloworld Component Association Helper
 *
 */
abstract class QwhelloworldHelperAssociation extends CategoryHelperAssociation
{
	/**
	 * Method to get the associations for a given item
	 *
	 * @param   integer  $id    Id of the item (project id or catid, depending on view)
	 * @param   string   $view  Name of the view ('project' or 'category')
	 *
	 * @return  array   Array of associations for the item
	 */
	public static function getAssociations($id = 0, $view = null)
	{
		$input = Factory::getApplication()->input;
		$view = $view === null ? $input->get('view') : $view;
		$id = empty($id) ? $input->getInt('id') : $id;

		if ($view === 'project')
		{
			if ($id)
			{
				$associations = Associations::getAssociations('com_qwhelloworld', '#__com_qwhelloworld_projects', 'com_qwhelloworld.item', $id);

				$return = array();

				foreach ($associations as $tag => $item)
				{
					$link = 'index.php?option=com_qwhelloworld&view=project&id=' . $item->id . '&catid=' . $item->catid;
					if ($item->language && $item->language !== '*' && Multilanguage::isEnabled())
					{
						$link .= '&lang=' . $item->language;
					}
					$return[$tag] = $link;
				}

				return $return;
			}
		}

		if ($view === 'category' || $view === 'categories')
		{
			return self::getCategoryAssociations($id, 'com_qwhelloworld');
		}

		return array();
	}
}