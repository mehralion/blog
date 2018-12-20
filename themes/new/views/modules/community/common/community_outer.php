<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 02.01.14
 * Time: 13:37
 *
 * @var Community $model
 */ ?>

<article class="long_block">
    <h3 class="title">
        <i class="icon" id="shield"></i>
        <a href="<?php echo Yii::app()->createUrl('/community/request/show', array('community_alias' => $model->alias)); ?>">
            <?php
            echo Yii::app()->stringHelper->subString($model->title, 70, '...');
            ?>
        </a>
    </h3>
    <div class="content">
        <div class="description">
            <table>
                <tr>
                    <td style="vertical-align: top;">
                        <span class="avatar">
                            <?php echo CHtml::image($model->user->getAvatar(), '', array('align' => 'left')); ?>
                        </span>
                    </td>
                    <td style="vertical-align: top;">
                        <span class="text" style="display: block;word-wrap: break-word;">
                            <?php $text = Yii::app()->stringHelper->setBR(Yii::app()->stringHelper->parseTag($model->description));
                            echo Yii::app()->stringHelper->subStringNew($text, 800, '...');
                            ?>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
        <?php echo $model->drawSubDescriptionsTextDeleted(); ?>
        <div class="clear"></div>
    </div>
    <div class="info">
        <div class="left">
            <span class="author"><?php echo $model->user->getFullLogin(); ?></span>
            <time class="time" datetime="<?php echo Yii::app()->params['siteTimeFormat'] ?>"><?php echo date('d.m.Y', strtotime($model->create_datetime)); ?> | <?php echo date('H:i', strtotime($model->create_datetime)); ?></time>
        </div>
        <div class="right">
            <?php $this->widget('application.widgets.buttons.MButtonsWidget', array(
                'buttons' => array(
                    array(
                        'icon' => 'report',
                        'link' => Yii::app()->createUrl('/moder/report/community', array('community_alias' => $model->alias)),
                        'title' => 'Пожаловаться',
                        'htmlOptions' => array(),
                        'visible' => !$model->is_reported && !Yii::app()->user->isModer() && $model->user_id != Yii::app()->user->id
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
                        'htmlOptions' => array('confirm' => 'Вы уверены, что хотите отозвать свой запрос?'),
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
                        'htmlOptions' => array('confirm' => 'Вы действительно хотите удалить сообщество?'),
                        'visible' => Yii::app()->user->isModer() && !$model->inCommunity
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
            <span class="ratingCount ajax"><?php echo $model->canRate(); ?> <span class="showRate ajax" data-link="<?php echo Yii::app()->createUrl('/community/request/listrate', array('community_alias' => $model->alias)); ?>" data-html="true" rel="tooltip" title="<?php echo $model->getRateList(); ?>">Понравилось <span id="community_<?php echo $model->id; ?>"><?php echo $model->rating; ?></span></span></span>
            <a href="<?php echo Yii::app()->createUrl('/community/request/show', array('community_alias' => $model->alias)); ?>"><span class="commentCount">Комментарии <?php echo $model->comment_count; ?></span></a>
        </div>
    </div>
</article>