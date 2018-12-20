<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 14.06.13
 * Time: 22:18
 * To change this template use File | Settings | File Templates.
 */

Yii::import('zii.widgets.CBreadcrumbs');
class Breadcrumbs extends CBreadcrumbs
{
    public $htmlOptions=array('class'=>'breadcrumbs breadcrumb');
    public $separator = '<span class="divider">/</span>';
    public $inactiveLinkTemplate = '<span class="active">{label}</span>';

    public function run()
    {
        if(empty($this->links))
            return;

        echo CHtml::openTag($this->tagName,$this->htmlOptions)."\n";
        $links=array();
        if($this->homeLink===null)
            $links[]=CHtml::link(Yii::t('zii','Home'),Yii::app()->homeUrl);
        elseif($this->homeLink!==false)
            $links[]=$this->homeLink;

        if(null !== Yii::app()->userOwn->id)
            $links[] = CHtml::link(
                $this->encodeLabel ? CHtml::encode(Yii::app()->userOwn->login) : Yii::app()->userOwn->login,
                Yii::app()->createUrl('/user/profile/show', array('gameId' => Yii::app()->userOwn->game_id)));

        $last = end($this->links);
        foreach($this->links as $label=>$url)
        {
            if($last != $url || is_string($label) || is_array($url))
                $item = strtr($this->activeLinkTemplate,array(
                    '{url}'=>CHtml::normalizeUrl($url),
                    '{label}'=>$this->encodeLabel ? CHtml::encode($label) : $label,
                ));
            else
                $item = str_replace('{label}',$this->encodeLabel ? CHtml::encode($url) : $url,$this->inactiveLinkTemplate);

            if(!in_array($item, $links))
                $links[] = $item;
        }

        echo implode($this->separator,$links);
        echo CHtml::closeTag($this->tagName);
    }
}