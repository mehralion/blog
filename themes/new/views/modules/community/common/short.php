<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 30.01.14
 * Time: 18:26
 *
 * @var Community $model
 */ ?>
<article class="short_block userSubscribe" id="subscribe_<?php echo $model->id; ?>">
    <h3 class="title">
        <?php echo CHtml::link(
            Yii::app()->stringHelper->subStringNew($model->title, 60, '...'),
            Yii::app()->createUrl('/community/request/show', array('community_alias' => $model->alias)),
            array('rel' => 'tooltip', 'title' => $model->title)
        ); ?>
    </h3>
    <div class="content" style="text-align: left;">
        <figure class="img_border">
            <?php echo CHtml::image($model->getAvatar()); ?>
        </figure>
        <div class="description" style="display: inline-block;width: 125px;">
            <?php $text = \Yii::app()->stringHelper->skipTag($model->description);
            echo Yii::app()->stringHelper->subStringNew($text, 120, '...'); ?>
        </div>
    </div>
    <div class="info">
        <div class="left" style="float: left;">
            <i class="icon" id="subscribePost_active" title="Заметки"></i><span class="ratingCount"><?php echo $model->postCount; ?></span>
            <i class="icon" id="subscribeImage_active" title="Фотографии"></i><span class="ratingCount"><?php echo $model->imageCount; ?></span>
            <i class="icon" id="subscribeVideo_active" title="Видеозаписи""></i><span class="ratingCount"><?php echo $model->videoCount; ?></span>
            <i class="icon" id="subscribeAudio_active" title="Аудиозаписи"></i><span class="ratingCount"><?php echo $model->audioCount; ?></span>
        </div>
        <div class="right">
            <?php $this->widget('application.widgets.buttons.MButtonsWidget', array(
                'buttons' => array(
                    array(
                        'icon' => 'report',
                        'link' => Yii::app()->createUrl('/moder/report/community', array('community_alias' => $model->alias)),
                        'title' => 'Пожаловаться',
                        'htmlOptions' => array(),
                        'visible' => !$model->is_reported && !Yii::app()->user->isModer()
                    ),
                    array(
                        'icon' => 'ok',
                        'link' => Yii::app()->createUrl('/community/request/accept', array('community_alias' => $model->alias)),
                        'title' => 'Вступить',
                        'htmlOptions' => array(),
                        'visible' => $model->inRequest && $model->inRequest->isInvite
                    ),
                    array(
                        'icon' => 'no',
                        'link' => Yii::app()->createUrl('/community/request/decline', array('community_alias' => $model->alias)),
                        'title' => 'Отказаться',
                        'htmlOptions' => array('confirm' => 'Вы уверены, что хотите отказаться от вступления в это сообщество?'),
                        'visible' => $model->inRequest && $model->inRequest->isInvite
                    ),
                    array(
                        'icon' => 'no',
                        'link' => Yii::app()->createUrl('/community/request/decline', array('community_alias' => $model->alias)),
                        'title' => 'Отозвать запрос',
                        'htmlOptions' => array('confirm' => 'Вы уверены, что зртите отозвать свой запрос?'),
                        'visible' => $model->inRequest && !$model->inRequest->isInvite
                    ),
                    array(
                        'icon' => 'del',
                        'link' => Yii::app()->createUrl('/community/profile/delete', array('community_alias' => $model->alias)),
                        'title' => 'Удалить сообщество',
                        'htmlOptions' => array('confirm' => 'Вы действительно хотите удалить сообщество?'),
                        'visible' => $model->user_id == Yii::app()->user->id
                    ),
                    array(
                        'icon' => 'del',
                        'link' => Yii::app()->createUrl('/moder/community/delete', array('community_alias' => $model->alias)),
                        'title' => 'Удалить сообщество',
                        'htmlOptions' => array('confirm' => 'Вы действительно хотите удалить сообщество?', 'class' => 'moder_delete fancybox.ajax'),
                        'visible' => Yii::app()->user->isModer() && $model->user_id != Yii::app()->user->id
                    ),
                    array(
                        'icon' => 'del',
                        'link' => Yii::app()->createUrl('/community/request/logout', array('community_alias' => $model->alias)),
                        'title' => 'Выйти из сообщества',
                        'htmlOptions' => array('confirm' => 'Вы действительно хотите выйти из этого сообщества?'),
                        'visible' => $model->inCommunity && $model->user_id != Yii::app()->user->id
                    ),
                )
            )); ?>
        </div>
    </div>
</article>