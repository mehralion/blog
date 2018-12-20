<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 06.06.13
 * Time: 16:09
 * To change this template use File | Settings | File Templates.
 *
 *
 * @var Post $model
 */ ?>

<article class="long_block">
    <h3 class="title">
        <i class="icon" id="shield"></i>
        <a href="<?php echo Yii::app()->createUrl('/post/index/show', array('id' => $model->id, 'gameId' => $model->user->game_id)); ?>">
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
            <?php if($model->user_update_datetime > $model->create_datetime): ?>
                <time class="time">Изменено: <?php echo date('d.m.Y H:i', strtotime($model->user_update_datetime)); ?></time>
            <?php endif; ?>
        </div>
        <div class="right">
            <?php $this->widget('application.widgets.itemButtons.ItemButtonsWidget', array(
                'model' => $model,
                'editLink' => Yii::app()->createUrl('/post/profile/update', array('id' => $model->id, 'gameId' => $model->user->game_id)),
                'deleteLink' => Yii::app()->createUrl('/post/profile/delete', array('id' => $model->id, 'gameId' => $model->user->game_id)),
                'reportLink' => Yii::app()->createUrl('/moder/report/post', array('id' => $model->id, 'gameId' => $model->user->game_id)),
                'deleteModerLink' => Yii::app()->createUrl('/moder/post/delete', array('id' => $model->id, 'gameId' => $model->user->game_id)),
                'deleteText' => "Вы уверены, что хотите отправить эту заметку в корзину? \n Восстановить заметку можно из корзины в любой момент.",
            )); ?>
            <span class="ratingCount ajax"><?php //echo $model->canRate(); ?> <span class="showRate ajax" data-link="<?php echo Yii::app()->createUrl('/post/index/listrate', array('id' => $model->id)); ?>" data-html="true" rel="tooltip" title="<?php echo $model->getRateList(); ?>">Понравилось <span id="post_<?php echo $model->id; ?>"><?php echo $model->rating; ?></span></span></span>
            <a href="<?php echo Yii::app()->createUrl('/post/index/show', array('id' => $model->id, 'gameId' => $model->user->game_id)); ?>"><span class="commentCount">Комментарии <?php echo $model->comment_count; ?></span></a>
        </div>
    </div>
</article>