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
class Delete extends \CAction
{
    public function run()
    {
        $userId = \Yii::app()->request->getParam('user_id');
        $itemId = \Yii::app()->request->getParam('item_id');
        if(empty($userId) || empty($itemId))
            return;

        \Rights::model()->deleteAll('user_id = :user_id and item_id = :item_id', array(':user_id' => $userId, ':item_id' => $itemId));
    }
}