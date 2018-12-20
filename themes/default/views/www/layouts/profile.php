<?php $this->beginContent('webroot.themes.'.Yii::app()->theme->name.'.views.www.layouts.main'); ?>
    <div id="container">
        <div id="profile">
            <?php echo $content; ?>
        </div>
    </div>
    <aside id="sideLeft">
        <h2 class="title">Меню пользователя</h2>
        <nav id="leftMenu">
            <?php
            $this->widget('zii.widgets.CMenu', array(
                'items'=>array(
                    array('label'=>'Профиль', 'url'=>array('/user/profile/index')),
                    array('label'=>'Мои фото', 'url'=>array('/gallery/profile/albums')),
                    array('label'=>'Мои видео', 'url'=>array('/gallery/profile/videos')),
                    array('label'=>'Посты', 'url'=>array('/post/profile/index')),
                ),
            ));
            ?>
        </nav>
    </aside><!-- #sideLeft -->
<?php $this->endContent(); ?>