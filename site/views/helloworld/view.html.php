<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
/**
 * HTML View class for the HelloWorld Component
 *
 * @since  0.0.1
 */
class HelloWorldViewHelloWorld extends JViewLegacy
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
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		
		// for custom fields
		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		$item = $this->item;
		$item->text = null;

		$dispatcher->trigger('onContentPrepare', array ('com_helloworld.helloworld', &$item, &$item->params, null));

		$results = $dispatcher->trigger('onContentAfterTitle', array('com_helloworld.helloworld', &$item, &$item->params, null));
		$item->afterDisplayTitle = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentBeforeDisplay', array('com_helloworld.helloworld', &$item, &$item->params, null));
		$item->beforeDisplayContent = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentAfterDisplay', array('com_helloworld.helloworld', &$item, &$item->params, null));
		$item->afterDisplayContent = trim(implode("\n", $results));

        // Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

			return false;
		}
		
		// Take action based on whether the user has access to see the record or not
		$loggedIn = $user->get('guest') != 1;
		if (!$this->item->canAccess)
		{
			if ($loggedIn)
			{
				$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
				$app->setHeader('status', 403, true);
				return;
			}
			else
			{
				$return = base64_encode(JUri::getInstance());
				$login_url_with_return = JRoute::_('index.php?option=com_users&return=' . $return, false);
				$app->enqueueMessage(JText::_('COM_HELLOWORLD_MUST_LOGIN'), 'notice');
				$app->redirect($login_url_with_return, 403);
			}
		}
		
        $this->addMap();
		
		$tagsHelper = new JHelperTags;
		$this->item->tags = $tagsHelper->getItemTags('com_helloworld.helloworld' , $this->item->id);
                
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
		$document = JFactory::getDocument();

		// everything's dependent upon JQuery
		JHtml::_('jquery.framework');

		// we need the Openlayers JS and CSS libraries
		$document->addScript("https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.4/ol.js");
		$document->addStyleSheet("https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.4/ol.css");

		// ... and our own JS and CSS
		$document->addScript(JURI::root() . "media/com_helloworld/js/openstreetmap.js");
		$document->addStyleSheet(JURI::root() . "media/com_helloworld/css/openstreetmap.css");

		// get the data to pass to our JS code
		$params = $this->get("mapParams");
		$document->addScriptOptions('params', $params);
	}
}