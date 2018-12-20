<?php
namespace application\modules\community\behaviors;
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
 * @property \Community $owner
 */
class Rights extends  \RightsBehavior
{
    /**
     * @return bool
     */
    public function canComment()
    {
        if(!$this->owner->is_deleted
            && !$this->owner->is_moder_deleted
            && !$this->owner->deleted_trunc
            && $this->owner->is_comment
            && !\Yii::app()->user->isSilence()
            && \Yii::app()->user->level > \Access::CAN_COMMENT
            && $this->owner->inCommunity
        )
            return true;
        else
            return false;
    }
}