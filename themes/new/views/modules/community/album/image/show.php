<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 05.06.13
 * Time: 14:00
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryImage[] $models
 * @var GalleryAlbum $album
 * @var CPagination $pages
 */

$this->breadcrumbs = array(
    Yii::app()->community->title => Yii::app()->createUrl('/community/request/show', array('community_alias' => Yii::app()->community->alias)),
    'Фотоальбомы' => \Yii::app()->createUrl('/community/album/image', array('community_alias' => Yii::app()->community->alias)),
    $album->title
); ?>

<div class="buttons" style="padding-left: 6px;">
    <?php if(Yii::app()->community->inCommunity()): ?>
        <div class="m_button">
            <span class="btn2" id="add_image">Загрузить фотографию</span>
        </div>
    <?php endif; ?>
    <?php if(Access::canEdit($album->user_id)): ?>
        <div class="m_button">
            <?php echo CHtml::link(
                'Редактировать альбом',
                Yii::app()->createUrl('/community/album/image_update', array('album_id' => $album->id, 'community_alias' => Yii::app()->community->alias)
                ),
                array('class' => 'fancybox.ajax btn1', 'id' => 'edit_album_btn')); ?>
        </div>
    <?php endif; ?>
    <?php if(Yii::app()->community->isModer() || Access::canEdit($album->user_id)): ?>
        <div class="m_button">
            <?php echo CHtml::link(
                'Удалить альбом',
                Yii::app()->createUrl('/community/album/image_delete', array('album_id' => $album->id, 'community_alias' => Yii::app()->community->alias)),
                array('class' => 'fancybox.ajax btn1 del')); ?>
        </div>
    <?php endif; ?>
</div>
<?php if(Yii::app()->community->inCommunity()): ?>
    <div class="hidden block">
        <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.community.image.form_add', array(
            'model' => $new,
            'album_id' => $album->id
        ), false, false); ?>
    </div>
<?php endif; ?>

<ul class="image">
    <?php foreach($models as $model): ?>
        <li>
            <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.gallery.common.image', array(
                'model' => $model,
                'route' => '/community/image/show',
                'routeParams' => array('community_alias' => $model->community_alias)
            )); ?>
            <div class="buttons">
                <?php if(Access::canEdit($model->user_id)) {
                    echo CHtml::link(
                        '<i class="icon" id="edit" title="Редактировать"></i>',
                        Yii::app()->createUrl('/community/image/update', array('id' => $model->id, 'community_alias' => $model->community_alias)),
                        array('class' => 'edit fancybox.ajax', 'title' => 'Редактировать')
                    );
                    echo '<span style="width:10px;display: inline-block"></span>';
                }
                if(Yii::app()->community->isModer() || Access::canEdit($model->user_id)){
                    echo CHtml::link(
                        '<i class="icon" id="del" title="Удалить"></i>',
                        Yii::app()->createUrl('/community/image/delete', array('id' => $model->id, 'community_alias' => $model->community_alias)),
                        array('title' => 'Удалить', 'confirm' => "Вы уверены, что хотите отправить эту фотографию в корзину? \n Восстановить фотографию можно из корзины в любой момент.")
                    );
                } ?>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
<?php if(empty($models)): ?>
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
<?php if(null !== $album->image_front && $album->image_front != '' && !$album->is_croped && Access::canEdit($album->user_id)): ?>
    <div id="crop" style="display: none;">
        <img id="jcrop_target" src="<?php echo Yii::app()->baseUrl; ?>/uploads/albums/image/<?php echo  \Yii::app()->user->id .'/'.$album->image_front; ?>">
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
        $(function() {
            $.fancybox({
                fitToView	: false,
                autoSize	: true,
                autoHeight  : true,
                autoWidth   : true,
                openEffect	: 'none',
                closeEffect	: 'none',
                content     : $('#crop').html(),
                afterShow   : function(){
                    $('#saveCrop').click(function(){ sendCrop(); return false; });
                    setTimeout(function(){
                        $.fancybox.update();
                    }, 1000);
                    setTimeout(function(){
                        $('#jcrop_target').Jcrop({
                            bgFade:     true,
                            bgOpacity: .2,
                            allowResize: true,
                            aspectRatio: 1,
                            setSelect: [ 0, 0, 200, 200 ],
                            allowSelect: false,
                            allowMove : true,
                            onChange: showCoords
                        });
                        setTimeout(function(){
                            $.fancybox.update();
                        }, 1000);
                    }, 2000);
                },
                afterLoad   : function() {
                    $('#crop').remove();
                    return true;
                }
            });
        });
        function showCoords(c)
        {
            x = c.x;
            y = c.y;
            x2 = c.x2;
            y2 = c.y2;
            w = c.w;
            h = c.h;
        }
        function sendCrop()
        {
            $.ajax({
                url:'<?php echo Yii::app()->createUrl(
                    '/community/album/image_crop',
                    array('album_id' => $album->id, 'community_alias' => Yii::app()->community->alias)
                ); ?>',
                data:{
                    'crop[x]':x,
                    'crop[y]':y,
                    'crop[x2]':x2,
                    'crop[y2]':y2,
                    'crop[w]':w,
                    'crop[h]':h,
                    'YII_CSRF_TOKEN':'<?php echo Yii::app()->request->csrfToken; ?>'},
                type:'post',
                dataType:'json',
                success: function(response){
                    if(response.text !== undefined)
                        location.reload();
                    //location.reload();
                }
            });
        }
    </script>
<?php endif; ?>