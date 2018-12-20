<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 04.06.13
 * Time: 20:41
 * To change this template use File | Settings | File Templates.
 */?>

<section id="userProfile">
    <div class="img_border" style="float: right;">
        <img style="height: 81px;" src="<?php echo Yii::app()->user->avatar; ?>" alt="" />
    </div>
    <div class="userControl">
        <h4 class="nickName"><?php echo Yii::app()->user->Name; ?></h4>
        <hr class="ucHr">
        <?php $this->widget('zii.widgets.CMenu', array(
            'htmlOptions' => array(
                'class' => 'profile_menu'
            ),
            'items'=>array(
                //array('label'=>'Личные сообщения ▪', 'url'=>array('site/index'), 'linkOptions' => array('class' => 'dark')),
                array('label'=>'Профиль/Настройки ▪', 'url'=>array('/user/profile/show', 'gameId' => Yii::app()->user->getGameId()), 'linkOptions' => array('class' => 'dark')),
                array('label'=>'Выйти', 'url'=>array('/site/logout'), 'linkOptions' => array('class' => 'dark')),
                //array('label'=>'подписки ▪', 'url'=>array('site/index'), 'linkOptions' => array('class' => 'dark')),
                //array('label'=>'игры', 'url'=>array('site/index'), 'linkOptions' => array('class' => 'dark')),
            ),
        ));
        ?>
    </div>
</section>