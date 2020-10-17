<?php
/**
 * Class definition for QwhelloworldCategories
 */

defined('_JEXEC') or die;

use Joomla\CMS\Categories\Categories;

class QwhelloworldCategories extends Categories
{

	public function __construct($options = array())
	{
		$options['table'] = '#__com_qwhelloworld';
		$options['extension'] = 'com_qwhelloworld';

		parent::__construct($options);
	}
}