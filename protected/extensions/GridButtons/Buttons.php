<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Nick Nikitchenko
 * Skype: quietasice
 * E-mail: quietasice123@gmail.com
 * Date: 04.07.13
 * Time: 13:54
 * To change this template use File | Settings | File Templates.
 */
Yii::import('zii.widgets.grid.CButtonColumn');
class Buttons extends CButtonColumn
{

    public $labelExp = true;
    public $buttonsImg = array(
        'reset'
    );

    public function init()
    {
        $this->viewButtonImageUrl = Yii::app()->theme->baseUrl."/images/icons/".Yii::app()->params["icons"]["grid"]["view"];
        $this->updateButtonImageUrl = Yii::app()->theme->baseUrl."/images/icons/".Yii::app()->params["icons"]["grid"]["accept"];
        $this->deleteButtonImageUrl = Yii::app()->theme->baseUrl."/images/icons/".Yii::app()->params["icons"]["grid"]["delete"];
        $this->buttonsImg['reset'] = Yii::app()->theme->baseUrl."/images/icons/".Yii::app()->params["icons"]["grid"]["delete"];
        $this->buttonsImg['unsubscribe'] = Yii::app()->theme->baseUrl."/images/icons/".Yii::app()->params["icons"]["grid"]["delete"];

        return parent::init();
    }

    protected function renderButton($id,$button,$row,$data)
    {
        if(isset($this->buttonsImg[$id]))
            $button['imageUrl'] = $this->buttonsImg[$id];

        if (isset($button['visible']) && !$this->evaluateExpression($button['visible'],array('row'=>$row,'data'=>$data)))
            return;

        if($this->labelExp)
            $label=$this->evaluateExpression((isset($button['label']) ? $button['label'] : $id), array('data' => $data));
        else
            $label=isset($button['label']) ? $button['label'] : $id;
        $url=isset($button['url']) ? $this->evaluateExpression($button['url'],array('data'=>$data,'row'=>$row)) : '#';
        $options=isset($button['options']) ? $button['options'] : array();
        $options['rel'] = $data->id;
        if(!isset($options['title']))
            $options['title']=$label;
        if(isset($button['imageUrl']) && is_string($button['imageUrl']))
            echo CHtml::link(CHtml::image($button['imageUrl'],$label),$url,$options);
        else
            echo CHtml::link($label,$url,$options);
    }
}