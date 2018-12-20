<?php
namespace application\modules\admin\actions\rights;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.post.actions.index
 */
class Add extends \CAction
{
    public function run()
    {
        $post = \Yii::app()->request->getParam('Rights');
        if($post) {
            $User = \User::model()->find('login = :login', array(':login' => trim($post['user_id'])));
            if(!$User)
                \Yii::app()->message->setErrors('danger', 'Пользователь не найден');
            else {
                $model = \Rights::model()->find('user_id = :user_id and item_id = :item_id', array(':user_id' => $User->id, ':item_id' => $post['item_id']));
                if($model) {
                    \Yii::app()->message->setErrors('danger', 'Подобные права уже выданы');
                } else {
                    $post['user_id'] = $User->id;
                    $Rights = new \Rights();
                    $Rights->attributes = $post;
                    $Rights->save();
                    \Yii::app()->message->setText('success', 'Права добавлены');
                }
            }
        }

        \Yii::app()->message->url = \Yii::app()->createUrl('/admin/rights/index');
        \Yii::app()->message->showMessage();
    }
}