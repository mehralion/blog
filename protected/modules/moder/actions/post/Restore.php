<?php
namespace application\modules\moder\actions\post;
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 22:22
 * To change this template use File | Settings | File Templates.
 */

class Restore extends \CAction
{
    public function run()
    {
        $id = \Yii::app()->request->getParam('id');
        $post = \Yii::app()->request->getParam('ModerLog');
        /** @var \ModerLog $Log */
        $Log = \ModerLogPost::model()->findByPk($id);
        if(!isset($Log) )
            \MyException::ShowError(404, 'Возникла ошибка, повторите позже!');

        $criteria = new \CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->scopes = array('moderDeletedStatus', 'deletedStatus', 'truncatedStatus');
        $criteria->params = array(
            ':moderDeletedStatus' => 1,
            ':truncatedStatus' => 0,
            ':deletedStatus' => 0,
            ':id' => $Log->item_id
        );
        /** @var \Post $Post */
        $Post = \Post::model()->find($criteria);
        if(!isset($Post))
            \MyException::ShowError(404, 'Заметка не найдена');

        if($post) {
            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                $Post->is_moder_deleted = 0;
                if(!$Post->restore()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Post);
                }

                $Log = new \ModerLogPost();
                $Log->update_datetime = \DateTimeFormat::format();
                $Log->create_datetime = \DateTimeFormat::format();
                if(!\Yii::app()->user->isAdmin())
                    $Log->scenario = 'moder';
                $Log->moder_id = \Yii::app()->user->id;
                $Log->item_id = $Post->id;
                $Log->user_owner_id = $Post->user_id;
                $Log->moder_reason = $post['moder_reason'];
                $Log->operation_type = \ModerLogPost::ITEM_OPERATION_RESTORE;
                if(!$Log->save()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Log);
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

            \Yii::app()->message->showMessage();
        } else {
            $model = new \ModerLog();
            if(!\Yii::app()->user->isAdmin())
                $model->scenario = 'moder';

            $this->controller->renderPartial('ajax.moderRestore', array(
                'model' => $model
            ), false, true);
        }
    }
}