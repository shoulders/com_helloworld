<?php
/**
 * The Qwhelloworld helper file for Multilingual Associations
 */

defined('_JEXEC') or die;

use Joomla\CMS\Association\AssociationExtensionHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Table\Table;

Table::addIncludePath(__DIR__ . '/../tables');

class QwhelloworldAssociationsHelper extends AssociationExtensionHelper
{
	/**
	 * The extension name
	 */
	protected $extension = 'com_qwhelloworld';

	/**
	 * Array of item types which have associations
	 */
	protected $itemTypes = array('project', 'category');

	/**
	 * Has the extension association support
	 */
	protected $associationsSupport = true;

	/**
	 * Get the associated items for an item
	 *
	 * @param   string  $typeName  The item type, either 'project' or 'category'
	 * @param   int     $id        The id of item for which we need the associated items
	 *
	 */
	public function getAssociations($typeName, $id)
	{
		$type = $this->getType($typeName);

		$context    = $this->extension . '.item';
		$catidField = 'catid';

		if ($typeName === 'project')
		{
			$context    = 'com_qwhelloworld.item';
			$catidField = 'catid';
		}
        elseif ($typeName === 'category')
		{
			$context    = 'com_categories.item';
			$catidField = '';
		}
        else
        {
            return null;
        }

		// Get the associations.
		$associations = Associations::getAssociations(
			$this->extension,
			$type['tables']['a'],
			$context,
			$id,
			'id',
			'alias',
			$catidField
		);

		return $associations;
	}

	/**
	 * Get item information
	 *
	 * @param   string  $typeName  The item type
	 * @param   int     $id        The id of item for which we need the associated items
	 *
	 * @return  JTable object associated with the record id passed in
	 */
	public function getItem($typeName, $id)
	{
		if (empty($id))
		{
			return null;
		}

		$table = null;

		switch ($typeName)
		{
			case 'project':
				$table = Table::getInstance('Project', 'QwhelloworldTable');
				break;

			case 'category':
				$table = Table::getInstance('Category');
				break;
		}

		if (empty($table))
		{
			return null;
		}

		$table->load($id);

		return $table;
	}

	/**
	 * Get information about the type
	 *
	 * @param   string  $typeName  The item type
	 *
	 * @return  array  Array of item types
	 */
	public function getType($typeName = '')
	{
		$fields  = $this->getFieldsTemplate();
		$tables  = array();
		$joins   = array();
		$support = $this->getSupportTemplate();
		$title   = '';

		if (in_array($typeName, $this->itemTypes))
		{
			switch ($typeName)
			{
				case 'project':
					$fields['title'] = 'a.title';
                    $fields['ordering'] = '';
                    $fields['access'] = '';
                    $fields['state'] = 'a.published';
                    $fields['created_user_id'] = '';
                    $fields['checked_out'] = '';
                    $fields['checked_out_time'] = '';

					$support['state'] = true;
					$support['acl'] = false;
					$support['category'] = true;

					$tables = array(
						'a' => '#__com_qwhelloworld'
					);

					$title = 'project';
					break;

				case 'category':
					$fields['created_user_id'] = 'a.created_user_id';
					$fields['ordering'] = 'a.lft';
					$fields['level'] = 'a.level';
					$fields['catid'] = '';
					$fields['state'] = 'a.published';

					$support['state'] = true;
					$support['acl'] = true;
					$support['checkout'] = true;
					$support['level'] = true;

					$tables = array(
						'a' => '#__categories'
					);

					$title = 'category';
					break;
			}
		}

		return array(
			'fields'  => $fields,
			'support' => $support,
			'tables'  => $tables,
			'joins'   => $joins,
			'title'   => $title
		);
	}
}