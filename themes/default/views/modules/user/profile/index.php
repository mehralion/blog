<?php
/** @var TbActiveForm $form
 *  @var integer $rating
 *  @var integer $commentCount
 *  @var integer $ratingCount
 *  @var integer $postCount
 *  @var integer $imageCount
 *  @var integer $videoCount
 *  @var integer $audioCount
 *  @var CommunityUser[] $communities
 *
 * @var UserProfile $model
 *
 */
$this->breadcrumbs = array(
    'Профиль'
);
?>


<div class="profile">
    <div class="left">
        <div class="img_border">
            <img src="<?php echo Yii::app()->userOwn->getAvatar(); ?>" alt="">
        </div>
    </div>
    <ul class="stats">
        <li><?php
            echo CHtml::link(
                    'Комментарии:', Yii::app()->createUrl('/event/comment/index', array('gameId' => Yii::app()->user->getGameId())));
            ?>
            <?php echo $commentCount; ?></li>
        <li><?php
            echo CHtml::link(
                    'Заметки:', Yii::app()->createUrl('/event/news/post', array('gameId' => Yii::app()->user->getGameId())));
            ?>
            <?php echo $postCount; ?></li>
        <li><?php
            echo CHtml::link(
                    'Фотографии:', Yii::app()->createUrl('/event/news/image', array('gameId' => Yii::app()->user->getGameId())));
            ?>
            <?php echo $imageCount; ?></li>
        <li><?php
            echo CHtml::link(
                    'Аудиозаписи:', Yii::app()->createUrl('/event/news/audio', array('gameId' => Yii::app()->user->getGameId())));
            ?>
<?php echo $audioCount; ?></li>
        <li><?php
echo CHtml::link(
        'Видеозаписи:', Yii::app()->createUrl('/event/news/video', array('gameId' => Yii::app()->user->getGameId())));
?>
        <?php echo $videoCount; ?></li>
        <li><label>Активность (поставлено оценок): </label> <?php echo $ratingCount; ?></li>
    </ul>
    <div class="uploadAvatar">
        <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'avatar_form',
            'type' => 'horizontal',
            'action' => Yii::app()->createUrl('/user/profile/update'),
            'htmlOptions' => array(
                'enctype' => 'multipart/form-data',
            ),
        ));
        ?>
        <input type="text" class="field" style="margin-right: 10px;"><div class="m_button"><span class="btn1 obzor">Обзор...</span></div>
        <?php echo $form->fileFieldRow($model, 'avatar_path', array('style' => 'position:absolute;top:-100px;left:-1000px;', 'labelOptions' => array('label' => false))); ?>
        <div class="buttons">
            <div class="m_button">
                <input type="submit" id="submit_avatar" class="btn2" value="Обновить аватарку">
            </div>
        </div>
    <?php $this->endWidget(); ?>
    </div>
    <div class="rate">
    <?php $class = $rating < 0 ? 'red' : 'green';
    $sign = $rating > 0 ? '+' : ''; ?>
        <span class="value <?php echo $class; ?>"><?php echo $rating === null ? '0' : $sign . $rating; ?></span>
    </div>
    <div class="clear"></div>
        <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'profile_form',
            'type' => 'horizontal',
            'action' => Yii::app()->createUrl('/user/profile/update'),
        ));
        ?>
    <div class="description">
    <?php echo $form->textAreaRow($model, 'description', array('class' => 'span8', 'rows' => 5, 'labelOptions' => array('label' => false))); ?>
    </div>
    <div class="buttons">
        <div class="m_button">
            <input type="submit" class="btn2" value="Сохранить изменения">
        </div>
    </div>
        <?php $this->endWidget(); ?>
    <div class="friends">
        <label>Друзья: </label>
