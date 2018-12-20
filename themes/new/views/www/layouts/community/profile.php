<?php $this->beginContent('webroot.themes.'.Yii::app()->theme->name.'.views.www.layouts.main'); ?>
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
<?php $this->endContent(); ?>