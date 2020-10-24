<?php

defined('_JEXEC') or die;

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Component\Router\RouterInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Multilanguage;

class QwhelloworldRouter implements RouterInterface
{
		/**
	 * Preprocess the route for the com_qwhelloworld component
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  $query
	 *
	 * @since       3.6
	 * @deprecated  4.0
	 */
	public function preprocess($query)
	{
		static $currentLang = null;
		if (Associations::isEnabled())
		{
			$app  = Factory::getApplication();
			$sitemenu = $app->getMenu();
			$lang = $query['lang'];

			if (!isset($query['lang']) || ($query['lang'] == $currentLang))
			{
				return $query;
			}

			if (!isset($query['Itemid']))  // we're currently on /component type of URL
			{
				// use the home page for the URL's language
				$home = $sitemenu->getItems(array('language','home'), array($lang, true));
				if ($home)
				{
					$query['Itemid'] = $home[0]->id;
				}
				return $query;
			}

			$itemid = $query['Itemid'];

			// ensure the menuitem for Itemid has the correct language
			$thismenuitem = $sitemenu->getItem($itemid);
			$thismenuitemLang = $thismenuitem->language;
			if ($thismenuitemLang == $lang)
			{
				$currentLang = $thismenuitemLang;
			}
			if ($thismenuitemLang == $lang || $thismenuitemLang == '*')
			{
				return $query;
			}
            
			// if not, try to find an associated menuitem with the correct language
			$associations = Associations::getAssociations('com_menus', '#__menu', 'com_menus.item', $itemid, 'id', '', '');
			if (isset($associations[$lang]))
			{
				$query['Itemid'] = (int) $associations[$lang]->id;
				return $query;
			}
			else // use the home page for that language (if it's set)
			{
				$home = $sitemenu->getItems(array('language','home'), array($lang, true));
				if ($home)
				{
					$query['Itemid'] = $home[0]->id;
				}
			}
		}
		return $query;
	}

	/**
	 * Build the route for the com_qwhelloworld component
	 *
	 * @param   array  &$query     An array of URL arguments
	 *
	 * @return  $segments
	 *
	 * @since       3.6
	 * @deprecated  4.0
	 */
	public function build(&$query)
	{
		$segments = array();
		$app  = Factory::getApplication();

		// If there is no multilanguage or a view set, return
		if (!Multilanguage::isEnabled() || !isset($query['view']))
		{
			return $segments;
		}

		// Get the language string from the request
		//$lang = Factory::getLanguage()->getTag();
		        
		// If this build() call is not by a menu item, return
		if (!isset($query['Itemid']))
		{
			return $segments;
		}
		
		// Get the current/active menu item that called this build()		
		$sitemenu = $app->getMenu();
		$thisMenuitem = $sitemenu->getItem($query['Itemid']);
        
		// Check if the current/active menu item has a note on it 'Ajax'
		if ($thisMenuitem->note == "Ajax")
		{   
			// Check we've got the right parameters then set url segment = id : alias
			if ($query['view'] == "stream" && isset($query['id']))
			{
				// we'll support the passed id being in the form id:alias
				$segments[] = $query['id'];

				unset($query['id']);
				unset($query['catid']);
			}		
		}
		
		// Is category being requested	
		elseif (($query['view'] == "category") && isset($query['id']))
		{
			// set this part of the url to be of the form /subcat1/subcat2/...
			$pathSegments = $this->getCategorySegments($query['id']);
			if ($pathSegments)
			{
				$segments = $pathSegments;
				unset($query['id']);
			}
		}
		
		// Not sure what is being requested here
		elseif ($query['view'] == "stream" && isset($query['catid']) && isset($query['id']))
		{
			// set this part of the url to be of the form /subcat1/subcat2/.../hello-world 
			$pathSegments = $this->getCategorySegments($query['catid']);
			if ($pathSegments)
			{
				$segments = $pathSegments;
			}

			$segments[] = $query['id'];

			unset($query['id']);
			unset($query['catid']);
		}		

		unset($query['view']);
		return $segments;
	}


