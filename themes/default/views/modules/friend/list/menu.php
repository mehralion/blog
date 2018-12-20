<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 16:32
 * To change this template use File | Settings | File Templates.
 */
$label = 'Запросы ко мне';
if($warning === true)
    $label = 'Запросы ко мне [ <div style="display:inline-block;color:red;">!</div> ]';
$this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
    'stacked'=>false, // whether this is a stacked menu
    'encodeLabel' => false,
    'items'=>array(
        array('label'=>'Мои друзья', 'url' => array('/friend/list/friend', 'gameId' => Yii::app()->user->getGameId())),
        array('label'=>$label, 'url' => array('/friend/list/pending', 'gameId' => Yii::app()->user->getGameId())),
        array('label'=>'Мои запросы', 'url' => array('/friend/list/own', 'gameId' => Yii::app()->user->getGameId())),
    ),
));