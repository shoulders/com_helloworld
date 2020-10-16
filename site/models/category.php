<?php
/**
 * Model for displaying the helloworld messages in a given category
 */

defined('_JEXEC') or die;

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Router\Route;

class HelloworldModelCategory extends ListModel
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id',
				'greeting',
				'alias',
				'lft',
			);
		}

		parent::__construct($config);
	}
    
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState($ordering, $direction);
        
		$app = Factory::getApplication('site');
		$catid = $app->input->getInt('id');

		$this->setState('category.id', $catid);
	}
    
	protected function getListQuery()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$catid = $this->getState('category.id'); 
		$query->select('id, greeting, alias, catid, access, description, image')
			->from($db->quoteName('#__helloworld'))
			->where('catid = ' . $catid);

		if (Multilanguage::isEnabled())
		{
			$lang = Factory::getLanguage()->getTag();
			$query->where('language IN ("*","' . $lang . '")');
		}

		$orderCol	= $this->state->get('list.ordering', 'lft');
		$orderDirn 	= $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;	
	}

	public function getCategoryName()
	{
		$catid = $this->getState('category.id'); 
		$categories = Categories::getInstance('Helloworld', array('access' => false));
		$categoryNode = $categories->get($catid);   
		return $categoryNode->title; 
	}
    
	public function getSubcategories()
	{
		$catid = $this->getState('category.id'); 
		$categories = Categories::getInstance('Helloworld', array('access' => false));
		$categoryNode = $categories->get($catid);
		$subcats = $categoryNode->getChildren(); 
        
		$lang = Factory::getLanguage()->getTag();
		if (Multilanguage::isEnabled() && $lang)
		{
			$query_lang = "&lang={$lang}";
		}
		else
		{
			$query_lang = '';
		}
        
		foreach ($subcats as $subcat)
		{
			$subcat->url = Route::_("index.php?view=category&id=" . $subcat->id . $query_lang);
		}
		return $subcats;
	}

	public function getCategoryAccess()
	{
		$catid = $this->getState('category.id'); 
		$categories = Categories::getInstance('Helloworld', array('access' => false));
		$categoryNode = $categories->get($catid);   
		return $categoryNode->access; 
	}
	
	public function getItems()
	{
		$items = parent::getItems();
		$user = Factory::getUser();
		$loggedIn = $user->get('guest') != 1;

		if ($user->authorise('core.admin')) // ie superuser
		{
			return $items;
		}
		else
		{
			$userAccessLevels = $user->getAuthorisedViewLevels();
			$catAccess = $this->getCategoryAccess();
			
			if (!in_array($catAccess, $userAccessLevels))
			{  // the user hasn't access to the category
				if ($loggedIn)
				{	
					return array();
				}
				else
				{
					foreach ($items as $item)
					{
						$item->canAccess = false;
					}
					return $items;
				}
			}

			foreach ($items as $item) 
			{
				if (!in_array($item->access, $userAccessLevels))
				{
					if ($loggedIn)
					{
						unset($item);
					}
					else
					{
						$item->canAccess = false;
					}
				}
			}
		}
		return $items;
	}

	public function getCategory()
	{
		$categories = Categories::getInstance('Helloworld', array());
		$category = $categories->get($this->getState('category.id', 'root'));
		return $category;
	}
}