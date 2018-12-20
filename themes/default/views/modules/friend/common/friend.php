<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 14:57
 * To change this template use File | Settings | File Templates.
 *
 * @var UserFriend $model
 */ ?>

<article class="short_block userBlockFriend">
    <h3 class="title">
        <?php if($type == 'pending') $UserModel = $model->user; else $UserModel = $model->friend; ?>
        <?php echo $UserModel->getFullLogin(); ?>
    </h3>
    <div class="content">
        <figure class="photo">
            <?php
            echo CHtml::link(
                CHtml::image($UserModel->getAvatar(), $UserModel->login),
                Yii::app()->createUrl('/user/profile/show', array('gameId' => $UserModel->game_id))
            );
            ?>
            <figcaption class="description bottom">
                <ul class="menu">
                    <li>
                    <?php if($type == 'pending') {
                        echo CHtml::link(
                            '<i class="icon" id="ok"></i>',
                            Yii::app()->createUrl('/friend/request/accept', array('id' => $model->id)),
                            array('class' => 'ajaxRequestFriend')
                        );
                        echo CHtml::link(
                            '<i class="icon" id="no"></i>',
                            Yii::app()->createUrl('/friend/request/fail', array('id' => $model->id)),
                            array('class' => 'ajaxRequestFriend')
                        );
                    } elseif($type == 'friend') {
                        echo CHtml::link(
                            '<i class="icon" id="del"></i>',
                            Yii::app()->createUrl('/friend/request/delete', array('gameId' => $model->friend->game_id)),
                            array('class' => 'ajaxRequestFriend')
                        );
                    } elseif($type == 'own') {
                        echo CHtml::link(
                            '<i class="icon" id="del"></i>',
                            Yii::app()->createUrl('/friend/request/cancel', array('id' => $model->id)),
                            array('class' => 'ajaxRequestFriend')
                        );
                    } ?>
                    </li>
                </ul>
            </figcaption>
        </figure>
    </div>
    <div class="info">
        <div class="left">
            <time class="time" datetime="<?php echo Yii::app()->params['siteTimeFormat'] ?>"><?php echo date('d.m.Y', strtotime($model->create_datetime)); ?> | <?php echo date('H:i', strtotime($model->create_datetime)); ?></time>
        </div>
    </div>
</article>