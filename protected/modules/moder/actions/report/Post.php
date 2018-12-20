<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 23:21
 * To change this template use File | Settings | File Templates.
 */

namespace application\modules\moder\actions\report;


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

        /** @var \Post $Post */
        $Post = \Post::model()->find($criteria);
        if(!isset($Post))
            \MyException::ShowError(404, 'Заметка не найдена');

        $criteria = new \CDbCriteria();
        $criteria->addCondition('item_id = :id');
        $criteria->params = array(':id' => $Post->id);
        /** @var \ReportPost $Report */
        $Report = \ReportPost::model()->find($criteria);
        if(null !== $Report) {
            if($Report->status == \ReportPost::STATUS_PENDING)
                \Yii::app()->message->setErrors('danger', 'Кто-то уже пожаловался на эту заметку и она находится в очереди');
            elseif($Report->status == \ReportPost::STATUS_DONE)
                \Yii::app()->message->setErrors('danger', 'Эту жалобу уже обработали');
        } else {
            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                $Report = new \ReportPost();
                $Report->create_datetime = \DateTimeFormat::format();
                $Report->update_datetime = \DateTimeFormat::format();
                $Report->item_id = $Post->id;
                $Report->title = $Post->title;
                $Report->user_owner_id = $Post->user_id;
                $Report->user_id = \Yii::app()->user->id;
                if(!$Report->save()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Report);
                }

                $Post->is_reported = 1;
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