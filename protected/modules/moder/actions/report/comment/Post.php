<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 23:20
 * To change this template use File | Settings | File Templates.
 */

namespace application\modules\moder\actions\report\comment;


class Post extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->scopes = array('activatedStatus', 'deletedStatus', 'moderDeletedStatus', 'truncatedStatus');
        $criteria->params = array(
            ':id' => \Yii::app()->request->getParam('id'),
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );
        /** @var \CommentItem $Comment */
        $Comment = \CommentItemPost::model()->find($criteria);
        if(!isset($Comment))
            \MyException::ShowError(404, 'Комментарий не найден');

        $criteria = new \CDbCriteria();
        $criteria->addCondition('item_id = :id');
        $criteria->params = array(':id' => $Comment->id);
        /** @var \Report $Report */
        $Report = \ReportComment::model()->find($criteria);
        if(null !== $Report) {
            if($Report->status == \ReportComment::STATUS_PENDING)
                \Yii::app()->message->setErrors('danger', 'Кто-то уже пожаловался на этот комментарий и он находится в очереди');
            elseif($Report->status == \ReportComment::STATUS_DONE)
                \Yii::app()->message->setErrors('danger', 'Эту жалобу уже обработали');
        } else {
            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                $Report = new \ReportComment();
                $Report->create_datetime = \DateTimeFormat::format();
                $Report->update_datetime = \DateTimeFormat::format();
                $Report->item_id = $Comment->id;
                $Report->title = "Комментарий";
                $Report->user_owner_id = $Comment->user_id;
                $Report->user_id = \Yii::app()->user->id;
                if(!$Report->save()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Report);
                }

                $Comment->is_reported = 1;
                if(!$Comment->save()){
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Comment);
                }

                /** @var \Post $Post */
                $Post = \Post::model()->findByPk($Comment->item_id);
                if(!$Post->mUpdate()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Post);
                }

                if(!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Ваша жалоба добавлена в очередь');
                } else
                    $t->rollback();

            } catch (\Exception $ex) {
                $t->rollback();
                \MyException::log($ex);
            }
        }
        \Yii::app()->message->url = \Yii::app()->request->getUrlReferrer();
        \Yii::app()->message->showMessage();
    }
}