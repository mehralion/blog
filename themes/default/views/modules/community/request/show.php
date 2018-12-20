<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 03.01.14
 * Time: 6:52
 *
 * @var Community $model
 * @var CommunityUser $users
 * @var CommunityUser $moders
 */
$this->breadcrumbs = array(
    'Мои сообщества' => Yii::app()->createUrl('/community/profile/own', array('gameId' => Yii::app()->user->getGameId())),
    'Сообщество - '.$model->title
);
?>
<div class="community show">
    <div class="left">
        <div class="img_border">
            <img src="<?php echo $model->getAvatar(); ?>" alt="">
        </div>
    </div>
    <ul class="stats">
        <li>Сообщество <?php echo $model->title; ?></li>
        <li><?php
            echo CHtml::link(
                'Заметки:', Yii::app()->createUrl('/event/news/post', array('community_alias' => $model->alias)));
            ?>
            <?php echo $post; ?></li>
        <li><?php
            echo CHtml::link(
                'Фотографии:', Yii::app()->createUrl('/event/news/image', array('community_alias' => $model->alias)));
            ?>
            <?php echo $image; ?></li>
        <li><?php
            echo CHtml::link(
                'Аудиозаписи:', Yii::app()->createUrl('/event/news/audio', array('community_alias' => $model->alias)));
            ?>
            <?php echo $audio; ?></li>
        <li><?php
            echo CHtml::link(
                'Видеозаписи:', Yii::app()->createUrl('/event/news/video', array('community_alias' => $model->alias)));
            ?>
            <?php echo $video; ?></li>
    </ul>
    <div class="rate">
        <label style="display: inline-block;">Рейтинг: </label>
        <?php $class = $rating < 0 ? 'red' : 'green';
        $sign = $rating > 0 ? '+' : ''; ?>
        <span class="value <?php echo $class; ?>"><?php echo $rating === null ? '0' : $sign . $rating; ?></span>
        <div class="clear"></div>
        <div class="">Вступление в сообщество: <?php echo Community::getCurrType($model->view_role); ?></div>
    </div>
    <div class="clear"></div>
    <div class="buttons" style="margin-bottom: 5px;">
        <div class="m_button">
            <?php if(Yii::app()->user->id == $model->user_id)
                echo CHtml::link(
                    'Удалить сообщество',
                    Yii::app()->createUrl('/community/profile/delete', array('community_alias' => $model->alias)),
                    array('class' => 'btn2')
                );
            elseif(Yii::app()->community->inCommunity())
                echo CHtml::link(
                    'Выйти из сообщества',
                    Yii::app()->createUrl('/community/request/logout', array('community_alias' => $model->alias)),
                    array('class' => 'btn2', 'confirm' => 'Вы точно хотите покинуть сообщество?')
                );
            elseif(Yii::app()->community->inInvite())
                echo CHtml::link(
                    'Подтвердить вступление',
                    Yii::app()->createUrl('/community/request/accept', array('community_alias' => $model->alias)),
                    array('class' => 'btn2', 'id' => 'connect_community')
                );
            elseif(Yii::app()->community->inRequest())
                echo CHtml::link(
                    'Вступление на модерации',
                    '#',
                    array('class' => 'btn2', 'onclick' => 'return false;')
                );
            else
                echo CHtml::link(
                    'Вступить в сообщество',
                    Yii::app()->createUrl('/community/request/connect', array('community_alias' => $model->alias)),
                    array('id' => 'connect_community', 'class' => 'btn2')
                ); ?>
            <a id="subscribe" class="btn1" href="<?php echo Yii::app()->createUrl('/subscribe/request/community', array('community_alias' => $model->alias)) ?>">Подписаться</a>
        </div>
    </div>
    <div class="friends">
        <?php echo Yii::app()->stringHelper->setBR(Yii::app()->stringHelper->parseTag(Yii::app()->community->description)); ?>
    </div>
    <div class="friends">
        <label>Участники: </label>
        <?php $userList = ''; ?>
        <?php foreach ($users as $user): ?>
            <?php if ($userList != '') $userList .= ', '; $userList .= CHtml::link($user->user->getFullLogin(), Yii::app()->createUrl('/user/profile/show', array('gameId' => $user->user->game_id))); ?>
        <?php endforeach; ?>
        <?php echo $userList; ?>
        <div class="clear"></div>
    </div>
    <div class="friends">
        <label>Модераторы: </label>
        <?php $userList = ''; ?>
        <?php foreach ($moders as $user): ?>
            <?php if ($userList != '') $userList .= ', '; $userList .= CHtml::link($user->user->getFullLogin(), Yii::app()->createUrl('/user/profile/show', array('gameId' => $user->user->game_id))); ?>
        <?php endforeach; ?>
        <?php echo $userList; ?>
        <div class="clear"></div>
    </div>
    <div class="friends">
        <label>Сообщество читают: </label>
        <?php $readMeList = ''; ?>
        <?php foreach ($readMeSubscribe as $subscribe): ?>
            <?php if ($readMeList != '') $readMeList .= ', '; $readMeList .= CHtml::link($subscribe->subscribeUser->getFullLogin(), Yii::app()->createUrl('/user/profile/show', array('gameId' => $subscribe->subscribeUser->game_id))); ?>
        <?php endforeach; ?>
        <?php echo $readMeList; ?>
        <div class="clear"></div>
    </div>
    <?php $this->widget('application.modules.comment.widgets.commentList.CommentListWidget', array(
        'item_type' => ItemTypes::ITEM_TYPE_COMMUNITY,
        'model' => $model,
        'url' => Yii::app()->createUrl('/comment/community/add', array('id' => $model->id, 'gameId' => $model->user->game_id, 'community_alias' => $model->alias))
    )); ?>
</div>
<script>
    $(function(){
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