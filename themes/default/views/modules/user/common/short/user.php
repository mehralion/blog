<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 26.06.13
 * Time: 15:33
 * To change this template use File | Settings | File Templates.
 *
 * @var User $model
 */ ?>

<article class="short_block userShortBlock">
    <h3 class="title">
        <?php echo $model->getFullLogin(); ?>
    </h3>
    <div class="content">
        <figure class="img_border">
            <?php
            echo CHtml::link(
                CHtml::image($model->getAvatar()),
                Yii::app()->createUrl('/user/profile/show', array(
                    'gameId' => $model->game_id
                ))
            );
            ?>
        </figure>
    </div>
    <div class="info">
        <span class="ratingCount"><span class="icon" id="like"></span> <?php echo $model->userProfile->rating; ?></span>
        <?php if(!Yii::app()->user->isGuest && Yii::app()->user->id != $model->id && !in_array($model->id, FriendRequest::getFriendsRequested(Yii::app()->user->id, true))): ?>
            <i style="margin-left: 45px;" rel="tooltip" title="Добавить в друзья <?php echo $model->login; ?>" class="icon addUserToFriend" login="<?php echo $model->login; ?>" link="<?php echo Yii::app()->createUrl('/friend/request/add', array('gameId' => $model->game_id)); ?>" id="add_friend"></i>
        <?php endif; ?>
    </div>
</article>