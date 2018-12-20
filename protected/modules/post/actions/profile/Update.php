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
class Update extends PostAction
{
    public $updateLinkRoute = null;
    public $updateLinkParams = array();

    public $viewName = 'form';

    public function run()
    {
        if($this->isCommunity) {
            if(!\Yii::app()->community->inCommunity()) {
                \Yii::app()->message->setErrors('danger', 'Вы не состоите в сообществе этой заметки');
                \Yii::app()->message->showMessage();
            }

            $this->successLinkRoute = '/community/post/show';
            $this->successLinkParams = array('community_alias' => \Yii::app()->community->alias);

            $this->updateLinkRoute = '/community/post/update';
            $this->updateLinkParams = array('community_alias' => \Yii::app()->community->alias);
        } else {
            $this->successLinkRoute = '/post/index/show';
            $this->successLinkParams = array('gameId' => \Yii::app()->user->getGameId());

            $this->updateLinkRoute = '/post/profile/update';
            $this->updateLinkParams = array('gameId' => \Yii::app()->user->getGameId());
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
        if (!isset($Post))
            \MyException::ShowError(403, 'Заметка не найдена');

        $Post->scenario = 'edit';

        $post = \Yii::app()->request->getParam('Post');
        $tags = \Yii::app()->request->getParam('Tags', array());

        if (!empty($post)) {
            $Post->attributes = $post;
            if (empty($tags))
                $Post->tags->removeAllTags();

            $Post->user_update_datetime = \DateTimeFormat::format();
            if($Post->tags->setTags($tags)->mUpdate())
                \Yii::app()->message->setText('success', 'Заметка обновлена!');
            else
                \Yii::app()->message->setErrors('danger', $Post);

            \Yii::app()->message->url = \Yii::app()->createUrl($this->successLinkRoute, \CMap::mergeArray($this->successLinkParams, array('id' => $Post->id)));
            \Yii::app()->message->showMessage();
        } else {
            $this->controller->renderPartial($this->viewName, array(
                'model' => $Post,
                'url' => \Yii::app()->createUrl($this->updateLinkRoute, \CMap::mergeArray($this->updateLinkParams, array('id' => $Post->id))),
            ), false, true);
        }
    }
}