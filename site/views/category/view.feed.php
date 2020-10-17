<?php
/**
 * view file associated with the Syndicated Feed for a project category
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\CategoryFeedView;
use Joomla\Registry\Registry;

class QwhelloworldViewCategory extends CategoryFeedView
{
	// required so that the parent class can find the project content-type record containing the field mapping details
	protected $viewName = 'project';

	/**
	 * Function overriding the parent reconcileNames() method. 
	 * We use this to insert an html link to the project image into the description
	 * 
	 * The input parameter is the project item as extracted from the database, passed by reference
	 *
	 * The result of the method is that the project item passed as a parameter gets its description property changed
	 */
	protected function reconcileNames($item)
	{ 
		$description = '';
		
		if (!empty($item->image))
		{
			// Convert the JSON-encoded image info into an array
			$imageDetails = new Registry;
			$imageDetails->loadString($item->image, 'JSON');
			$src = $imageDetails->get('image','');
			if (!empty($src))
			{
				$description .= '<p><img src="' . $src . '" /></p>';
			}
		}
		$item->description =  $description . $item->description;
	}
}