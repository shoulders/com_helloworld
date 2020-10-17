<?php
/**
 * Class for displaying the Ordering field in the project edit layout
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Language\Text;

FormHelper::loadFieldClass('list');

class JFormFieldProjectOrdering extends JFormFieldList
{
	protected $type = 'projectordering';

	/**
	 * Method to return the options for ordering the project record
	 * This is the list of siblings the record's siblings - ie those records with the same parent.
	 * The method requires that parent id be set.
	 */
	protected function getOptions()
	{
		$options = array();

		// Get the parent
		$parent_id = $this->form->getValue('parent_id', 0);

		if (empty($parent_id))
		{
			return false;
		}

		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id AS value, a.title AS text')
			->from('#__com_qwhelloworld AS a')
			->where('a.parent_id =' . (int) $parent_id);

		$query->order('a.lft ASC');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		$options = array_merge(
			array(array('value' => '-1', 'text' => Text::_('COM_QWHELLOWORLD_ITEM_FIELD_ORDERING_VALUE_FIRST'))),
			$options,
			array(array('value' => '-2', 'text' => Text::_('COM_QWHELLOWORLD_ITEM_FIELD_ORDERING_VALUE_LAST')))
		);

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * This method returns the input element except if a new record is being created, in which case a text string is output
	 */
	protected function getInput()
	{
		if ($this->form->getValue('id', 0) == 0)
		{
			return '<span class="readonly">' . Text::_('COM_QWHELLOWORLD_ITEM_FIELD_ORDERING_TEXT') . '</span>';
		}
		else
		{
			return parent::getInput();
		}
	}
}