<?php
namespace application\modules\community\actions\profile;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
 */
class Create extends \CAction
{
    public function run()
    {
        $model = null;
        if(\Yii::app()->community->alias) {
            $criteria = new \CDbCriteria();
            $criteria->scopes = array('deletedStatus', 'moderDeletedStatus', 'truncatedStatus', 'own');
            $criteria->addCondition('alias = :alias');
            $criteria->params = array(
                ':alias' => \Yii::app()->community->alias,
                ':deletedStatus' => 0,
                ':moderDeletedStatus' => 0,
                ':truncatedStatus' => 0
            );
            $model = \Community::model()->find($criteria);
        }

        if(!$model)
            $model = new \Community('create');

        $post = \Yii::app()->request->getParam('Community');
        if($post) {
            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                $model->attributes = $post;
                $model->user_id = \Yii::app()->user->id;
                $model->update_datetime = \DateTimeFormat::format();
                $model->create_datetime = \DateTimeFormat::format();
                if(!$model->create()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $model);
                }

                if(!$error) {
                    $inCommunity = new \CommunityUser();
                    $inCommunity->community_id = $model->id;
                    $inCommunity->user_id = \Yii::app()->user->id;
                    $inCommunity->user_type = \CommunityUser::TYPE_ADMIN;
                    $inCommunity->update_datetime = \DateTimeFormat::format();
                    $inCommunity->create_datetime = \DateTimeFormat::format();
                    if(!$inCommunity->save()) {
                        $error = true;
                        \Yii::app()->message->setErrors('danger', $inCommunity);
                    }
                }

                if(!$error) {
                    $t->commit();
                    \Yii::app()->message->setOther(array(
                        'content' => $this->controller->renderPartial('second_form', array('model' => $model), true, false)
                    ));
                    \Yii::app()->message->setText('success', 'Сообщество сохранено!');
                } else
                    $t->rollback();

            } catch (\Exception $ex) {
                $t->rollback();
                \MyException::log($ex);
            }

            \Yii::app()->message->showMessage();
        } else {
            if(!\Yii::app()->request->isAjaxRequest)
                $this->controller->render('first_form', array(
                    'model' => $model
                ));
            else {
                \Yii::app()->message->setOther(array(
                    'content' => $this->controller->renderPartial('first_form', array(
                            'model' => $model
                        ), true, true)
                ));
                \Yii::app()->message->showMessage();
            }
        }
    }
}