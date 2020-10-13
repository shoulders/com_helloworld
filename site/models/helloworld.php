<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JLoader::register('HelloworldHelperRoute', JPATH_ROOT . '/components/com_helloworld/helpers/route.php');

/**
 * HelloWorld Model
 *
 * @since  0.0.1
 */
class HelloWorldModelHelloWorld extends JModelItem
{
	/**
	 * @var object item
	 */
	protected $item;

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	2.5
	 */
	protected function populateState()
	{
		// Get the message id
		$jinput = JFactory::getApplication()->input;
		$id     = $jinput->get('id', 1, 'INT');
		$this->setState('message.id', $id);

		// Load the parameters.
		$this->setState('params', JFactory::getApplication()->getParams());
		parent::populateState();
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'HelloWorld', $prefix = 'HelloWorldTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Get the message
	 * @return object The message to be displayed to the user
	 */
	public function getItem($id = null)
	{
		if (!isset($this->item) || !is_null($id)) 
		{
			$id    = is_null($id) ? $this->getState('message.id') : $id;
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('h.greeting, h.params, h.image as image, c.title as category, c.access as catAccess, 
						h.latitude as latitude, h.longitude as longitude, h.access as access,
						h.id as id, h.alias as alias, h.catid as catid, h.parent_id as parent_id, h.level as level, h.description as description')
				  ->from('#__helloworld as h')
				  ->leftJoin('#__categories as c ON h.catid=c.id')
				  ->where('h.id=' . (int)$id);

			if (JLanguageMultilang::isEnabled())
			{
				$lang = JFactory::getLanguage()->getTag();
				$query->where('h.language IN ("*","' . $lang . '")');
			}

			$db->setQuery((string)$query);
		
			if ($this->item = $db->loadObject()) 
			{
				// Load the JSON string
				$params = new JRegistry;
				$params->loadString($this->item->params, 'JSON');
				$this->item->params = $params;

				// Merge global params with item params
				$params = clone $this->getState('params');
				$params->merge($this->item->params);
				$this->item->params = $params;

				// Convert the JSON-encoded image info into an array
				$image = new JRegistry;
				$image->loadString($this->item->image, 'JSON');
				$this->item->imageDetails = $image;

				// Check if the user can access this record (and category)
				$user = JFactory::getUser();
				$userAccessLevels = $user->getAuthorisedViewLevels();
				if ($user->authorise('core.admin')) // ie superuser
				{
					$this->item->canAccess = true;
				}
				else
				{
					if ($this->item->catid == 0)
					{
						$this->item->canAccess = in_array($this->item->access, $userAccessLevels);
					}
					else
					{
						$this->item->canAccess = in_array($this->item->access, $userAccessLevels) && in_array($this->item->catAccess, $userAccessLevels);
					}
				}
			}
			else
			{
				throw new Exception('Helloworld id not found', 404);
			}
		}
		return $this->item;
	}

	public function getMapParams()
	{
		if ($this->item) 
		{
			$url = HelloworldHelperRoute::getAjaxURL();
			$this->mapParams = array(
				'latitude' => $this->item->latitude,
				'longitude' => $this->item->longitude,
				'zoom' => 10,
				'greeting' => $this->item->greeting,
				'ajaxurl' => $url
			);
			return $this->mapParams; 
		}
		else
		{
			throw new Exception('No helloworld details available for map', 500);
		}
	}

	public function getMapSearchResults($mapbounds)
	{
		if (JFactory::getConfig()->get('caching') >= 1)
		{
			// Build a cache ID based on the conditions for the SQL where clause
			$groups = implode(',', JFactory::getUser()->getAuthorisedViewLevels());
			$cacheId = $groups . '.' . $mapbounds['minlat'] . '.' . $mapbounds['maxlat'] . '.' . 
										$mapbounds['minlng'] . '.' . $mapbounds['maxlng'];
			if (JLanguageMultilang::isEnabled())
			{
				$lang = JFactory::getLanguage()->getTag();
				$cacheId .= $lang;
			}
			$cache = JFactory::getCache('com_helloworld', 'callback');
			$results = $cache->get(array($this, '_getMapSearchResults'), array($mapbounds), md5($cacheId), false);
			return $results;
		}
		else
		{
			return $this->_getMapSearchResults($mapbounds);
		}
	}

	public function _getMapSearchResults($mapbounds)
	{
		try 
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('h.id, h.alias, h.catid, h.greeting, h.latitude, h.longitude, h.access')
			   ->from('#__helloworld as h')
			   ->where('h.latitude > ' . $mapbounds['minlat'] . 
				' AND h.latitude < ' . $mapbounds['maxlat'] .
				' AND h.longitude > ' . $mapbounds['minlng'] .
				' AND h.longitude < ' . $mapbounds['maxlng']);

			if (JLanguageMultilang::isEnabled())
			{
				$lang = JFactory::getLanguage()->getTag();
				$query->where('h.language IN ("*","' . $lang . '")');
			}

			$user = JFactory::getUser();
			$loggedIn = $user->get('guest') != 1;
			if ($loggedIn && !$user->authorise('core.admin'))
			{
				$userAccessLevels = $user->getAuthorisedViewLevels();
				$query->where('h.access IN (' . implode(",", $userAccessLevels) . ')');
				$query->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON c.id = h.catid');
				$query->where('(c.access IN (' . implode(",", $userAccessLevels) . ') OR h.catid = 0)');
			}

			$db->setQuery($query);
			$results = $db->loadObjectList(); 
		}
		catch (Exception $e)
		{
			$msg = $e->getMessage();
			JFactory::getApplication()->enqueueMessage($msg, 'error'); 
			$results = null;
		}

		if (JLanguageMultilang::isEnabled())
		{
			$query_lang = "&lang={$lang}";
		}
		else
		{
			$query_lang = "";
		}

		for ($i = 0; $i < count($results); $i++) 
		{
			$results[$i]->url = JRoute::_('index.php?option=com_helloworld&view=helloworld&id=' . $results[$i]->id . 
				":" . $results[$i]->alias . '&catid=' . $results[$i]->catid . $query_lang);
		}

		return $results; 
	}

	public function getChildren($id)
	{
		$table = $this->getTable();
		$children = $table->getTree($id);
		return $children;
	}
}