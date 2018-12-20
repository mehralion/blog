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
            <?php $this->widget('bootstrap.widgets.TbMenu', array(
                'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
                'stacked'=>false, // whether this is a stacked menu
                'encodeLabel' => false,
                'items'=>array(
                    array('label'=>'Пользователи', 'url' => array('/admin/user/index')),
                ),
            )); ?>
            <?php echo $content; ?>
        </div>
    </div>
    <aside id="sideLeft">
        <?php $this->widget('application.widgets.menu.MenuWidget', array(
            'friend'     => true,
            'main'       => true,
            'user'       => true,
            'event'      => true,
            'subscribe'  => true,
        )); ?>
    </aside><!-- #sideLeft -->

    <aside id="sideRight">
        <?php $this->widget('application.widgets.menu.MenuWidget', array(
            'rating' => true,
            'moder'      => true,
        )); ?>
        <h2 class="title">ТЕГИ</h2>
        <div class="block">
            <?php $this->widget('application.widgets.tagCloud.TagCloudWidget', array(
                'maxTags'=>Yii::app()->params['tagCount'],
            )); ?>
        </div>
    </aside><!-- #sideRight -->
<?php $this->endContent(); ?>