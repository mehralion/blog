<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 11.06.13
 * Time: 19:19
 * To change this template use File | Settings | File Templates.
 */

Yii::import('zii.widgets.CMenu');
class Menu extends CMenu
{
    private $actionParams = array();

    public function init()
    {
        $this->htmlOptions['id']=$this->getId();
        $route=$this->getController()->getRoute();
        $this->actionParams = $this->getController()->getActionParams();
        $this->items=$this->normalizeItems($this->items,$route,$hasActiveChild);
    }

    protected function isItemActive($item,$route)
    {
        if(isset($item['url']) && is_array($item['url']) && !strcasecmp(trim($item['url'][0],'/'),$route))
        {
            unset($item['url']['#']);
            if(count($item['url'])>1)
            {
                foreach(array_splice($item['url'],1) as $name=>$value)
                {
                    if(!isset($_GET[$name]) || $_GET[$name]!=$value)
                        return false;
                }
                return true;
            } elseif(empty($this->actionParams))
                return true;
        }
        return false;
    }

    protected function renderMenuItem($item)
    {
        if(isset($item['url']))
        {
            $label=$this->linkLabelWrapper===null ? $item['label'] : '<'.$this->linkLabelWrapper.'>'.$item['label'].'</'.$this->linkLabelWrapper.'>';
            return CHtml::link($label,$item['url'],isset($item['linkOptions']) ? $item['linkOptions'] : array());
        }
        else {
            $tag = !isset($item['linkLabelWrapper']) ? 'span' : $item['linkLabelWrapper'];
            return CHtml::tag($tag, isset($item['linkOptions']) ? $item['linkOptions'] : array(), $item['label']);
        }
    }
}