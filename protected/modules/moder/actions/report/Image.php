<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 23:21
 * To change this template use File | Settings | File Templates.
 */

namespace application\modules\moder\actions\report;


class Image extends \CAction
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
        /** @var \GalleryImage $Image */
        $Image = \GalleryImage::model()->find($criteria);
        if(!isset($Image))
            \MyException::ShowError(404, 'Фотография не найдена');

        $criteria = new \CDbCriteria();
        $criteria->addCondition('item_id = :id');
        $criteria->params = array(':id' => $Image->id);
        /** @var \Report $Report */
        $Report = \ReportImage::model()->find($criteria);
        if(null !== $Report) {
            if($Report->status == \ReportImage::STATUS_PENDING)
                \Yii::app()->message->setErrors('danger', 'Кто-то уже пожаловался на эту фотографию и она находится в очереди');
            elseif($Report->status == \ReportImage::STATUS_DONE)
                \Yii::app()->message->setErrors('danger', 'Эту жалобу уже обработали');
        } else {
            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                $Report = new \ReportImage();
                $Report->create_datetime = \DateTimeFormat::format();
                $Report->update_datetime = \DateTimeFormat::format();
                $Report->item_id = $Image->id;
                $Report->title = $Image->title;
                $Report->user_owner_id = $Image->user_id;
                $Report->user_id = \Yii::app()->user->id;
                if(!$Report->save()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Report);
                }

                $Image->is_reported = 1;
                if(!$Image->mUpdate()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Image);
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