	/**
	 * Parse the segments of a URL.
	 * 
	 * This function does not assume that only the last segment is not a category, so we have to build and test from the root
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return array Routing variables
	 *
	 * @since       3.6
	 * @deprecated  4.0
	 */
	public function parse(&$segments)
	{
		$vars = array();
		$numSegments = count($segments);
		$categoryTree = array();	// This will store the category tree as we build        
		
		// https://docs.joomla.org/Categories_and_CategoryNodes_API_Guide
		// https://api.joomla.org/cms-3/classes/Joomla.CMS.Categories.Categories.html
		// https://api.joomla.org/cms-3/classes/Joomla.CMS.Categories.CategoryNode.html		
		// https://docs.joomla.org/J3.x:Supporting_SEF_URLs_in_your_component		
		
		/** Parsing out the categories **/
		
		// Get Categories instance
		$categories = Categories::getInstance('Qwupdateserver', array()); 
		
		// Set the root category node as the start category for the loop
		$currentCategory = $categories->get('root');		
			
		// Loop through the segments (aliases)
		for ($i=0; $i <= $numSegments; $i++)
		{
			// This segment is a category
			if ($currentCategory instanceof CategoryNode)
			{				
				// Add this category to our tree
				$categoryTree[$i] = $currentCategory->id;				
				
				// Does this category have any child categories
				if ($currentCategory->hasChildren())
				{
					// Get the immediate child categories
					$categoryChildren = $currentCategory->getChildren();

					// Compare the child categories `alias` to against the `segment` to find match (if any) and then set it to be the new category or null
					// This figures out the next child to pick so we can go down the next level in the category tree.
					if($childCategory = $this->match($categoryChildren, $segments[(int)$i]))
					{
						// There is a matched child category and this should now be made the new currentCategory
						$currentCategory = $childCategory;
						
						// Child category was found, restart loop
						continue;		
					}
					// No matching category alias, so no further category processing is required as this segment cannot be a category
					else
					{
						break;
					}
				}				
			}
		}
		
		/* Parsing out the items */
		
		$lastCategoryId = $currentCategory->id;
		//$penultimateCategory = count($categoryTree) >= 2 ? prev(end($categoryTree)) : null;
		$lastSegment = $segments[$numSegments - 1];
		$penultimateSegment = $numSegments >= 1 ? $numSegments - 2 : null;
		
		switch ($i)
		{
			// Category
			case $numSegments:
			{
				$vars['view'] = 'category';
				$vars['id'] = $lastCategoryId;				

				// The request is now built
				break;			
			}

			// Project
			case $numSegments - 1:
			{				
				// Set the view
				$vars['view'] = 'stream';
				$vars['id'] = 'project';
				
				// The request is now built
				break;
			}
			
			default:
			{
				// Invalid URL
				break;
			}
		}

		return $vars;
		
	}

	/*
	 * This function take a category id and finds the path from that category to the root of the category tree
	 * The path returned from getPath() is an associative array of key = category id, value = id:alias
	 * If no valid category is found from the passed-in category id then null is returned. 
	 */
     
	private function getCategorySegments($catid)
	{
		$categories = Categories::getInstance('Qwhelloworld', array());
		$categoryNode = $categories->get($catid);
		if ($categoryNode)
		{
			$path = $categoryNode->getPath();

			return $path;
		}
		else
		{
			return null;
		}
	}
	
	/*
	 * This function takes an array of categoryNode elements and a url segment
	 * It goes through the categoryNodes looking for the one whose id:alias matches the passed-in segment
	 *   and returns the matching categoryNode, or null if not found
	 */
	private function match($categoryNodes, $segment)
	{
		foreach ($categoryNodes as $categoryNode)
		{
			if ($segment == $categoryNode->id . ':' . $categoryNode->alias)
			{
				return $categoryNode;
			}
		}
		return null;
	}

	
}