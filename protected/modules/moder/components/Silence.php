<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 21:07
 * To change this template use File | Settings | File Templates.
 */

class Silence
{
    public static function Add(User $User, $reason)
    {
        $UserSilence = new \UserSilence();
        $UserSilence->create_datetime = DateTimeFormat::format();
        if(!Yii::app()->user->isAdmin())
            $UserSilence->scenario = 'moder';
        $UserSilence->user_id = $User->id;
        $UserSilence->moder_reason = $reason;
        $UserSilence->sender_id = \Yii::app()->user->id;
        $UserSilence->end_datetime = date(\Yii::app()->params['dbTimeFormat'], strtotime('+'.\UserSilence::SILENCE_DAY.' DAY'));
        if(!$UserSilence->save()) {
            \Yii::app()->message->setErrors('danger', $UserSilence);
            return false;
        }

        $User->is_silenced = true;
        $User->silence_end = $UserSilence->end_datetime;
        if(!$User->save()) {
            \Yii::app()->message->setErrors('danger', $User);
            return false;
        }

        $Log = new \ModerLog();
        $Log->update_datetime = DateTimeFormat::format();
        $Log->create_datetime = DateTimeFormat::format();
        if(!Yii::app()->user->isAdmin())
            $Log->scenario = 'moder';
        $Log->moder_id = \Yii::app()->user->id;
        $Log->item_id = $User->id;
        $Log->item_type = \ItemTypes::ITEM_TYPE_SILENCE;
        $Log->user_owner_id = $User->id;
        $Log->moder_reason = $reason;
        $Log->operation_type = \ModerLog::ITEM_OPERATION_SILENCE;
        $Log->silence_id = $UserSilence->id;
        if(!$Log->save()) {
            \Yii::app()->message->setErrors('danger', $Log);
            return false;
        }

        return $UserSilence->id;
    }

    public static function Restore(User $User, $reason)
    {
        $User->is_silenced = false;
        $User->silence_end = null;
        if(!$User->save()) {
            \Yii::app()->message->setErrors('danger', $User);
            return false;
        }

        $Log = new \ModerLog();
        $Log->update_datetime = DateTimeFormat::format();
        $Log->create_datetime = DateTimeFormat::format();
        if(!Yii::app()->user->isAdmin())
            $Log->scenario = 'moder';
        $Log->moder_id = \Yii::app()->user->id;
        $Log->item_id = $User->id;
        $Log->item_type = \ItemTypes::ITEM_TYPE_SILENCE;
        $Log->user_owner_id = $User->id;
        $Log->moder_reason = $reason;
        $Log->operation_type = \ModerLog::ITEM_OPERATION_SILENCE_RESTORE;
        if(!$Log->save()) {
            \Yii::app()->message->setErrors('danger', $Log);
            return false;
        }

        return true;
    }
}