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
            <div class="community">
                <?php
                $this->widget('bootstrap.widgets.TbMenu', array(
                    'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
                    'stacked'=>false, // whether this is a stacked menu
                    'encodeLabel' => false,
                    'items'=>array(
                        array('label'=>'Мои сообщества', 'url' => array('/community/profile/own', 'gameId' => Yii::app()->user->getGameId())),
                        array('label'=>'Состою в сообществах', 'url' => array('/community/profile/incommunity', 'gameId' => Yii::app()->user->getGameId())),
                        array('label'=>'Запросы в сообщества', 'url' => array('/community/profile/request', 'gameId' => Yii::app()->user->getGameId())),
                        array('label'=>'Запросы ко мне', 'url' => array('/community/profile/invite', 'gameId' => Yii::app()->user->getGameId())),
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
                'community' => false,
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