<?php $friendList = ''; ?>
        <?php foreach ($friends as $friend): ?>
            <?php if ($friendList != '') $friendList .= ', '; $friendList .= CHtml::link($friend->friend->getFullLogin(), Yii::app()->createUrl('/user/profile/show', array('gameId' => $friend->user->game_id))); ?>
        <?php endforeach; ?>
        <?php echo $friendList; ?>
        <div class="clear"></div>
    </div>
    <div class="friends">
        <label>Я читаю: </label>
<?php $readList = ''; ?>
<?php foreach ($readSubscribe as $subscribe): ?>
            <?php if ($readList != '') $readList .= ', '; $readList .= CHtml::link($subscribe->ownerUser->getFullLogin(), Yii::app()->createUrl('/user/profile/show', array('gameId' => $subscribe->ownerUser->game_id))); ?>
        <?php endforeach; ?>
        <?php echo $readList; ?>
        <div class="clear"></div>
    </div>

    <div class="friends">
        <label>Меня читают: </label>
    <?php $readMeList = ''; ?>
<?php foreach ($readMeSubscribe as $subscribe): ?>
    <?php if ($readMeList != '') $readMeList .= ', '; $readMeList .= CHtml::link($subscribe->subscribeUser->getFullLogin(), Yii::app()->createUrl('/user/profile/show', array('gameId' => $subscribe->subscribeUser->game_id))); ?>
<?php endforeach; ?>
<?php echo $readMeList; ?>
        <div class="clear"></div>
    </div>
    <div class="friends">
        <label>Сообщества: </label>
        <?php $communityList = ''; ?>
        <?php foreach ($communities as $community): ?>
            <?php if ($communityList != '') $communityList .= ', '; $communityList .= CHtml::link($community->community->title, Yii::app()->createUrl('/community/request/show', array('community_alias' => $community->community->alias))); ?>
        <?php endforeach; ?>
        <?php echo $communityList; ?>
        <div class="clear"></div>
    </div>
<?php $this->widget('application.modules.user.widgets.ld.LdWidget', array()); ?>
</div>

<script>
    $(function() {
        $(document.body).on('change', '#UserProfile_avatar_path', function() {
            if ($(this).val() != '')
                $('.field').val($(this).val());
        });
        $(document.body).on('click', '.obzor', function() {
            $('#UserProfile_avatar_path').trigger('click');
        });
        /*$(document.body).on('click', '.submit_form', function(event){
         $('#avatar_form').submit();
         event.preventDefault();
         });*/
    });
</script>
<?php if (null !== $model->avatar_path && $model->avatar_path != '' && !$model->is_croped): ?>
    <div id="crop" style="display: none;">
        <img id="jcrop_target" src="<?php echo Yii::app()->user->getAvatar(true); ?>">
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
        var x2 = 98;
        var y2 = 98;
        var w = 98;
        var h = 98;
        $(function() {
            $.fancybox({
                fitToView: false,
                autoSize: true,
                autoHeight: true,
                autoWidth: true,
                openEffect: 'none',
                closeEffect: 'none',
                content: $('#crop').html(),
                afterShow: function() {
                    $('#saveCrop').click(function() {
                        sendCrop();
                        return false;
                    });
                    setTimeout(function() {
                        $.fancybox.update();
                    }, 1000);
                    setTimeout(function() {
                        $('#jcrop_target').Jcrop({
                            bgFade: true,
                            bgOpacity: .2,
                            allowResize: true,
                            aspectRatio: 1,
                            setSelect: [0, 0, 98, 98],
                            allowSelect: false,
                            allowMove: true,
                            onChange: showCoords
                        });
                        setTimeout(function() {
                            $.fancybox.update();
                        }, 1000);
                    }, 2000);
                },
                afterLoad: function() {
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
                url: '<?php echo Yii::app()->createUrl('/user/profile/crop'); ?>',
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
                success: function(response) {
                    if (response.text !== undefined)
                        location.reload();
                    //location.reload();
                }
            });
        }
    </script>
<?php endif; ?>