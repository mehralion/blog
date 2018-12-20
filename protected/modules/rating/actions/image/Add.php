<?php
namespace application\modules\rating\actions\image;
use application\modules\rating\components\RatingAction;

/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
 */
class Add extends RatingAction
{
    public function run()
    {
        if($this->isCommunity && !\Yii::app()->community->inCommunity()) {
            \Yii::app()->message->setErrors('danger', 'Вы не состоите в сообществе');
            \Yii::app()->message->showMessage();
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->scopes = array(
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus',
            'truncatedStatus',
        );
        $criteria->params = array(
            ':id' => \Yii::app()->request->getParam('item_id'),
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0,
        );

        /** @var \GalleryImage $GalleryImage */
        $GalleryImage = \GalleryImage::model()->find($criteria);
        if(!isset($GalleryImage))
            \MyException::ShowError(403, 'Фотография не найдена!');

        $criteria = new \CDbCriteria();
        $criteria->addCondition('item_id = :i_id and user_id = :u_id');
        $criteria->params = array(':i_id' => $GalleryImage->id, ':u_id' => \Yii::app()->user->id);
        $Rate = \RatingItemImage::model()->find($criteria);
        if(isset($Rate))
            \Yii::app()->message->setErrors('danger', 'Вы уже оценивали эту фотографию');
        elseif($GalleryImage->user_id == \Yii::app()->user->id)
            \Yii::app()->message->setErrors('danger', 'Вы не можете оценивать свою фотографию');
        else {
            $t = \Yii::app()->db->beginTransaction();
            $error = false;
            try {
                $Rate = new \RatingItemImage();
                $Rate->create_datetime = \DateTimeFormat::format();
                $Rate->user_id = \Yii::app()->user->id;
                $Rate->item_id = $GalleryImage->id;
                $Rate->value_type = \RatingItemImage::VALUE_TYPE_ADD;
                $Rate->user_owner_id = $GalleryImage->user_id;
                if(!$Rate->save())
                    $error = true;
                if(!$error) {
                    $GalleryImage->rating += 1;
                    if(!$GalleryImage->mUpdate())
                        $error = true;
                }

                if(!$error) {
                    /** @var \UserProfile $UserOwner */
                    $UserOwner = \UserProfile::model()->find('user_id = :user_id', array(':user_id' => $GalleryImage->user_id));
                    $UserOwner->rating += 1;
                    if(!$UserOwner->save())
                        $error = true;
                }

                if(!$error && $GalleryImage->is_community) {
                    /** @var \Community $Community */
                    $Community = \Community::model()->findByPk($GalleryImage->community_id);
                    $Community->rating += 1;
                    if(!$Community->mUpdate())
                        $error = true;
                }

                if($error)
                    $t->rollback();
                else {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Оценка добавлена');
                    \Yii::app()->message->setOther(array(
                        'selector' => '#image_'.$GalleryImage->id,
                        'rateVal' => $GalleryImage->rating
                    ));
                }
            } catch (\Exception $ex) {
                $t->rollback();
                \MyException::log($ex);
            }
        }
        \Yii::app()->message->showMessage();
    }
}