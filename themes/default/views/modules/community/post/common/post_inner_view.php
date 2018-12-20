<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 06.06.13
 * Time: 16:09
 * To change this template use File | Settings | File Templates.
 *
 * @var Post $model
 */
?>
<article class="long_block">
    <h3 class="title">
        <i class="icon" id="shield"></i>
        <?php echo Yii::app()->stringHelper->subString($model->title, 70, '...'); ?></h3>

    <div class="content">
        <div class="description">
            <table>
                <tr>
                    <td style="vertical-align: top;">
                        <span class="avatar">
                                        <?php echo CHtml::image($model->user->getAvatar(), '', array('align' => 'left')); ?>
                                    </span>
                    </td>
                    <td style="vertical-align: top;min-width: 655px;">
                        <span style="display: block;word-wrap: break-word;">
                            <?php echo Yii::app()->stringHelper->setBR(Yii::app()->stringHelper->parseTag($model->description)); ?>
                        </span>
                        <?php $this->widget('application.widgets.poll.PollViewWidget', array(
                            'pollId' => $pollId
                        )); ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>
        <?php echo $model->drawSubDescriptionsTextDeleted(); ?>
    </div>
    <div class="info">
        <div class="left">
            <span class="author"><?php echo $model->user->getFullLogin(); ?></span>
            <time class="time"
                  datetime="<?php echo Yii::app()->params['siteTimeFormat'] ?>"><?php echo date('d.m.Y', strtotime($model->create_datetime)); ?>
                | <?php echo date('H:i', strtotime($model->create_datetime)); ?></time>
            <?php if($model->user_update_datetime > $model->create_datetime): ?>
                <time class="time">Изменено: <?php echo date('d.m.Y H:i', strtotime($model->user_update_datetime)); ?></time>
            <?php endif; ?>
        </div>
        <div class="right">
            <?php $this->widget('application.widgets.buttons.MButtonsWidget', array(
                'buttons' => array(
                    array(
                        'icon' => 'edit',
                        'link' => Yii::app()->createUrl('/community/post/update', array(
                                'community_alias' => $model->community_alias,
                                'id' => $model->id,
                            )),
                        'title' => 'Редактировать',
                        'htmlOptions' => array('class' => 'update item'),
                        'visible' => $model->user_id == Yii::app()->user->id
                    ),
                    array(
                        'icon' => 'report',
                        'link' => Yii::app()->createUrl('/moder/report/post', array(
                                'community_alias' => $model->community_alias,
                                'id' => $model->id,
                            )),
                        'title' => 'Пожаловаться',
                        'htmlOptions' => array(),
                        'visible' => !$model->is_reported && !Yii::app()->user->isModer() && !Yii::app()->community->isModer()
                    ),
                    array(
                        'icon' => 'del',
                        'link' => Yii::app()->createUrl('/community/post/delete', array(
                                'community_alias' => $model->community_alias,
                                'id' => $model->id,
                            )),
                        'title' => 'Удалить заметку',
                        'htmlOptions' => array('confirm' => 'Вы действительно хотите удалить заметку в корзину?'),
                        'visible' => Yii::app()->community->isModer()
                    ),
                    array(
                        'icon' => 'del',
                        'link' => Yii::app()->createUrl('/moder/post/delete', array(
                                'community_alias' => $model->community_alias,
                                'id' => $model->id,
                            )),
                        'title' => 'Удалить сообщество',
                        'htmlOptions' => array('confirm' => 'Вы действительно хотите удалить заметку?', 'class' => 'moder_delete fancybox.ajax'),
                        'visible' => Yii::app()->user->isModer() && !Yii::app()->community->isModer()
                    ),
                )
            )); ?>

            <span class="subscribe ajax"><?php echo $model->canSubscribe(); ?></span>
            <span class="ratingCount ajax"><?php echo $model->canRate(); ?> <span class="showRate ajax" data-link="<?php echo Yii::app()->createUrl('/community/post/listrate', array('id' => $model->id, 'community_alias' => $model->community_alias)); ?>" data-html="true" rel="tooltip" title="<?php echo $model->getRateList(); ?>">Понравилось <span id="post_<?php echo $model->id; ?>"><?php echo $model->rating; ?></span></span></span>
            <span class="commentCount">Комментарии <?php echo $model->comment_count; ?></span>
        </div>
    </div>
</article>