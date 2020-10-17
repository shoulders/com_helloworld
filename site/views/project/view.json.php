<?php
/**
 * View file for responding to Ajax request for performing Search Here on the map
 * 
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Response\JsonResponse;
 
class QwhelloworldViewProject extends HtmlView
{
	/**
	 * This display function returns in json format the Project titles
	 *   found within the latitude and longitude boundaries of the map.
	 * These bounds are provided in the parameters
	 *   minlat, minlng, maxlat, maxlng
	 */

	function display($tpl = null)
	{
		$input = Factory::getApplication()->input;
		$mapbounds = $input->get('mapBounds', array(), 'ARRAY');
		$model = $this->getModel();
		if ($mapbounds)
		{
			$records = $model->getMapSearchResults($mapbounds);
			if ($records) 
			{
				echo new JsonResponse($records);
			}
			else
			{
				echo new JsonResponse(null, Text::_('COM_QWHELLOWORLD_ERROR_NO_RECORDS'), true);
			}
		}
		else 
		{
			$records = array();
			echo new JsonResponse(null, Text::_('COM_QWHELLOWORLD_ERROR_NO_MAP_BOUNDS'), true);
		}
	}
}