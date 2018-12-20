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
            <div class="form shadow community users">
                <?php
                $this->widget('bootstrap.widgets.TbMenu', array(
                    'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
                    'stacked'=>false, // whether this is a stacked menu
                    'encodeLabel' => false,
                    'items'=>array(
                        array('label'=>'Участники', 'url' => array('/community/users/index', 'community_alias' => Yii::app()->community->alias)),
                        array('label'=>'Модераторы', 'url' => array('/community/users/moders', 'community_alias' => Yii::app()->community->alias)),
                        array('label'=>'Запросы в сообщество', 'url' => array('/community/users/request', 'community_alias' => Yii::app()->community->alias)),
                        array('label'=>'Отправленные приглашения', 'url' => array('/community/users/invite', 'community_alias' => Yii::app()->community->alias)),
                    ),
                ));
                ?>
                <?php echo $content; ?>
            </div>
        </div>
    </div>
    <aside id="sideLeft">
        <?php $this->widget('application.widgets.menu.MenuWidget', array(
            'guest'      => true,
            'friend'     => false,
            'main'       => true,
            'user'       => true,
            'event'      => true,
            'moder'      => false,
            'subscribe'  => true,
        )); ?>
    </aside><!-- #sideLeft -->

    <aside id="sideRight">

            <?php $this->widget('application.widgets.menu.MenuWidget', array(
                'community' => true,
                'friend' => true,
                'rating' => true,
                'moder' => true,
            )); ?>
        <?php $this->widget('application.widgets.poll.PollWidget'); ?>
        <h2 class="title">ТЕГИ</h2>
        <div class="block">
            <?php $this->widget('application.widgets.tagCloud.TagCloudWidget', array(
                'maxTags'=>Yii::app()->params['tagCount'],
            )); ?>
        </div>
    </aside><!-- #sideRight -->
<?php $this->endContent(); ?>