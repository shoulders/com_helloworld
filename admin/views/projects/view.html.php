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
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Projects View
 *
 * @since  0.0.1
 */
class QwhelloworldViewProjects extends HtmlView
{
        /**
         * Display the Projects view
         *
         * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
         *
         * @return  void
         */
        function display($tpl = null)
        {
                // Get application
                $app = Factory::getApplication();

                // Get data from the model
                $this->items                    = $this->get('Items');
                $this->pagination               = $this->get('Pagination');
                $this->state                    = $this->get('State');
                $this->filterForm       = $this->get('FilterForm');
                $this->activeFilters    = $this->get('ActiveFilters');
        
                // What Access Permissions does this user have? What can (s)he do?
                $this->canDo = ContentHelper::getActions('com_qwhelloworld');

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));

                        return false;
                }
        
                // Set the sidebar submenu and toolbar, but not on the modal window
                if ($this->getLayout() !== 'modal')
                {
                        QwhelloworldHelper::addSubmenu('projects');
                        $this->addToolBar();
                }
                else
                {
                        // If it's being displayed to select a record as an association, then forcedLanguage is set
                        if ($forcedLanguage = $app->input->get('forcedLanguage', '', 'CMD'))
                        {
                                // Transform the language selector filter into an hidden field, so it can't be set
                                $languageXml = new SimpleXMLElement('<field name="language" type="hidden" default="' . $forcedLanguage . '" />');
                                $this->filterForm->setField($languageXml, 'filter', true);

                                // Also, unset the active language filter so the search tools is not open by default with this filter.
                                unset($this->activeFilters['language']);
                        }
                }

                // Prepare a mapping from parent id to the ids of its children
                $this->ordering = array();
                foreach ($this->items as $item)
                {
                        $this->ordering[$item->parent_id][] = $item->id;
                }

                // Display the template
                parent::display($tpl);

                // Set the document
                $this->setDocument();
        }

        /**
         * Add the page title and toolbar.
         *
         * @return  void
         *
         * @since   1.6
         */
        protected function addToolBar()
        {
                $title = Text::_('COM_QWHELLOWORLD_MANAGER_PROJECTS');

                $bar = Toolbar::getInstance('toolbar');

                if ($this->pagination->total)
                {
                        $title .= "<span style='font-size: 0.5em; vertical-align: middle;'>(" . $this->pagination->total . ")</span>";
                }

                ToolbarHelper::title($title, 'project');//ToolbarHelper::title($title, 'helloworld');
				
                if ($this->canDo->get('core.create')) 
                {
                        ToolbarHelper::addNew('project.add', 'JTOOLBAR_NEW');
                }
                if ($this->canDo->get('core.edit')) 
                {
                        ToolbarHelper::editList('project.edit', 'JTOOLBAR_EDIT');
                }
                if ($this->canDo->get('core.delete')) 
                {
                        ToolbarHelper::deleteList('', 'projects.delete', 'JTOOLBAR_DELETE');
                }
                if ($this->canDo->get('core.edit') || Factory::getUser()->authorise('core.manage', 'com_checkin'))
                {
                        ToolbarHelper::checkin('projects.checkin');
                }
                // Add a batch button
                if ($this->canDo->get('core.create') && $this->canDo->get('core.edit')
                        && $this->canDo->get('core.edit.state'))
                {
                        // we use a standard Joomla layout to get the html for the batch button
                        $layout = new FileLayout('joomla.toolbar.batch');
                        $batchButtonHtml = $layout->render(array('title' => Text::_('JTOOLBAR_BATCH')));
                        $bar->appendButton('Custom', $batchButtonHtml, 'batch');
                }
                if ($this->canDo->get('core.admin')) 
                {
                        ToolbarHelper::divider();
                        ToolbarHelper::preferences('com_qwhelloworld');
                }
        }
        /**
         * Method to set up the document properties
         *
         * @return void
         */
        protected function setDocument() 
        {
                $document = Factory::getDocument();
                $document->setTitle(Text::_('COM_QWHELLOWORLD_ADMINISTRATION'));
        }
}