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
            $communityLabel = 'Сообщества';
            ?>
            <?php $this->widget('bootstrap.widgets.TbMenu', array(
                'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
                'stacked'=>false, // whether this is a stacked menu
                'encodeLabel' => false,
                'items'=>array(
                    array('label'=>$postLabel, 'url' => array('/trunc/show/post', 'gameId' => Yii::app()->user->getGameId())),
                    array('label'=>$imageLabel, 'url' => array('/trunc/show/image', 'gameId' => Yii::app()->user->getGameId())),
                    array('label'=>$audioLabel, 'url' => array('/trunc/show/audio', 'gameId' => Yii::app()->user->getGameId())),
                    array('label'=>$videoLabel, 'url' => array('/trunc/show/video', 'gameId' => Yii::app()->user->getGameId())),
                    array('label'=>$commentLabel, 'url' => array('/trunc/show/comment', 'gameId' => Yii::app()->user->getGameId())),
                    array('label'=>$communityLabel, 'url' => array('/trunc/show/community', 'gameId' => Yii::app()->user->getGameId())),
                    array(
                        'label'=>'Очистить все',
                        'url' => array('/trunc/request/trunc', 'gameId' => Yii::app()->user->getGameId()),
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