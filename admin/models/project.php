<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_qwhelloworld
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\UCM\UCMType;
use Joomla\Registry\Registry;

/**
 * Project Model
 *
 * @since  0.0.1
 */
class QwhelloworldModelProject extends AdminModel
{
    // AdminModel needs to know this for storing the associations 
	protected $associationsContext = 'com_qwhelloworld.item';
    
	// Contenthistory needs to know this for restoring previous versions
	public $typeAlias = 'com_qwhelloworld.project';
	
	// batch processes supported by Qwhelloworld (over and above the standard batch processes)
	protected $project_batch_commands = array(
		'position' => 'batchPosition',
		);

	/**
	 * Method overriding batch in AdminModel so that we can include the additional batch processes
	 * which the Qwhelloworld component supports.
	 */
	public function batch($commands, $pks, $contexts)
	{
		$this->batch_commands = array_merge($this->batch_commands, $this->project_batch_commands);
		return parent::batch($commands, $pks, $contexts);
	}
	
	/**
	 * Method implementing the batch setting of lat/long values
	 */
	protected function batchPosition($value, $pks, $contexts)
	{

		$app = Factory::getApplication();
		$app->enqueueMessage("In batchPosition");

		if (isset($value['setposition']) && ($value['setposition'] === 'changePosition'))
		{
			if (empty($this->batchSet))
			{
				// Set some needed variables.
				$this->user = Factory::getUser();
				$this->table = $this->getTable();
				$this->tableClassName = get_class($this->table);
				$this->contentType = new UCMType;
				$this->type = $this->contentType->getTypeByTable($this->tableClassName);
			}

			foreach ($pks as $pk)
			{
				if ($this->user->authorise('core.edit', $contexts[$pk]))
				{
					$this->table->reset();
					$this->table->load($pk);
					if (isset($value['latitude']))
					{
						$latitude = floatval($value['latitude']);
						if ($latitude <= 90 && $latitude >= -90)
						{
							$this->table->latitude = $latitude;
						}
					}
					if (isset($value['longitude']))
					{
						$longitude = floatval($value['longitude']);
						if ($longitude <= 180 && $longitude >= -180)
						{
							$this->table->longitude = $longitude;
						}
					}
					if (!$this->table->store())
					{
						$this->setError($this->table->getError());

						return false;
					}
				}
				else
				{
					$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));

					return false;
				}
			}
		}
		return true;
	}
	
	/**
	 * Method to override generateTitle() because the Qwhelloworld component uses 'greeting' as the title field
	 * 
	 * Reverted to using title field
	 * 
	 * @depreceated
	 * 
	 */
	/*public function generateTitle($categoryId, $table)
	{
		// Alter the title & alias
		$data = $this->generateNewTitle($categoryId, $table->alias, $table->greeting);
		$table->greeting = $data['0'];
		$table->alias = $data['1'];
	}*/

	/**
	 * Method to override getItem to allow us to convert the JSON-encoded image information
	 * in the database record into an array for subsequent prefilling of the edit form
	 * We also use this method to prefill the tags and associations
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		if ($item AND property_exists($item, 'image'))
		{
			$registry = new Registry($item->image);
			$item->imageinfo = $registry->toArray();
		}

		if (!empty($item->id))
		{
			$tagsHelper = new TagsHelper;
			$item->tags = $tagsHelper->getTagIds($item->id, 'com_qwhelloworld.project');
		}
        
		// Load associated items
		if (Associations::isEnabled())
		{
			$item->associations = array();

			if ($item->id != null)
			{
				$associations = Associations::getAssociations('com_qwhelloworld', '#__com_qwhelloworld_projects', 'com_qwhelloworld.item', (int)$item->id);

				foreach ($associations as $tag => $association)
				{
					$item->associations[$tag] = $association->id;
				}
			}
		}
		return $item; 
	}
	
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Table  A Table object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Project', $prefix = 'QwhelloworldTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed    A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_qwhelloworld.project',
			'project',
			array(
				'control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}
    
    /**
	 * Method to preprocess the form to add the association fields dynamically
	 *
	 * @return     none
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'project')
	{
		// Association content items
		if (Associations::isEnabled())
		{
			$languages = LanguageHelper::getContentLanguages(false, true, null, 'ordering', 'asc');

			if (count($languages) > 1)
			{
				$addform = new SimpleXMLElement('<form />');
				$fields = $addform->addChild('fields');
				$fields->addAttribute('name', 'associations');
				$fieldset = $fields->addChild('fieldset');
				$fieldset->addAttribute('name', 'item_associations');

				foreach ($languages as $language)
				{
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $language->lang_code);
					$field->addAttribute('type', 'modal_project');
					$field->addAttribute('language', $language->lang_code);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');
				}

				$form->load($addform, false);
			}
		}
		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to get the script to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getScript() 
	{
		return 'administrator/components/com_qwhelloworld/models/forms/project.js';
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState(
			'com_qwhelloworld.edit.project.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}
	/**
	 * Method to override the AdminModel save() function to handle Save as Copy correctly
	 *
	 * @param   The project record data submitted from the form.
	 *
	 * @return  parent::save() return value
	 */
	public function save($data)
	{
		$input = Factory::getApplication()->input;

		JLoader::register('CategoriesHelper', JPATH_ADMINISTRATOR . '/components/com_categories/helpers/categories.php');

		// Validate the category id
		// validateCategoryId() returns 0 if the catid can't be found
		if ((int) $data['catid'] > 0)
		{
			$data['catid'] = CategoriesHelper::validateCategoryId($data['catid'], 'com_qwhelloworld');
		}

		// Alter the title and alias for save as copy
		if ($input->get('task') == 'save2copy')
		{
			$origTable = clone $this->getTable();
			$origTable->load($input->getInt('id'));

			if ($data['title'] == $origTable->title)
			{
				list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
				$data['title'] = $title;
				$data['alias'] = $alias;
			}
			else
			{
				if ($data['alias'] == $origTable->alias)
				{
					$data['alias'] = '';
				}
			}
			// standard Joomla practice is to set the new record as unpublished
			$data['published'] = 0;
		}

		$result = parent::save($data);
		if ($result)
		{
			$this->getTable()->rebuild(1);
		}
		return $result;
	}
	/**
	 * Method to check if it's OK to delete a message. Overrides AdminModel::canDelete
	 */
	protected function canDelete($record)
	{
		if( !empty( $record->id ) )
		{
			return Factory::getUser()->authorise( "core.delete", "com_qwhelloworld.project." . $record->id );
		}
	}
	/**
	 * Prepare a project record for saving in the database
	 */
	protected function prepareTable($table)
	{
	}
	
	/**
	 * Save the record reordering after a record is dragged to a new position in the projects view
	 */
	public function saveorder($idArray = null, $lft_array = null)
	{
		// Get an instance of the table object.
		$table = $this->getTable();

		if (!$table->saveorder($idArray, $lft_array))
		{
			$this->setError($table->getError());

			return false;
		}

		return true;
	}

	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_qwhelloworld');
	}
}