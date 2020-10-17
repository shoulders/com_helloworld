<?php
/**
 * Layout file for the admin modal display of project records
 *
 */

defined('_JEXEC') or die('Restricted Access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.core');
HTMLHelper::_('script', 'com_qwhelloworld/admin-projects-modal.js', array('version' => 'auto', 'relative' => true));

$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape($this->state->get('list.direction'));

$app = Factory::getApplication();
$function  = $app->input->getCmd('function', 'jSelectProject');
$onclick   = $this->escape($function);
?>
<div class="container-popup">
    
<form action="<?php echo Route::_('index.php?option=com_qwhelloworld&view=projects&layout=modal&tmpl=component&function=' . $function . '&' . Session::getFormToken() . '=1'); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">

	<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
    
    <div class="clearfix"></div>

        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th width="3%"><?php echo Text::_('COM_QWHELLOWORLD_NUM'); ?></th>
                <th width="15%">
                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_QWHELLOWORLD_PROJECTS_NAME', 'title', $listDirn, $listOrder); ?>
                </th>
                <th width="15%">
                    <?php echo Text::_('COM_QWHELLOWORLD_PROJECTS_POSITION'); ?>
                </th>
                <th width="15%">
                    <?php echo Text::_('COM_QWHELLOWORLD_PROJECTS_IMAGE'); ?>
                </th>
                <th width="15%">
                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_QWHELLOWORLD_AUTHOR', 'author', $listDirn, $listOrder); ?>
                </th>
                <th width="15%">
                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_QWHELLOWORLD_LANGUAGE', 'language', $listDirn, $listOrder); ?>
                </th>
                <th width="15%">
                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_QWHELLOWORLD_CREATED_DATE', 'created', $listDirn, $listOrder); ?>
                    </th>
                <th width="5%">
                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_QWHELLOWORLD_PUBLISHED', 'published', $listDirn, $listOrder); ?>
                </th>
                <th width="2%">
                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_QWHELLOWORLD_ID', 'id', $listDirn, $listOrder); ?>
                </th>
            </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="5">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
            <tbody>
                <?php if (!empty($this->items)) : ?>
                    <?php foreach ($this->items as $i => $row) :
                        $row->image = new Registry;
                        $row->image->loadString($row->imageInfo);
                        if ($row->language && Multilanguage::isEnabled())
                        {
                            $tag = strlen($row->language);
                            if ($tag == 5)
                            {
                                $lang = substr($row->language, 0, 2);
                            }
                            elseif ($tag == 6)
                            {
                                $lang = substr($row->language, 0, 3);
                            }
                            else {
                                $lang = '';
                            }
                        }
                        elseif (!Multilanguage::isEnabled())
                        {
                            $lang = '';
                        }
                    ?>
                        <tr>
                            <td><?php echo $this->pagination->getRowOffset($i); ?></td>
                            <td>
                                <?php 
                                $link = 'index.php?option=com_qwhelloworld&view=project&id=' . $row->id;
                                $attribs = 'data-function="' . $this->escape($onclick) . '"'
								. ' data-id="' . $row->id . '"'
								. ' data-title="' . $this->escape(addslashes($row->title)) . '"'
								. ' data-uri="' . $link . '"'
								. ' data-language="' . $this->escape($lang) . '"'
                                ;
                                ?>
                                <a class="select-link" href="javascript:void(0)" <?php echo $attribs; ?>>
                                    <?php echo $this->escape($row->title); ?>
                                </a>
                                <span class="small break-word">
                                	<?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($row->alias)); ?>
                                </span>
                                <div class="small">
									<?php echo Text::_('JCATEGORY') . ': ' . $this->escape($row->category_title); ?>
								</div>
                            </td>
                            <td align="center">
                                <?php echo "[" . $row->latitude . ", " . $row->longitude . "]"; ?>
                            </td>
                            <td align="center">
                                <?php
                                    $caption = $row->image->get('caption') ? : '' ;
                                    $src = Uri::root() . ($row->image->get('image') ? : '' );
                                    $html = '<p class="hasTooltip" style="display: inline-block" data-html="true" data-toggle="tooltip" data-placement="right" title="<img width=\'100px\' height=\'100px\' src=\'%s\'>">%s</p>';
                                    echo sprintf($html, $src, $caption);  ?>
                            </td>
                            <td align="center">
                                <?php echo $row->author; ?>
                            </td>
                            <td align="center">
                                <?php echo LayoutHelper::render('joomla.content.language', $row); ?>
                            </td>
                            <td align="center">
                                <?php echo substr($row->created, 0, 10); ?>
                            </td>
                            <td align="center">
                                <?php echo HTMLHelper::_('jgrid.published', $row->published, $i, 'projects.', true, 'cb'); ?>
                            </td>
                            <td align="center">
                                <?php echo $row->id; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="forcedLanguage" value="<?php echo $app->input->get('forcedLanguage', '', 'CMD'); ?>" />
        <?php echo HTMLHelper::_('form.token'); ?>
</form>
</div>