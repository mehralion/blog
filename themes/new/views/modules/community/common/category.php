<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 02.01.14
 * Time: 13:37
 *
 * @var CommunityCategory $model
 */ ?>

<article class="community_block">
    <h3 class="title">
        <i class="icon" id="shield"></i>
        <?php echo CHtml::link(
            Yii::app()->stringHelper->subString($model->title, 70, '...'),
            Yii::app()->createUrl('/community/request/list', array('category_id' => $model->id))
        ); ?>
    </h3>
    <div class="content">
        <div class="description">
            <table>
                <?php foreach($model->community as $community): ?>
                    <tr>
                        <td><?php echo DateTimeFormat::format(Yii::app()->params['dateTime']['community'], $community->create_datetime).' '; ?>
                            <?php echo CHtml::link(
                                $community->title,
                                Yii::app()->createUrl('/community/request/show', array('community_alias' => $community->alias))
                            ); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if(count($model->community) == CommunityCategory::LIMIT): ?>
                <tr><td><?php echo CHtml::link(
                        'Смотреть полный список >>',
                        Yii::app()->createUrl('/community/request/list', array('category_id' => $model->id))
                    ); ?></td></tr>
                <?php endif; ?>
            </table>
        </div>
        <div class="clear"></div>
    </div>
    <div class="info"></div>
</article>