<?php
Yii::import('bootstrap.widgets.TbTabs');
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 30.05.13
 * Time: 13:59
 * To change this template use File | Settings | File Templates.
 *
 * @package application.widgets.mtab
 */
class MTabWidget extends TbTabs
{
    protected function normalizeTabs($tabs, &$panes, &$i = 0)
    {
        $id = $this->getId();
        $items = array();

        foreach ($tabs as $tab)
        {
            $item = $tab;

            if (isset($item['visible']) && $item['visible'] === false)
                continue;

            if (!isset($item['itemOptions']))
                $item['itemOptions'] = array();

            if (!isset($item['url']))
                $item['linkOptions']['data-toggle'] = 'tab';

            if (isset($tab['items']))
                $item['items'] = $this->normalizeTabs($item['items'], $panes, $i);
            else
            {
                if (!isset($item['id']))
                    $item['id'] = $id.'_tab_'.($i + 1);

                if (!isset($item['url']))
                    $item['url'] = '#'.$item['id'];

                if (!isset($item['content']))
                    $item['content'] = '';

                if(isset($item['view']) && isset($item['data'])) {
                    ob_start();
                    $this->controller->renderPartial($item['view'], $item['data']);
                    $item['content'] = ob_get_clean();
                }

                $content = $item['content'];
                unset($item['content']);

                if (!isset($item['paneOptions']))
                    $item['paneOptions'] = array();

                $paneOptions = $item['paneOptions'];
                unset($item['paneOptions']);

                $paneOptions['id'] = $item['id'];

                $classes = array('tab-pane fade');

                if (isset($item['active']) && $item['active'])
                    $classes[] = 'active in';

                $classes = implode(' ', $classes);
                if (isset($paneOptions['class']))
                    $paneOptions['class'] .= ' '.$classes;
                else
                    $paneOptions['class'] = $classes;

                $panes[] = CHtml::tag('div', $paneOptions, $content);

                $i++; // increment the tab-index
            }

            $items[] = $item;
        }
        return $items;
    }
}