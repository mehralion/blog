<?php
namespace application\modules\community\actions\request;
use application\modules\community\components\CommunityAction;

/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
 */
class Logout extends CommunityAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.community_id = :community_id');
        $criteria->addCondition('`t`.user_id = :user_id');
        $criteria->params = array(
            ':community_id' => \Yii::app()->community->id,
            ':user_id' => \Yii::app()->user->id
        );

        /** @var \CommunityUser $inCommunity */
        $inCommunity = \CommunityUser::model()->find($criteria);
        if(!$inCommunity)
            \Yii::app()->message->setErrors('danger', 'Участник не найден, вы не состоите в этом сообществе');
        else {
            $inCommunity->is_deleted = 1;
            $inCommunity->update_datetime = \DateTimeFormat::format();
            if(!$inCommunity->save())
                \Yii::app()->message->setErrors('danger', $inCommunity);
            else
                \Yii::app()->message->setText('success', 'Вы покинули сообщество');
        }

        \Yii::app()->message->url = \Yii::app()->createUrl('/community/request/show', array('community_alias' => \Yii::app()->community->alias));
        \Yii::app()->message->showMessage();
    }
}