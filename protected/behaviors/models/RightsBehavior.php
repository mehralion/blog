<?php
/**
 * Class RightsBehavior
 * Created by JetBrains PhpStorm.
 * User: Nick Nikitchenko
 * Skype: quietasice
 * E-mail: quietasice123@gmail.com
 * Date: 04.07.13
 * Time: 18:27
 * To change this template use File | Settings | File Templates.
 *
 * @package application.behaviors.models
 *
 * @property null | Post | GalleryImage | GalleryVideo $owner
 */
class RightsBehavior extends  CActiveRecordBehavior
{
    /** @var array  */
    protected $buttons = array(
        'moderDelete'    => false,
        'delete'         => false,
        'edit'           => false,
        'report'         => false,
    );

    /**
     * @param null $owner_id
     * @return array
     */
    public function getAvailableButtons($owner_id = null)
    {
        if(!$this->owner->is_deleted && !$this->owner->is_moder_deleted && ($this->owner->user_id == Yii::app()->user->id || $owner_id == Yii::app()->user->id)) {
            $this->buttons['delete'] = true;
            $this->buttons['edit'] = true;
        } elseif($this->owner->user_id != Yii::app()->user->id && !$this->owner->is_deleted && !$this->owner->is_moder_deleted) {
            if(Yii::app()->user->isModer())
                $this->buttons['moderDelete'] = true;
            elseif(!$this->owner->is_reported)
                $this->buttons['report'] = true;
        }

        return $this->buttons;
    }

    /**
     * @return bool
     */
    public function canComment()
    {
        if(!$this->owner->is_deleted && !$this->owner->is_moder_deleted && $this->owner->is_comment && !Yii::app()->user->isSilence() && Yii::app()->user->level > Access::CAN_COMMENT)
            return true;
        else
            return false;
    }
}