<?php
/**
 * Class ColorPickerWidget
 *
 * @package application.widgets.colorpicker
 */
class ColorPicker2Widget extends CWidget
{
    public $selector = null;
    public $editor = null;
    private $_assetsUrl = null;

    public function init()
    {
        // this method is called by CController::beginWidget()
        if ($this->_assetsUrl === null)
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(
                (__DIR__).'/assets/'
            );

        Yii::app()->clientScript->registerScriptFile($this->_assetsUrl.'/js/spectrum.js');
        Yii::app()->clientScript->registerCssFile($this->_assetsUrl.'/css/spectrum.css');

    }

    public function run()
    {
        /*$script = <<<EOD
$('{$this->selector}').ColorPicker({
	onSubmit: function(hsb, hex, rgb, el) {
		//$(el).val(hex);
		$('#{$this->editor}').selection('insert', {text: '[color="#'+hex+'"]', mode: 'before'});
        $('#{$this->editor}').selection('insert', {text: '[/color]', mode: 'after'});
		$(el).ColorPickerHide();
	},
	onBeforeShow: function () {
		$(this).ColorPickerSetColor(this.value);
	}
})
.bind('keyup', function(){
	$(this).ColorPickerSetColor(this.value);
});
EOD;*/
        $script = <<<EOD
$('{$this->selector}').spectrum({
    showPaletteOnly: true,
    showPalette:true,
    palette: [
        ['#fe5926', '#77b500', '#00c6b1', '#7151fe', '#ff0000'],
        ['#ff8400', '#039000', '#00a4da', '#9921ff'],
        ['#bcad00', '#00d268', '#407efe', '#f002ff']
    ],
    change: function(color) {
        $('#{$this->editor}').selection('insert', {text: '[color="'+color.toHexString()+'"]', mode: 'before'});
        $('#{$this->editor}').selection('insert', {text: '[/color]', mode: 'after'});
    }
});
EOD;
        Yii::app()->clientScript->registerScript(uniqid(),  $script, CClientScript::POS_LOAD);
        //$this->render('index');
    }
}