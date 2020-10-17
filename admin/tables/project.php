<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_qwhelloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Access\Rules;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Table\Nested;
use Joomla\CMS\Table\Observer\Tags;
use Joomla\Registry\Registry;
use Joomla\CMS\Table\Table;

/**
 * Hello Table class
 *
 * @since  0.0.1
 */
class QwhelloworldTableProject extends Nested
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
	 */
	function __construct(&$db)
	{
		JObserverMapper::addObserverClassToClass('JTableObserverContenthistory', 'QwhelloworldTableProject', array('typeAlias' => 'com_qwhelloworld.project'));
		parent::__construct('#__com_qwhelloworld', 'id', $db);
		Tags::createObserver($this, array('typeAlias' => 'com_qwhelloworld.project'));
	}
	/**
	 * Overloaded bind function
	 *
	 * @param       array           named array
	 * @return      null|string     null is operation was satisfactory, otherwise returns an error
	 * @see JTable:bind
	 * @since 1.5
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params']))
		{
			// Convert the params field to a string.
			$parameter = new Registry;
			$parameter->loadArray($array['params']);
			$array['params'] = (string)$parameter;
		}
        
        if (isset($array['imageinfo']) && is_array($array['imageinfo']))
		{
			// Convert the imageinfo array to a string.
			$parameter = new Registry;
			$parameter->loadArray($array['imageinfo']);
			$array['image'] = (string)$parameter;
		}
        
        // Bind the rules.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new Rules($array['rules']);
			$this->setRules($rules);
		}
        
		if (isset($array['parent_id']))
		{
			if (!isset($array['id']) || $array['id'] == 0)
			{   // new record
				$this->setLocation($array['parent_id'], 'last-child');
			}
			elseif (isset($array['projectordering']))
			{
				// when saving a record load() is called before bind() so the table instance will have properties which are the existing field values
				if ($this->parent_id == $array['parent_id'])
				{
					// If first is chosen make the item the first child of the selected parent.
					if ($array['projectordering'] == -1)
					{
						$this->setLocation($array['parent_id'], 'first-child');
					}
					// If last is chosen make it the last child of the selected parent.
					elseif ($array['projectordering'] == -2)
					{
						$this->setLocation($array['parent_id'], 'last-child');
					}
					// Don't try to put an item after itself. All other ones put after the selected item.
					elseif ($array['projectordering'] && $this->id != $array['projectordering'])
					{
						$this->setLocation($array['projectordering'], 'after');
					}
					// Just leave it where it is if no change is made.
					elseif ($array['projectordering'] && $this->id == $array['projectordering'])
					{
						unset($array['projectordering']);
					}
				}
				// Set the new parent id if parent id not matched and put in last position
				else
				{
					$this->setLocation($array['parent_id'], 'last-child');
				}
			}
		}

		return parent::bind($array, $ignore);
	}
    
    /**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return	string
	 * @since	2.5
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_qwhelloworld.project.'.(int) $this->$k;
	}
	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return	string
	 * @since	2.5
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}
	/**
	 * Method to get the asset-parent-id of the item
	 *
	 * @return	int
	 */
	protected function _getAssetParentId(JTable $table = NULL, $id = NULL)
	{
		// We will retrieve the parent-asset from the Asset-table
		$assetParent = Table::getInstance('Asset');
		// Default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId();

		// Find the parent-asset
		if (($this->catid)&& !empty($this->catid))
		{
			// The item has a category as asset-parent
			$assetParent->loadByName('com_qwhelloworld.category.' . (int) $this->catid);
		}
		else
		{
			// The item has the component as asset-parent
			$assetParent->loadByName('com_qwhelloworld');
		}

		// Return the found asset-parent-id
		if ($assetParent->id)
		{
			$assetParentId=$assetParent->id;
		}
		return $assetParentId;
	}

	public function check()
	{
		$this->alias = trim($this->alias);
		if (empty($this->alias))
		{
			$this->alias = $this->title;
		}
		$this->alias = OutputFilter::stringURLSafe($this->alias);
		return true;
	}

	public function delete($pk = null, $children = false)
	{
		return parent::delete($pk, $children);
	}
}