<?php $this->beginContent('webroot.themes.'.Yii::app()->theme->name.'.views.www.layouts.main'); ?>
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
<?php $this->endContent(); ?>