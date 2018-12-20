<?php
/**
 * @var CommunityUser[] $communities
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
        <li><?php echo Yii::app()->userOwn->getFullLogin(); ?></li>
        <li><?php
            echo CHtml::link(
                    'Комментарии:', Yii::app()->createUrl('/event/comment/index', array('gameId' => Yii::app()->userOwn->game_id)));
            ?>
            <?php echo $commentCount; ?></li>
        <li><?php
            echo CHtml::link(
                    'Заметки:', Yii::app()->createUrl('/event/news/post', array('gameId' => Yii::app()->userOwn->game_id)));
            ?>
            <?php echo $postCount; ?></li>
        <li><?php
            echo CHtml::link(
                    'Фотографии:', Yii::app()->createUrl('/event/news/image', array('gameId' => Yii::app()->userOwn->game_id)));
            ?>
            <?php echo $imageCount; ?></li>
        <li><?php
            echo CHtml::link(
                    'Аудиозаписи:', Yii::app()->createUrl('/event/news/audio', array('gameId' => Yii::app()->userOwn->game_id)));
            ?>
            <?php echo $audioCount; ?></li>
        <li><?php
            echo CHtml::link(
                    'Видеозаписи:', Yii::app()->createUrl('/event/news/video', array('gameId' => Yii::app()->userOwn->game_id)));
            ?>
<?php echo $videoCount; ?></li>
        <li><label>Активность (поставлено оценок): </label> <?php echo $ratingCount; ?></li>
    </ul>
    <?php if (Yii::app()->user->isModer()): ?>
        <div class="silence">
            <div class="buttons">
                <div class="m_button">
    <?php echo CHtml::link('Наказать', Yii::app()->createUrl('/moder/silence/set', array('id' => Yii::app()->userOwn->id)), array('id' => 'silence', 'class' => 'btn2 fancybox.ajax')); ?>
                </div>
            </div>
        </div>
<?php endif; ?>
    <div class="rate">
        <label style="display: inline-block;">Рейтинг: </label>
        <?php $class = $rating < 0 ? 'red' : 'green';
        $sign = $rating > 0 ? '+' : ''; ?>
        <span class="value <?php echo $class; ?>"><?php echo $rating === null ? '0' : $sign . $rating; ?></span>
    </div>
    <div class="clear"></div>
    <div class="" style="margin-bottom: 5px;">
        <?php if ($canFriend): ?>
            <div class="buttons deleteThis" style="margin-bottom: 5px; display: inline-block;">
                <div class="m_button">
                    <a id="addToFriend" class="btn2" href="<?php echo Yii::app()->createUrl('/friend/request/add', array('gameId' => Yii::app()->userOwn->game_id)) ?>">Добавить в друзья</a>
                </div>
            </div>
<?php endif; ?>
        <div class="buttons" style="display: inline-block;">
            <div class="m_button">
                <a id="subscribe" class="btn1" href="<?php echo Yii::app()->createUrl('/subscribe/request/subscribe', array('gameId' => Yii::app()->userOwn->game_id)) ?>">Подписаться</a>
            </div>
        </div>
    </div>
    <div class="description">
        <?php echo Yii::app()->stringHelper->setBR(Yii::app()->userOwn->description); ?>
    </div>
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
        $(document.body).on('click', '#silence', function(event) {
            event.preventDefault();
            var $self = $(this);
            $.fancybox.open({
                type: 'ajax',
                openEffect: 'none',
                closeEffect: 'none',
                href: $self.attr('href')
            });
        });

        $(document.body).on('click', '#subscribe', function(event) {
            event.preventDefault();
            var $self = $(this);
            $.fancybox.open({
                type: 'ajax',
                openEffect: 'none',
                closeEffect: 'none',
                href: $self.attr('href')
            });
        });
    });
</script>