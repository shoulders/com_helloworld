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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Controller\BaseController;

// Get an instance of the controller prefixed by Qwhelloworld
$controller = BaseController::getInstance('Qwhelloworld');

// Add CSS and JS to the <head> - This method allows overriding
HTMLHelper::stylesheet('com_'.$controller->getName().'/qwhelloworld.css', array(), true);
HTMLHelper::script('com_'.$controller->getName().'/qwhelloworld.js', false, true);

// Perform the Request task
$input = Factory::getApplication()->input;
$controller->execute($input->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();