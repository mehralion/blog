<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 05.06.13
 * Time: 14:00
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryAudio[] $models
 * @var GalleryAlbumAudio $album
 * @var CPagination $pages
 */
$this->breadcrumbs = array(
    Yii::app()->community->title => Yii::app()->createUrl('/community/request/show', array('community_alias' => Yii::app()->community->alias)),
    'Аудиоальбомы' => \Yii::app()->createUrl('/community/album/audio', array('community_alias' => Yii::app()->community->alias)),
    $album->title
);
?>
    <div class="buttons" style="padding-left: 6px;margin-bottom: 10px;">
        <?php if(Yii::app()->community->inCommunity()): ?>
            <div class="m_button">
                <span class="btn2" id="add_audio">Загрузить аудиозапись</span>
            </div>
        <?php endif; ?>
        <?php if(Access::canEdit($album->user_id)): ?>
            <div class="m_button">
                <?php echo CHtml::link(
                    'Редактировать альбом',
                    Yii::app()->createUrl('/community/album/audio_update', array('album_id' => $album->id, 'community_alias' => Yii::app()->community->alias)),
                    array('class' => 'fancybox.ajax btn1', 'id' => 'edit_album_btn')); ?>
            </div>
        <?php endif; ?>
        <?php if(Yii::app()->community->isModer() || $album->user_id == Yii::app()->user->id): ?>
            <div class="m_button">
                <?php echo CHtml::link(
                    'Удалить альбом',
                    Yii::app()->createUrl('/community/album/audio_delete', array('album_id' => $album->id, 'community_alias' => Yii::app()->community->alias)),
                    array('class' => 'fancybox.ajax btn1 del')); ?>
            </div>
        <?php  endif;?>
    </div>
<?php if(Yii::app()->community->inCommunity()): ?>
    <div class="hidden block">
        <?php $this->renderPartial('webroot.themes.' . Yii::app()->theme->name . '.views.modules.community.audio.form_add', array(
            'model' => $album,
        ), false, false); ?>
    </div>
<?php endif; ?>
    <article class="long_block">
        <h3 class="title">
            <i class="icon" id="shield"></i>
            <?php echo Yii::app()->stringHelper->subString($album->title, 70, '...');; ?>
        </h3>

        <div class="content">
            <div id="album_audio">
                <ul class="audio">
                    <?php foreach ($models as $model): ?>
                        <li>
                            <div class="audio_player">
                                <?php $this->renderPartial('webroot.themes.' . Yii::app()->theme->name . '.views.modules.gallery.common.audio', array(
                                    'model' => $model
                                )); ?>
                            </div>
                            <div class="audio_title"><?php echo $model->title; ?></div>
                            <div class="buttons">
                                <?php if(Access::canEdit($model->user_id)) {
                                    echo CHtml::link(
                                        '<i class="icon" id="edit" title="Редактировать"></i>',
                                        Yii::app()->createUrl('/community/audio/update', array('id' => $model->id, 'community_alias' => Yii::app()->community->alias)),
                                        array('class' => 'edit fancybox.ajax', 'title' => 'Редактировать')
                                    );
                                    echo '<span style="width:10px;display: inline-block"></span>';
                                }
                                if(Yii::app()->community->isModer() || Access::canEdit($model->user_id)){
                                    echo CHtml::link(
                                        '<i class="icon" id="del" title="Удалить"></i>',
                                        Yii::app()->createUrl('/community/audio/delete', array('id' => $model->id, 'community_alias' => Yii::app()->community->alias)),
                                        array('title' => 'Удалить', 'confirm' => "Вы уверены, что хотите отправить эту аудиозапись в корзину? \n Восстановить аудиозапись можно из корзины в любой момент.")
                                    );
                                } ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php echo $album->drawSubDescriptionsTextDeleted(); ?>
            </div>
            <?php if (empty($models)): ?>
                <div class="event_empty">Список пуст</div>
            <?php endif; ?>
            <? $this->widget('ext.pagination.Pager', array(
                'internalPageCssClass' => 'btn',
                'pages' => $pages,
                'header' => '',
                'selectedPageCssClass' => 'active',
                'htmlOptions' => array(
                    'class' => 'btn-group pagination',
                )
            )); ?>
        </div>
        <div class="info">
            <div class="left">
                <span class="author"><?php echo $album->user->getFullLogin(); ?></span>
                <time class="time"
                      datetime="<?php echo Yii::app()->params['siteTimeFormat'] ?>"><?php echo date('d.m.Y', strtotime($album->create_datetime)); ?>
                    | <?php echo date('H:i', strtotime($album->create_datetime)); ?></time>
                <?php if ($album->user_update_datetime > $album->create_datetime): ?>
                    <time class="time">Изменено: <?php echo date('d.m.Y H:i', strtotime($album->user_update_datetime)); ?></time>
                <?php endif; ?>
            </div>
            <div class="right">
                <?php $this->widget('application.widgets.buttons.MButtonsWidget', array(
                    'buttons' => array(
                        array(
                            'icon' => 'report',
                            'link' => Yii::app()->createUrl('/moder/report/audio', array(
                                    'community_alias' => $album->community_alias,
                                    'id' => $album->id,
                                )),
                            'title' => 'Пожаловаться',
                            'htmlOptions' => array(),
                            'visible' => !$album->is_reported && !Yii::app()->user->isModer() && !Yii::app()->community->isModer()
                        ),
                        array(
                            'icon' => 'del',
                            'link' => Yii::app()->createUrl('/moder/audio/delete', array(
                                    'community_alias' => $album->community_alias,
                                    'id' => $album->id,
                                )),
                            'title' => 'Удалить аудиоальбом',
                            'htmlOptions' => array('confirm' => 'Вы действительно хотите удалить аудиоальбом в корзину?', 'class' => 'moder_delete fancybox.ajax'),
                            'visible' => Yii::app()->user->isModer() && !Yii::app()->community->isModer()
                        ),
                    )
                )); ?>
                <span class="subscribe ajax"><?php echo $album->canSubscribe(); ?></span>
                <span class="ratingCount ajax"><?php echo $album->canRate(); ?> <span class="showRate ajax" data-link="<?php echo Yii::app()->createUrl('/community/listrate/comment_audio', array('id' => $album->id, 'community_alias' => $album->community_alias)); ?>" data-html="true" rel="tooltip" title="<?php echo $album->getRateList(); ?>">Понравилось <span id="audio_<?php echo $album->id; ?>"><?php echo $album->rating; ?></span></span></span>
                <span class="commentCount">Комментарии <?php echo $album->comment_count; ?></span>
            </div>
        </div>
    </article>
