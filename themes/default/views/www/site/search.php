<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 10.08.13
 * Time: 22:01
 * To change this template use File | Settings | File Templates.
 *
 * @var TbActiveForm $form
 * @var Search $model
 * @var Post[] $models
 * @var CPagination $pages
 */ ?>

<style>
    .search form {
        text-align: center;
    }
</style>
<div class="search">
    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id'=>'search-form',
        'type'=>'horizontal',
        'method' => 'get',
        'action' => Yii::app()->createUrl('/site/search'),
        'enableAjaxValidation' => false,
    )); ?>
    <?php echo $form->textField($model, 'query', array('name' => 'query', 'placeholder' => 'Поисковая фраза', 'class' => 'span6'));  ?>
    <div class="searchType">
        <?php echo $form->radioButtonList($model, 'searchFlag', Search::getSearchType()); ?>
    </div>
        <div class="m_button">
            <input type="submit" id="search" class="btn2" value="Найти">
        </div>
    <?php $this->endWidget(); ?>
</div>
<div id="searchResult">

</div>

<script>
    var materialSearch = '<?php echo Yii::app()->createUrl('/site/search'); ?>';
    var blogerSearch = '<?php echo Yii::app()->createUrl('/site/searchblog'); ?>';
    $(function(){
        $(document.body).on('click', '#search', function(event){
            event.preventDefault();
            getContent($('#query').val(), 1, $('input:radio[name="Search[searchFlag]"]:checked').val());
        });

        $(document.body).on('click', '.pagination a', function(event){
            event.preventDefault();
            getContent($('#query').val(), $(this).attr('page'), $('input:radio[name="Search[searchFlag]"]:checked').val());
        });
    });

    function getContent(query, page, searchType)
    {
        var url = materialSearch;
        if(searchType == '1')
            url = blogerSearch;
        $.ajax({
            url:url,
            data:{'query':query, 'page':page, 'searchType':searchType},
            beforeSend:function(){
                $.fancybox.showLoading();
                return true;
            },
            success:function(response){
                $('#searchResult').html(response);
                $.fancybox.hideLoading();
            }
        });
    }
</script>