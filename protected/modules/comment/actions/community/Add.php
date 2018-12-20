<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 22:39
 * To change this template use File | Settings | File Templates.
 */

namespace application\modules\comment\actions\community;


class Add extends \CAction
{
    public function run()
    {
        if(!\Yii::app()->community->inCommunity() && !\Yii::app()->community->isPublic()) {
            \Yii::app()->message->setErrors('danger', 'Вы не можете оставлять сообщения в этом сообществе');
            \Yii::app()->message->showMessage();
        }

        $Community = \Yii::app()->community->getModel();
        if($Community->is_deleted || $Community->is_moder_deleted) {
            \Yii::app()->message->setErrors('danger', 'Сообщество удалено');
            \Yii::app()->message->showMessage();
        }

        $post = \Yii::app()->request->getParam('CommentItem');
        if($post) {
            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                $Community->comment_count += 1;
                if(!$Community->mUpdate())
                    $error = true;

                if(!$error) {
                    $Comment = new \CommentItemCommunity();
                    $Comment->item_id = $Community->id;
                    $Comment->attributes = $post;
                    $Comment->user_owner_id = $Community->user_id;
                    $Comment->user_id = \Yii::app()->user->id;
                    if(!$Comment->create()) {
                        $error = true;
                        \Yii::app()->message->setErrors('danger', $Community);
                    }
                }

                if(!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Комментарий добавлен');
                } else
                    $t->rollback();

            } catch (\Exception $ex) {
                $t->rollback();
                \MyException::log($ex);
            }
        }

        \Yii::app()->message->showMessage();
    }
}