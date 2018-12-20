<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 14:57
 * To change this template use File | Settings | File Templates.
 *
 * @var SubscribeUser $model
 */ ?>

<article class="short_block userSubscribe" id="subscribe_<?php echo $model->ownerUser->id; ?>">
    <h3 class="title">
        <?php echo $model->ownerCommunity->title; ?>
    </h3>
    <div class="content">
        <figure class="photo">
            <?php
            echo CHtml::link(
                CHtml::image($model->ownerCommunity->getAvatar(), $model->ownerCommunity->title),
                Yii::app()->createUrl('/community/request/show', array('community_alias' => $model->ownerCommunity->alias))
            );
            ?>
            <figcaption class="description bottom">
                <ul class="menu">
                    <li></li>
                </ul>
            </figcaption>
        </figure>
    </div>
    <div class="info">
        <div class="left" style="float: left;">
            <?php if($model->post): ?>
                <i class="icon" id="subscribePost_active" title="Вы подписаны на заметки"></i>
            <?php endif; ?>
            <?php if($model->image): ?>
                <i class="icon" id="subscribeImage_active" title="Вы подписаны на фотографии"></i>
            <?php endif; ?>
            <?php if($model->video): ?>
                <i class="icon" id="subscribeVideo_active" title="Вы подписаны на видеозаписи"></i>
            <?php endif; ?>
            <?php if($model->audio): ?>
                <i class="icon" id="subscribeAudio_active" title="Вы подписаны на аудиозаписи"></i>
            <?php endif; ?>
            <?php if($model->comment): ?>
                <i class="icon" id="subscribeComment_active" title="Вы подписаны на комментарии"></i>
            <?php endif; ?>
        </div>
        <div class="right" style="float: right;">
            <?php echo CHtml::link('<i class="icon" rel="tooltip" title="Редактировать подписку" id="edit"></i>', Yii::app()->createUrl('/subscribe/request/community', array('community_alias' => $model->ownerCommunity->alias)), array('class' => 'subscribe_update')); ?>
            <?php echo CHtml::link('<i class="icon" rel="tooltip" title="Удалить подписку" id="del"></i>', Yii::app()->createUrl('/subscribe/request/deletesubscribe', array('community_alias' => $model->ownerCommunity->alias)), array('class' => 'subscribe_delete')); ?>
        </div>
    </div>
</article>