<?php
/**
 * Class CommentItemImage
 *
 * @package application.comment.models
 */
class CommentItemImage extends CommentItem
{
    /**
     * @param string $className
     * @return CommentItemImage
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return array
     */
    public function defaultScope() {
        $t = $this->getTableAlias(false, false);
        return array(
            'condition' => $t . '.item_type = :'.$t.'_item_type',
            'params' => array(':'.$t.'_item_type' => ItemTypes::ITEM_TYPE_IMAGE)
        );
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        $this->item_type = ItemTypes::ITEM_TYPE_IMAGE;
        return parent::beforeValidate();
    }

    public function create($validate = true, $attributes = null)
    {
        $t = null;
        if(null === Yii::app()->db->getCurrentTransaction()) {
            /** @var CDbTransaction $t */
            $t = Yii::app()->db->beginTransaction();
        }
        $error = false;
        try {
            $this->create_datetime = \DateTimeFormat::format();
            $this->update_datetime = \DateTimeFormat::format();
            if(!$this->save($validate, $attributes)) {
                $error = true;
                Yii::app()->message->setErrors('danger', $this);
            }

            /** @var EventCommentAudio $Event */
            $Event = null;
            if(!$error) {
                $Event = new EventCommentImage();
                $Event->comment_id = $this->id;
                $Event->item_id = $this->item_id;
                $Event->user_id = Yii::app()->user->id;
                $Event->user_owner_id = $this->user_owner_id;
                $Event->create_datetime = DateTimeFormat::format();
                if(!$Event->save()) {
                    $error = true;
                    Yii::app()->message->setErrors('danger', $Event);
                }
            }

            if(null !== $t) {
                if(!$error)
                    $t->commit();
                else
                    $t->rollback();
            }

            return !$error;
        } catch (Exception $ex) {
            if(null !== $t)
                $t->rollback();

            MyException::log($ex);
            return false;
        }
    }
}