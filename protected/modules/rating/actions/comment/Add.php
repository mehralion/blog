<?php
namespace application\modules\rating\actions\comment;
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
        $criteria->with = array('info');
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

        /** @var \CommentItem $Comment */
        $Comment = \CommentItem::model()->find($criteria);
        if(!isset($Comment))
            \MyException::ShowError(403, 'Комментарий не найден!');

        $criteria = new \CDbCriteria();
        $criteria->addCondition('item_id = :i_id and user_id = :u_id');
        $criteria->params = array(':i_id' => $Comment->id, ':u_id' => \Yii::app()->user->id);
        $Rate = \RatingItemComment::model()->find($criteria);
        if(isset($Rate))
            \Yii::app()->message->setErrors('danger', 'Вы уже оценивали этот комментарий');
        elseif($Comment->user_id == \Yii::app()->user->id)
            \Yii::app()->message->setErrors('danger', 'Вы не можете оценивать свой комментарий');
        else {
            $t = \Yii::app()->db->beginTransaction();
            $error = false;
            try {
                $Rate = new \RatingItemComment();
                $Rate->create_datetime = \DateTimeFormat::format();
                $Rate->user_id = \Yii::app()->user->id;
                $Rate->item_id = $Comment->id;
                $Rate->value_type = \RatingItemComment::VALUE_TYPE_ADD;
                $Rate->user_owner_id = $Comment->user_id;
                if(!$Rate->save())
                    $error = true;
                if(!$error) {
                    $Comment->rating += 1;
                    $Comment->update_datetime = \DateTimeFormat::format();
                    if(!$Comment->save())
                        $error = true;
                }

                if(!$error) {
                    /** @var \UserProfile $UserOwner */
                    $UserOwner = \UserProfile::model()->find('user_id = :user_id', array(':user_id' => $Comment->user_id));
                    $UserOwner->rating += 1;
                    if(!$UserOwner->save())
                        $error = true;
                }

                if(!$error && $this->isCommunity) {
                    /** @var \Community $Community */
                    $Community = \Community::model()->findByPk($this->communityId);
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
                        'selector' => '#comment_'.$Comment->id,
                        'rateVal' => $Comment->rating
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