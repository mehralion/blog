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
class Reset extends PostAction
{
    public function run()
    {
        if($this->isCommunity && !\Yii::app()->community->isModer()) {
            \Yii::app()->message->setErrors('danger', 'У вас нет прав для восстановления этой заметки');
            \Yii::app()->message->showMessage();
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->scopes = array(
            'deletedStatus',
            'moderDeletedStatus',
            'truncatedStatus',
        );
        $criteria->params = array(
            ':id' => \Yii::app()->request->getParam('id'),
            ':deletedStatus' => 1,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0,
        );

        /** @var \Post $Post */
        $Post = \Post::model()->find($criteria);
        if (!isset($Post))
            \MyException::ShowError(403, 'Подходящая заметка не найдена');
        elseif(!$this->isCommunity && \Yii::app()->user->id != $Post->user_id) {
            \Yii::app()->message->setErrors('danger', 'У вас нет прав для восстановления этой заметки');
            \Yii::app()->message->showMessage();
        }

        $error = false;
        $t = \Yii::app()->db->beginTransaction();
        try {
            if(!$Post->restore()) {
                $error = true;
                \Yii::app()->message->setErrors('danger', $Post);
            }

            if(!$error) {
                $t->commit();
                \Yii::app()->message->setText('success', 'Заметка восстановлена');
            } else
                $t->rollback();

        } catch (\Exception $ex) {
            $t->rollback();
            \MyException::log($ex);
        }
        \Yii::app()->message->url = \Yii::app()->request->getUrlReferrer();
        \Yii::app()->message->showMessage();
    }
}