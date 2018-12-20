<?php
/**
 * Class EditorWidget
 *
 * @package application.widgets.editor
 */
class EditorWidget extends CWidget
{
    public $name = null;
    public $model = null;
    public $attributeName = null;
    public $url = null;
    public $htmlOptions = array();
    /** @var TbActiveForm  */
    public $form = null;
    public $button = true;
    private $_assetsUrl = null;
    public $uniq = null;
    public $editor = null;

    private $_smiles = array();

    public function init()
    {
        /** @var Smiles[] $model */
        $model = Smiles::model()->cache(1000)->findAll();
        foreach($model as $item)
            $this->_smiles[] = $item->smile;

        $this->uniq = $uniqBlock = uniqid();
        $this->editor = uniqid();
        $htmlOptions = array(
            'id' =>$this->editor
        );
        $this->htmlOptions = CMap::mergeArray($htmlOptions, $this->htmlOptions);
        if ($this->_assetsUrl === null)
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(
                (__DIR__).'/assets/'
            );

        $smiles = CJavaScript::encode($this->_smiles);
        $vars = <<<OED
        var editorBlockId = '{$uniqBlock}';
        var smiles = {$smiles};
        var editorID = '{$this->editor}';
        var editor = new MyEditor();
OED;
        $events = <<<OED
        $(document.body).on('click', '#{$uniqBlock} li#table', function(){buildTable();});
        $(document.body).on('click', '#{$uniqBlock} li#b', function(){editor.B(this, '#{$this->editor}');});
        $(document.body).on('click', '#{$uniqBlock} li#i', function(){editor.I(this, '#{$this->editor}');});
        $(document.body).on('click', '#{$uniqBlock} li#u', function(){editor.U(this, '#{$this->editor}');});
        $(document.body).on('click', '#{$uniqBlock} li#hide', function(){editor.Hide(this, '#{$this->editor}');});
        $(document.body).on('click', '#{$uniqBlock} li#info', function(){editor.Info(this, '#{$this->editor}');});
        $(document.body).on('click', '#{$uniqBlock} li#link', function(){editor.Link(this, '#{$this->editor}');});
        $(document.body).on('click', '#{$uniqBlock} li#image', function(){editor.Image(this, '#{$this->editor}');});
        $(document.body).on('click', '#{$uniqBlock} li#audio', function(){editor.Audio(this, '#{$this->editor}');});
        $(document.body).on('click', '#{$uniqBlock} li#smile', function(){buildSmiles();});
        $(document.body).on('click', '#{$uniqBlock} li#video', function(){editor.Youtube(this, '#{$this->editor}');});
        $(document.body).on('click', '#{$uniqBlock} li#quote', function(){editor.Quote(this, '#{$this->editor}');});
        buildSmiles();
OED;
        Yii::app()->clientScript->registerScript(uniqid(), $vars, CClientScript::POS_END);
        Yii::app()->clientScript->registerScript(uniqid(), $events, CClientScript::POS_LOAD);
        Yii::app()->clientScript->registerScriptFile($this->_assetsUrl.'/js/editor.js');
        Yii::app()->clientScript->registerScriptFile($this->_assetsUrl.'/js/jquery.selection.js');
        Yii::app()->clientScript->registerCssFile($this->_assetsUrl.'/css/style.css');

    }

    public function run()
    {
        if(null === $this->form)
            $this->render('index');
        else
            $this->render('index2');
    }
}