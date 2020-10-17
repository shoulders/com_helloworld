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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Session\Session;

/**
 * Hello World Component Controller
 *
 * @since  0.0.1
 */
class QwhelloworldController extends BaseController
{
	public function display($cachable = false, $urlparams = array())
	{
		$viewName = $this->input->get('view', '');
		$cachable = true;
		if ($viewName == 'form' || Factory::getUser()->get('id'))
		{
			$cachable = false;
		}
		
		$safeurlparams = array(
			'id'               => 'ARRAY',
			'catid'            => 'ARRAY',
			'list'             => 'ARRAY',
			'limitstart'       => 'UINT',
			'Itemid'           => 'INT',
			'view'             => 'CMD',
			'lang'             => 'CMD',
		);
		
		parent::display($cachable, $safeurlparams);
	}

	public function mapsearch()
	{
//		if (!Session::checkToken('get')) 
//		{
//			echo new JsonResponse(null, Text::_('JINVALID_TOKEN'), true);
//		}
//		else 
//		{
			parent::display();
//		}
	}
}