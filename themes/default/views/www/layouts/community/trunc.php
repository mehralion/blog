<?php
/**
 * @var \application\modules\trunc\controllers\ShowController $this
 */
?>
<?php $this->beginContent('webroot.themes.'.Yii::app()->theme->name.'.views.www.layouts.main'); ?>
    <div id="container">
        <div id="content">
            <?php $this->widget('bootstrap.widgets.TbAlert', array(
                'block'=>true, // display a larger alert block?
                'fade'=>true, // use transitions?
                'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
                //'alerts'=>array( // configurations per alert type
                //    'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
                //),
            )); ?>
            <?php
            $postLabel = 'Заметки';
            $imageLabel = 'Фотографии';
            $audioLabel = 'Аудиозаписи';
            $videoLabel = 'Видеозаписи';
            $commentLabel = 'Комментарии';
            ?>
            <?php $this->widget('bootstrap.widgets.TbMenu', array(
                'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
                'stacked'=>false, // whether this is a stacked menu
                'encodeLabel' => false,
                'items'=>array(
                    array('label'=>$postLabel, 'url' => array('/community/trunc/post', 'community_alias' => Yii::app()->community->alias)),
                    array('label'=>$imageLabel, 'url' => array('/community/trunc/image', 'community_alias' => Yii::app()->community->alias)),
                    array('label'=>$audioLabel, 'url' => array('/community/trunc/audio', 'community_alias' => Yii::app()->community->alias)),
                    array('label'=>$videoLabel, 'url' => array('/community/trunc/video', 'community_alias' => Yii::app()->community->alias)),
                    array('label'=>$commentLabel, 'url' => array('/community/trunc/comment', 'community_alias' => Yii::app()->community->alias)),
                    array(
                        'label'=>'Очистить все',
                        'url' => array('/community/trunc/delete', 'community_alias' => Yii::app()->community->alias),
                        'linkOptions' => array(
                            'confirm' => "Вы уверены, что хотите очистить корзину? \n Все материалы в корзине будут удалены навсегда."
                        )
                    ),
                ),
            )); ?>
            <?php echo $content; ?>
        </div>
    </div>
    <aside id="sideLeft">
        <?php $this->widget('application.widgets.menu.MenuWidget', array(
            'friend' => true,
            'main'   => true,
            'user'   => true,
            'event'  => true,
            'subscribe' => true
        )); ?>
    </aside><!-- #sideLeft -->

    <aside id="sideRight">
        <?php $this->widget('application.widgets.menu.MenuWidget', array(
            'rating' => true,
            'community' => true,
            'moder' => true
        )); ?>
        <h2 class="title">ТЕГИ</h2>
        <div class="block">
            <?php $this->widget('application.widgets.tagCloud.TagCloudWidget', array(
                'maxTags'=>Yii::app()->params['tagCount'],
            )); ?>
        </div>
    </aside><!-- #sideRight -->
<?php $this->endContent(); ?>