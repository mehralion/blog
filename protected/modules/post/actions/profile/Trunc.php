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
class Trunc extends PostAction
{
    public function run()
    {
        if($this->isCommunity && !\Yii::app()->community->isModer()) {
            \Yii::app()->message->setErrors('danger', 'У вас нет прав для восстановления этой заметки');
            \Yii::app()->message->showMessage();
        }

        $id = \Yii::app()->request->getParam('id');
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->addCondition('`t`.is_deleted = 1 or `t`.is_moder_deleted = 1');
        $criteria->scopes = array('truncatedStatus',);

        $criteria->params = \CMap::mergeArray($criteria->params, array(
            ':id' => $id,
            ':truncatedStatus' => 0,
        ));

        /** @var \Post $Post */
        $Post = \Post::model()->find($criteria);
        if(!$Post)
            \MyException::ShowError(500, 'Заметка не найдена');
        elseif(!$this->isCommunity && \Yii::app()->user->id != $Post->user_id) {
            \Yii::app()->message->setErrors('danger', 'У вас нет прав для удаления этой заметки');
            \Yii::app()->message->showMessage();
        }

        $Post->deleted_trunc = 1;
        if($Post->mUpdate())
            \Yii::app()->message->setText('success', 'Заметка удалена навсегда');
        else
            \Yii::app()->message->setErrors('danger', 'Возникли проблемы, попробуйте позже');

        \Yii::app()->message->url = \Yii::app()->request->getUrlReferrer();
        \Yii::app()->message->showMessage();
    }
}