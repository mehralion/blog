<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 23:21
 * To change this template use File | Settings | File Templates.
 */

namespace application\modules\moder\actions\report;


class AudioAlbum extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->scopes = array(
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus',
            'truncatedStatus'
        );
        $criteria->params = array(
            ':id' => \Yii::app()->request->getParam('album_id'),
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );
        /** @var \GalleryAlbumAudio $Audio */
        $Audio = \GalleryAlbumAudio::model()->find($criteria);
        if(!isset($Audio))
            \MyException::ShowError(404, 'Видео не найдено');

        $criteria = new \CDbCriteria();
        $criteria->addCondition('item_id = :id');
        $criteria->params = array(':id' => $Audio->id);
        /** @var \Report $Report */
        $Report = \ReportAudioAlbum::model()->find($criteria);
        if(null !== $Report) {
            if($Report->status == \ReportAudioAlbum::STATUS_PENDING)
                \Yii::app()->message->setErrors('danger', 'Кто-то уже пожаловался на этот альбом и он находится в очереди');
            elseif($Report->status == \ReportAudioAlbum::STATUS_DONE)
                \Yii::app()->message->setErrors('danger', 'Эту жалобу уже обработали');
        } else {
            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                $Report = new \ReportAudioAlbum();
                $Report->create_datetime = \DateTimeFormat::format();
                $Report->update_datetime = \DateTimeFormat::format();
                $Report->item_id = $Audio->id;
                $Report->title = $Audio->title;
                $Report->user_owner_id = $Audio->user_id;
                $Report->user_id = \Yii::app()->user->id;
                if(!$Report->save()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Report);
                }

                $Audio->is_reported = 1;
                if(!$Audio->mUpdate()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Audio);
                }

                if(!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Ваша жалоба добавлена в очередь');
                } else
                    $t->rollback();

            } catch (\Exception $ex) {
                $t->rollback();
                \MyException::log($ex);
                \Yii::app()->message->setErrors('danger', 'Повторите попытку позже');
            }
        }
        \Yii::app()->message->url = \Yii::app()->request->getUrlReferrer();
        \Yii::app()->message->showMessage();
    }
}