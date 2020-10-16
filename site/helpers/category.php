<?php
/**
 * Class definition for HelloworldCategories
 */

defined('_JEXEC') or die;

use Joomla\CMS\Categories\Categories;

class HelloworldCategories extends Categories
{

	public function __construct($options = array())
	{
		$options['table'] = '#__helloworld';
		$options['extension'] = 'com_helloworld';

		parent::__construct($options);
	}
}