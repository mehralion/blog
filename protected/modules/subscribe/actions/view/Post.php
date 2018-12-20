<?php

namespace application\modules\subscribe\actions\view;

/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
 */
class Post extends \CAction {

    public function run() {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->scopes = array('own');
        $criteria->params = array(':id' => \Yii::app()->request->getParam('id'));
        /** @var \SubscribeDebatePost $model */
        $model = \SubscribeDebatePost::model()->find($criteria);
        if ($model) {
            $model->view_datetime = \DateTimeFormat::format();
            $model->update_datetime = \DateTimeFormat::format();
            if ($model->save())
                \Yii::app()->message->setOther(array('ok' => true));

            $commentString = '';
            $criteria = new \CDbCriteria();
            $criteria->addCondition('`t`.item_id = :item_id');
            $criteria->scopes = array(
                'activatedStatus',
                'deletedStatus',
                'moderDeletedStatus',
                'truncatedStatus',
            );
            $criteria->params = array(
                ':activatedStatus' => 1,
                ':deletedStatus' => 0,
                ':moderDeletedStatus' => 0,
                ':truncatedStatus' => 0,
                ':item_id' => $model->item_id
            );
            $criteria->with = array(
                'info' => array(
                    'scopes' => array(
                        'activatedStatus',
                        'truncatedStatus',
                        'deletedStatus',
                        'moderDeletedStatus',
                    ),
                    'params' => array(
                        ':activatedStatus' => 1,
                        ':truncatedStatus' => 0,
                        ':deletedStatus' => 0,
                        ':moderDeletedStatus' => 0,
                    )
                ),
                'canRate',
                'user'
            );
            $criteria->order = '`t`.create_datetime desc';
            $criteria->limit = 10;
            $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess('info', true));

            /** @var \CommentItemPost[] $Comments */
            $Comments = \CommentItemPost::model()->findAll($criteria);
            foreach($Comments as $Comment) {
                $Comment->quote = false;
                $commentString .= $this->controller->renderPartial('themePath.views.modules.comment.common.item', 
                        array('model' => $Comment),
                        true);
            }
                        
            \Yii::app()->message->setOther(array('content' => $commentString));
        } else
            \Yii::app()->message->setErrors('danger', 'Комментарии не найдены');

        \Yii::app()->message->showMessage();
    }

}
