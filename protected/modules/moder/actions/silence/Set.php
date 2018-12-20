<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 23:04
 * To change this template use File | Settings | File Templates.
 */

namespace application\modules\moder\actions\silence;


class Set extends \CAction
{
    public function run()
    {
        $id = \Yii::app()->request->getParam('id');
        $post = \Yii::app()->request->getPost('UserSilence');
        /** @var \User $User */
        $User = \User::model()->findByPk($id);
        if(!isset($User))
            \MyException::ShowError(404, 'Пользователь не найден');

        if($User->is_silenced) {
            if(time() >= strtotime($User->silence_end)) {
                $User->is_silenced = 0;
                $User->save();
            } else {
                $this->controller->renderPartial('silence_already');
                \Yii::app()->end();
            }
        }

        if($post) {
            $t = \Yii::app()->db->beginTransaction();
            $error = false;
            try {
                $silenceId = \Silence::Add($User, $post['moder_reason']);
                if(false === $silenceId)
                    $error = true;

                if(!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Вы удачно наложили заклятие молчания');
                } else
                    $t->rollback();

                \Yii::app()->message->showMessage();
            } catch (\Exception $ex) {
                $t->rollback();
                \MyException::log($ex);
            }
            \Yii::app()->message->showMessage();
        } else
            $this->controller->renderPartial('form', array(
                'model' => new \UserSilence()
            ), false, true);
    }
}