<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 06.06.13
 * Time: 16:53
 * To change this template use File | Settings | File Templates.
 *
 * @var CommentItem $model
 */
?>
<article class="comment_block">
    <div class="info">
        <div class="left">
            <span class="author"><?php echo $model->user->getFullLogin(); ?></span>
            <time class="time"
                  datetime="<?php echo Yii::app()->params['siteTimeFormat'] ?>"><?php echo date('d.m.Y', strtotime($model->create_datetime)); ?>
                | <?php echo date('H:i', strtotime($model->create_datetime)); ?></time>
        </div>
        <div class="right">
            <?php  $this->widget('application.widgets.buttons.MButtonsWidget', array(
                'buttons' => array(
                    array(
                        'icon' => 'quote',
                        'link' => '#',
                        'title' => 'Цитировать',
                        'htmlOptions' => array('class' => 'set_quote', 'data-content' => '[quote="'.$model->user->login.'"]'."\n".$model->description."\n".'[/quote]'),
                        'visible' => $model->quote
                    ),
                    array(
                        'icon' => 'report',
                        'link' => $model->getReportUrl(),
                        'title' => 'Пожаловаться',
                        'htmlOptions' => array(),
                        'visible' => !$model->is_reported && !Yii::app()->user->isModer() && !Yii::app()->community->isModer()
                    ),
                    array(
                        'icon' => 'del',
                        'link' => $model->getDeleteUrl(),
                        'title' => 'Удалить комментарий',
                        'htmlOptions' => array('confirm' => 'Вы действительно хотите удалить комментарий в корзину?'),
                        'visible' => Yii::app()->community->isModer() || Yii::app()->user->id == $model->user_id || Yii::app()->user->id == $model->user_owner_id
                    ),
                    array(
                        'icon' => 'del',
                        'link' => $model->getModerDeleteUrl(),
                        'title' => 'Удалить комментарий',
                        'htmlOptions' => array('confirm' => 'Вы действительно хотите удалить заметку?', 'class' => 'moder_delete fancybox.ajax'),
                        'visible' => Yii::app()->user->isModer() && !Yii::app()->community->isModer() && Yii::app()->user->id != $model->user_id && Yii::app()->user->id != $model->user_owner_id
                    ),
                )
            ));  ?>
            <span class="ratingCount ajax"><?php echo $model->canRate(); ?> Понравилось <span id="comment_<?php echo $model->id; ?>"><?php echo $model->rating; ?></span></span>
        </div>
    </div>
    <div class="content">
        <?php echo Yii::app()->stringHelper->setBR(Yii::app()->stringHelper->parseTag($model->description)); ?>
        <?php echo $model->drawSubDescriptionsTextDeleted(); ?>
    </div>
</article>