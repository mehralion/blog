<?php
namespace application\modules\post\actions\profile;
use application\modules\post\components\PostAction;

/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.post.actions.profile
 */
class Delete extends PostAction
{
    public function run()
    {
        if($this->isCommunity) {
            if(!\Yii::app()->community->inCommunity()) {
                \Yii::app()->message->setErrors('danger', 'Вы не состоите в этом сообществе');
                \Yii::app()->message->showMessage();
            }

            $this->successLinkRoute = '/community/post/index';
            $this->successLinkParams = array('community_alias' => \Yii::app()->community->alias);
        } else {
            $this->successLinkRoute = '/post/profile/index';
            $this->successLinkParams = array('gameId' => \Yii::app()->user->getGameId());
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->scopes = array('own', 'activatedStatus', 'deletedStatus', 'moderDeletedStatus', 'truncatedStatus');
        $criteria->params = array(
            ':id' => \Yii::app()->request->getParam('id'),
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );
        $criteria->with = array(
            'info' => array(
                'scopes' => array('deletedStatus', 'truncatedStatus', 'moderDeletedStatus'),
                'params' => array(':deletedStatus' => 0, ':truncatedStatus' => 0, ':moderDeletedStatus' => 0),
            )
        );
        /** @var \Post $Post */
        $Post = \Post::model()->find($criteria);
        if(!$Post)
            \MyException::ShowError(500, 'Заметка не найдена');

        $Post->user_deleted_id = \Yii::app()->user->id;
        $Post->is_deleted = 1;
        if($Post->delete())
            \Yii::app()->message->setText('success', 'Заметка удалена');
        else
            \Yii::app()->message->setErrors('danger', $Post);

        \Yii::app()->message->url =  \Yii::app()->createUrl($this->successLinkRoute, $this->successLinkParams);
        \Yii::app()->message->showMessage();
    }
}