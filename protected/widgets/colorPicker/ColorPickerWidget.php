<?php
/**
 * Class ColorPickerWidget
 *
 * @package application.widgets.colorpicker
 */
class ColorPickerWidget extends CWidget
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

        Yii::app()->clientScript->registerScriptFile($this->_assetsUrl.'/js/colorpicker.js');
        Yii::app()->clientScript->registerScriptFile($this->_assetsUrl.'/js/eye.js');
        Yii::app()->clientScript->registerScriptFile($this->_assetsUrl.'/js/layout.js');
        Yii::app()->clientScript->registerScriptFile($this->_assetsUrl.'/js/utils.js');
        Yii::app()->clientScript->registerCssFile($this->_assetsUrl.'/css/colorpicker.css');

    }

    public function run()
    {
        $script = <<<EOD
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
EOD;

        Yii::app()->clientScript->registerScript(uniqid(),  $script, CClientScript::POS_LOAD);
        //$this->render('index');
    }
}