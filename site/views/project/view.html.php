<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_qwhelloworld
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
 
/**
 * HTML View class for the Qwhelloworld Component
 *
 * @since  0.0.1
 */
class QwhelloworldViewProject extends HtmlView
{
	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		// Assign data to the view
        $this->item = $this->get('Item');
		$user = Factory::getUser();
		$app = Factory::getApplication();
		
		// for custom fields
		$dispatcher = JEventDispatcher::getInstance();
		PluginHelper::importPlugin('content');
		$item = $this->item;
		$item->text = null;

		$dispatcher->trigger('onContentPrepare', array ('com_qwhelloworld.project', &$item, &$item->params, null));

		$results = $dispatcher->trigger('onContentAfterTitle', array('com_qwhelloworld.project', &$item, &$item->params, null));
		$item->afterDisplayTitle = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentBeforeDisplay', array('com_qwhelloworld.project', &$item, &$item->params, null));
		$item->beforeDisplayContent = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentAfterDisplay', array('com_qwhelloworld.project', &$item, &$item->params, null));
		$item->afterDisplayContent = trim(implode("\n", $results));

        // Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			Log::add(implode('<br />', $errors), Log::WARNING, 'jerror');

			return false;
		}
		
		// Take action based on whether the user has access to see the record or not
		$loggedIn = $user->get('guest') != 1;
		if (!$this->item->canAccess)
		{
			if ($loggedIn)
			{
				$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
				$app->setHeader('status', 403, true);
				return;
			}
			else
			{
				$return = base64_encode(Uri::getInstance());
				$login_url_with_return = Route::_('index.php?option=com_users&return=' . $return, false);
				$app->enqueueMessage(Text::_('COM_QWHELLOWORLD_MUST_LOGIN'), 'notice');
				$app->redirect($login_url_with_return, 403);
			}
		}
		
        $this->addMap();
		
		$tagsHelper = new TagsHelper;
		$this->item->tags = $tagsHelper->getItemTags('com_qwhelloworld.project' , $this->item->id);
                
		$model = $this->getModel();
		$this->parentItem = $model->getItem($this->item->parent_id);
		$this->children = $model->getChildren($this->item->id);
		// getChildren includes the record itself (as well as the children) so remove this record
		unset($this->children[0]);
		
		// Display the view
		parent::display($tpl);
	}
    
	function addMap() 
	{
		$document = Factory::getDocument();

		// everything's dependent upon JQuery
		HTMLHelper::_('jquery.framework');

		// we need the Openlayers JS and CSS libraries
		$document->addScript("https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.4/ol.js");
		$document->addStyleSheet("https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.4/ol.css");

		// ... and our own JS and CSS
		$document->addScript(Uri::root() . "media/com_qwhelloworld/js/openstreetmap.js");
		$document->addStyleSheet(Uri::root() . "media/com_qwhelloworld/css/openstreetmap.css");

		// get the data to pass to our JS code
		$params = $this->get("mapParams");
		$document->addScriptOptions('params', $params);
	}
}