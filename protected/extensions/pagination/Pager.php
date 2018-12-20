<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 11.06.13
 * Time: 18:52
 * To change this template use File | Settings | File Templates.
 */

class Pager extends CLinkPager
{
    public $firstPageCssClass = 'btn';
    public $previousPageCssClass = 'btn';
    public $nextPageCssClass = 'btn';
    public $lastPageCssClass = 'btn';

    public $nextPageLabel = '>';
    public $prevPageLabel = '&lt;';
    public $firstPageLabel = '&lt;&lt;';
    public $lastPageLabel = '>>';

    private $_assetsUrl = null;

    public $firstUrl = false;
    public $pageVar = 'page';
    public $params;
    public $route = '';

    public function run()
    {
        if ($this->_assetsUrl === null)
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(
                (__DIR__).'/assets/'
            );

        Yii::app()->clientScript->registerCssFile($this->_assetsUrl.'/css/style.css');

        $this->registerClientScript();
        $buttons=$this->createPageButtons();
        if(empty($buttons))
            return;
        echo $this->header;
        echo CHtml::tag('div',array('class' => 'btn-toolbar'),CHtml::tag('div',$this->htmlOptions,implode("\n",$buttons)));
        echo $this->footer;
    }

    protected function createPageButton($label,$page,$class,$hidden,$selected)
    {
        if($hidden || $selected)
            $class.=' '.($hidden ? $this->hiddenPageCssClass : $this->selectedPageCssClass);
        if($page !== false)
            return CHtml::link($label,$this->createPageUrl($page), array('class' => $class, 'data-page' => $page+1));
        else
            return CHtml::link($label,'', array('class' => $class, 'pageNumber' => $page));
    }

    protected function createPageButtons()
    {
        if(($pageCount=$this->getPageCount())<=1)
            return array();

        list($beginPage,$endPage)=$this->getPageRange();
        $currentPage=$this->getCurrentPage(false); // currentPage is calculated in getPageRange()
        $buttons=array();

        // first page
        $buttons[]=$this->createPageButton($this->firstPageLabel,0,$this->firstPageCssClass,$currentPage<=0,false);

        // prev page
        if(($page=$currentPage-1)<0)
            $page=0;
        $buttons[]=$this->createPageButton($this->prevPageLabel,$page,$this->previousPageCssClass,$currentPage<=0,false);

        // internal pages
        for($i=$beginPage;$i<=$endPage;++$i)
            $buttons[]=$this->createPageButton($i+1,$i,$this->internalPageCssClass,false,$i==$currentPage);

        // next page
        if(($page=$currentPage+1)>=$pageCount-1)
            $page=$pageCount-1;
        $buttons[]=$this->createPageButton($this->nextPageLabel,$page,$this->nextPageCssClass,$currentPage>=$pageCount-1,false);

        // last page
        $buttons[]=$this->createPageButton($this->lastPageLabel,$pageCount-1,$this->lastPageCssClass,$currentPage>=$pageCount-1,false);

        return $buttons;
    }

    /**
     * Creates the URL suitable for pagination.
     * @param integer $page the page that the URL should point to.
     * @return string the created URL
     * @see CPagination::createPageUrl
     */
    protected function createPageUrl($page)
    {
        $params=$this->params===null ? $_GET : $this->params;
        if(isset($params['comment_id']))
            unset($params['comment_id']);
        if($page>0) // page 0 is the default
            $params[$this->pageVar]=$page+1;
        else
            unset($params[$this->pageVar]);
        return $this->getController()->createUrl($this->route,$params);
    }
}