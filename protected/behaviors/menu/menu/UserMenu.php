<?php
/**
 * Class UserMenu Меню пользователя
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 14.06.13
 * Time: 20:08
 * To change this template use File | Settings | File Templates.
 *
 * @package application.behaviors.menu
 */
class UserMenu
{
    /**
     * @return array
     */
    public function run()
    {
        if(Yii::app()->user->isGuest)
            return array();

        $friendLabel = 'Мои друзья';
        $ownCommentLabel = 'Комментарии в моем блоге';

        if(Yii::app()->user->isNewFriend())
            $friendLabel .= ' [ <div class="red attention">!</div> ]';

        /** @var EventViewDatetime $LastEventModel */
        $LastEventModel = EventViewDatetimeComment::model()->find('user_id = :user_id', array(
            ':user_id' => Yii::app()->user->id,
        ));
        $criteria = new CDbCriteria();
        if($LastEventModel) {
            $criteria->addCondition('`t`.create_datetime > :create_datetime');
            $criteria->params = array(':create_datetime' => $LastEventModel->update_datetime);
        }
        $criteria->addCondition('`t`.user_owner_id = :user_id and `t`.user_id != :user_id');
        $criteria->addCondition('`info`.is_community = 0');
        $criteria->with = array(
            'comment' => array(
                'scopes' => array(
                    'activatedStatus',
                    'deletedStatus',
                    'moderDeletedStatus',
                    'truncatedStatus',
                ),
                'params' => array(
                    ':activatedStatus' => 1,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0,
                )
            ),
            'info'
        );
        $criteria->params = CMap::mergeArray($criteria->params, array(':user_id' => Yii::app()->user->id));

        $dependency = new \CDbCacheDependency('select max(update_datetime) from {{comment_item}} where user_owner_id = :user_id and item_type != 5');
        $dependency->params = array(':user_id' => \Yii::app()->user->id);
        $dependency->reuseDependentData = true;

        $CommentCount = EventComment::model()->count($criteria);
        if($CommentCount)
            $ownCommentLabel .= ' [ <div class="red attention">!</div> ]';

        $menu = array(
            array(
                'label'=>'МОЙ БЛОГ',
                'linkOptions' => array('class' => 'title'),
                'linkLabelWrapper' => 'h2',
                'itemOptions' => array('class' => 'title')
            ),
            array('label'=>'Мои заметки', 'url'=>array('/post/profile/index', 'gameId' => Yii::app()->user->getGameId())),
            array('label'=>'Мои фотографии', 'url'=>array('/gallery/album/index_image', 'gameId' => Yii::app()->user->getGameId())),
            array('label'=>'Мои аудиозаписи', 'url'=>array('/gallery/album/index_audio', 'gameId' => Yii::app()->user->getGameId())),
            array('label'=>'Мои видеозаписи', 'url'=>array('/gallery/album/index_video', 'gameId' => Yii::app()->user->getGameId())),
            array(
                'label'=>'Мои сообщества',
                'url'=>array('/community/profile/own', 'gameId' => Yii::app()->user->getGameId()),
                'itemOptions' => array('class' => 'subHide'),
                'items' => array(
                    array('label' => '', 'url' => array('/community/profile/incommunity', 'gameId' => Yii::app()->user->getGameId())),
                    array('label' => '', 'url' => array('/community/profile/request', 'gameId' => Yii::app()->user->getGameId())),
                    array('label' => '', 'url' => array('/community/profile/invite', 'gameId' => Yii::app()->user->getGameId())),
                )
            ),
            array(
                'label'=>'Мои подписки',
                'url'=>array('/subscribe/index/user', 'gameId' => Yii::app()->user->getGameId()),
                'itemOptions' => array('class' => 'subHide'),
                'items' => array(
                    array('label' => '', 'url' => array('/subscribe/index/community', 'gameId' => Yii::app()->user->getGameId())),
                    array('label' => '', 'url' => array('/subscribe/index/debate', 'gameId' => Yii::app()->user->getGameId())),
                )
            ),
            array(
                'label'=>$friendLabel,
                'url'=>array('/friend/list/friend', 'gameId' => Yii::app()->user->getGameId()),
                'itemOptions' => array('class' => 'subHide'),
                'items' => array(
                    array('label' => '', 'url' => array('/friend/list/pending', 'gameId' => Yii::app()->user->getGameId())),
                    array('label' => '', 'url' => array('/friend/list/own', 'gameId' => Yii::app()->user->getGameId())),
                )
            ),
            array('label'=>$ownCommentLabel, 'url'=>array('/event/comment/own', 'gameId' => Yii::app()->user->getGameId())),
            array(
                'label'=>'Блоги друзей и сообщества',
                'url'=>array('/event/news/post', 'type' => 'friend'),
                'itemOptions' => array('class' => 'subHide'),
                'items' => array(
                    array('label' => '', 'url' => array('/event/news/image', 'type' => 'friend')),
                    array('label' => '', 'url' => array('/event/news/audio', 'type' => 'friend')),
                    array('label' => '', 'url' => array('/event/news/video', 'type' => 'friend')),
                    array('label' => '', 'url' => array('/event/comment/friend')),
                )
            ),
            array(
                'label'=>'Подписки',
                'url'=>array('/subscribe/show/post', 'gameId' => Yii::app()->user->getGameId()),
                'itemOptions' => array('class' => 'subHide'),
                'items' => array(
                    array('label' => '', 'url' => array('/subscribe/show/image', 'gameId' => Yii::app()->user->getGameId())),
                    array('label' => '', 'url' => array('/subscribe/show/audio', 'gameId' => Yii::app()->user->getGameId())),
                    array('label' => '', 'url' => array('/subscribe/show/video', 'gameId' => Yii::app()->user->getGameId())),
                    array('label' => '', 'url' => array('/subscribe/show/comment', 'gameId' => Yii::app()->user->getGameId())),
                    array('label' => '', 'url' => array('/subscribe/show/debate', 'gameId' => Yii::app()->user->getGameId())),
                )
            ),
            array(
                'label'=>'Корзина',
                'url'=>array('/trunc/show/post', 'gameId' => Yii::app()->user->getGameId()),
                'itemOptions' => array('class' => 'subHide'),
                'items' => array(
                    array('label' => '', 'url' => array('/trunc/show/image', 'gameId' => Yii::app()->user->getGameId())),
                    array('label' => '', 'url' => array('/trunc/show/audio', 'gameId' => Yii::app()->user->getGameId())),
                    array('label' => '', 'url' => array('/trunc/show/video', 'gameId' => Yii::app()->user->getGameId())),
                    array('label' => '', 'url' => array('/trunc/show/comment', 'gameId' => Yii::app()->user->getGameId())),
                    array('label' => '', 'url' => array('/trunc/show/community', 'gameId' => Yii::app()->user->getGameId())),
                )
            ),
        );

        $menu[] = [
            'label' => 'Реклама <sup class="new_advert">новое</sup>',
            'url' => Yii::app()->createUrl('/api/advert'),
            'linkOptions' => ['target' => '_blank']
        ];


        return $menu;
    }
}