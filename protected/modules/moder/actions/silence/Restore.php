<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 23:03
 * To change this template use File | Settings | File Templates.
 */

namespace application\modules\moder\actions\silence;


class Restore extends \CAction
{
    public function run()
    {
        $id = \Yii::app()->request->getParam('id');
        $post = \Yii::app()->request->getParam('ModerLog');
        /** @var \ModerLog $Log */
        $Log = \ModerLog::model()->findByPk($id);
        if(!isset($Log) )
            \MyException::ShowError(404, 'Возникла ошибка, повторите позже!');

        $criteria = new \CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->addCondition('is_silenced = :is_silenced');
        $criteria->params = array(
            ':is_silenced' => 1,
            ':id' => $Log->item_id
        );
        /** @var \User $User */
        $User = \User::model()->find($criteria);
        if(!isset($User))
            \MyException::ShowError(404, 'Пользователь не найден');

        if($post) {
            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                if(!\Silence::Restore($User, $post['moder_reason']))
                    $error = true;
                if(!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Молчанка снята');
                } else {
                    $t->rollback();
                    \Yii::app()->message->setErrors('danger', 'Возникли ошибки во время снятия молчанки');
                }

            } catch (\Exception $ex) {
                $t->rollback();
                \MyException::log($ex);
            }

            \Yii::app()->message->showMessage();
        } else
            $this->controller->renderPartial('ajax.moderRestore', array(
                'model' => new \ModerLog()
            ), false, true);
    }
}