<?php

$this->widget('application.modules.comment.widgets.commentList.CommentListWidget', array(
    'item_type' => ItemTypes::ITEM_TYPE_AUDIO_ALBUM,
    'model' => $album,
    'url' => Yii::app()->createUrl('/comment/audio/add', array('id' => $album->id, 'community_alias' => $album->community_alias))
));

?>
<?php if (null !== $album->image_front && $album->image_front != '' && !$album->is_croped && Access::canEdit($album->user_id)): ?>
    <div id="crop" style="display: none;">
        <img id="jcrop_target"
             src="<?php echo $album->getImageUrl(true); ?>">
        <br>

        <div class="buttons">
            <div class="m_button">
                <button class="btn2" id="saveCrop" type="submit">Сохранить</button>
            </div>
        </div>
    </div>
    <script>
        var x = 0;
        var y = 0;
        var x2 = 200;
        var y2 = 200;
        var w = 200;
        var h = 200;
        $(function () {
            $.fancybox({
                fitToView: false,
                autoSize: true,
                autoHeight: true,
                autoWidth: true,
                openEffect: 'none',
                closeEffect: 'none',
                content: $('#crop').html(),
                afterShow: function () {
                    $('#saveCrop').click(function () {
                        sendCrop();
                        return false;
                    });
                    setTimeout(function () {
                        $.fancybox.update();
                    }, 1000);
                    setTimeout(function () {
                        $('#jcrop_target').Jcrop({
                            bgFade: true,
                            bgOpacity: .2,
                            allowResize: true,
                            aspectRatio: 1,
                            setSelect: [ 0, 0, 200, 200 ],
                            allowSelect: false,
                            allowMove: true,
                            onChange: showCoords
                        });
                        setTimeout(function () {
                            $.fancybox.update();
                        }, 1000);
                    }, 2000);
                },
                afterLoad: function () {
                    $('#crop').remove();
                    return true;
                }
            });
        });
        function showCoords(c) {
            x = c.x;
            y = c.y;
            x2 = c.x2;
            y2 = c.y2;
            w = c.w;
            h = c.h;
        }
        function sendCrop() {
            $.ajax({
                url: '<?php echo Yii::app()->createUrl(
                    '/community/album/audio_crop',
                    array('album_id' => $album->id, 'community_alias' => Yii::app()->community->alias)
                ); ?>',
                data: {
                    'crop[x]': x,
                    'crop[y]': y,
                    'crop[x2]': x2,
                    'crop[y2]': y2,
                    'crop[w]': w,
                    'crop[h]': h,
                    'YII_CSRF_TOKEN': '<?php echo Yii::app()->request->csrfToken; ?>'},
                type: 'post',
                dataType: 'json',
                success: function (response) {
                    if (response.text !== undefined)
                        location.reload();
                    //location.reload();
                }
            });
        }
    </script>
<?php endif; ?>