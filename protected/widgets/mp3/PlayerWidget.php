<?php
Yii::import('zii.widgets.CPortlet');
/**
 * Class TagCloudWidget
 *
 *
<object type="application/x-shockwave-flash" data="player_mp3_maxi.swf" width="200" height="20">
    <param name="movie" value="player_mp3_maxi.swf" />
    <param name="FlashVars" value="mp3=test.mp3" />
</object>
 *
 *
 *
 * @package application.widgets.tagCloud
 */
class PlayerWidget extends CPortlet
{
    public $link;
    public $titleMp3;

    private $_assetsUrl;
    private $styles = 'audio49-1291.txt';
    private $player = 'uppod.swf';

    public function init()
    {
        parent::init();
        if ($this->_assetsUrl === null)
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(
                (__DIR__).'/assets/'
            );
    }

    public function getStyles()
    {
        if ($this->_assetsUrl === null)
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(
                (__DIR__).'/assets/'
            );
        return $this->_assetsUrl.'/styles/'.$this->styles;
    }

    public function getPlayer()
    {
        if ($this->_assetsUrl === null)
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(
                (__DIR__).'/assets/'
            );
        return $this->_assetsUrl.'/flash/'.$this->player;
    }

    protected function renderContent()
    {
        if(null === $this->link)
            return;

        $params =  array(
            CHtml::tag('param', array(
                'name' => 'bgcolor',
                'value' => '#ffffff'
            )),
            CHtml::tag('param', array(
                'name' => 'allowScriptAccess',
                'value' => 'always'
            )),
            CHtml::tag('param', array(
                'name' => 'movie',
                'value' => $this->_assetsUrl.'/flash/'.$this->player
            )),
            CHtml::tag('param', array(
                'name' => 'flashvars',
                'value' => 'st='.$this->_assetsUrl.'/styles/'.$this->styles.'&file='.$this->link.'&m=audio&comment='.$this->titleMp3
            )),
        );

        echo CHtml::tag('object', array(
            'type' => 'application/x-shockwave-flash',
            'data' => $this->_assetsUrl.'/flash/'.$this->player,
            'width' => 273,
            'height' => 25,
            'id' => uniqid()
        ), implode('', $params));
    }
}