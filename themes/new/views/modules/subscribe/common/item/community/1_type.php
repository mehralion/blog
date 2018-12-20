<?php
/**
 *
 * @var SubscribeDebatePost $model
 */
?>
<?php $timestamp = strtotime($model->comment[0]->create_datetime); ?>
<article class="long_block">
    <div class="content subscribe">
        <div class="left" style="float: left;">
            Комментарии к
            <?php echo CHtml::link(
                'заметке',
                Yii::app()->createUrl(
                    '/community/post/show',
                    array('id' => $model->item_id,'community_alias' => $model->info->community_alias)
                )); ?>
            <?php echo $model->owner->getFullLogin(); ?>
            <?php echo CHtml::link(
                $model->item_title,
                Yii::app()->createUrl(
                    '/community/post/show',
                    array('id' => $model->item_id,'community_alias' => $model->info->community_alias)
                )); ?>
        </div>
        <div style="float: right;cursor: pointer;">
            <div
                style="display: inline-block;"
                class="show_comment right"
                position="0"
                block_id="comment_post_<?php echo $model->item_id; ?>"
                link="<?php echo Yii::app()->createUrl('/subscribe/view/post', array('id' => $model->id)); ?>">
                Просмотреть
            </div>
            <?php if($timestamp > strtotime($model->view_datetime)):  ?>
                <div class="new" style="display: inline-block;">[<span style="color: red;">!</span>]</div>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
    </div>
</article>
<div style="display: none;" id="comment_post_<?php echo $model->item_id; ?>">
</div>