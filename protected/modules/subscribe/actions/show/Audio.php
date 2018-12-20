<?php
namespace application\modules\subscribe\actions\show;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
 */
class Audio extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.audio = 1');
        $criteria->scopes = array('own');

        $dependency = new \CDbCacheDependency('SELECT MAX(update_datetime) FROM {{subscribe}} where subscribe_user_id = :subscribe_user_id');
        $dependency->params = array(':subscribe_user_id' => \Yii::app()->user->id);
        $dependency->reuseDependentData = true;

        $idsUser = array();
        /** @var \Subscribe[] $models */
        $models = \Subscribe::model()->cache(\Yii::app()->paramsWrap->cache->subscribe, $dependency)->findAll($criteria);
        foreach($models as $model)
            $idsUser[] = $model->item_id;

        $criteria = new \CDbCriteria();
        $access = \Yii::app()->access->GetStringAccess('audioAll');
        $criteria->with = array(
            'audioAll' => array(
                'condition' => 'DATE_FORMAT(audioAll.create_datetime, "%Y-%d-%m %H") = DATE_FORMAT(`t`.create_datetime, "%Y-%d-%m %H")',
                'on' => $access['condition'],
                'with' => array(
                    'album' => array(
                        'with' => array(
                            'info' => array(
                                'scopes' => array('deletedStatus', 'truncatedStatus', 'moderDeletedStatus'),
                                'params' => array(':deletedStatus' => 0, ':truncatedStatus' => 0, ':moderDeletedStatus' => 0),
                            )
                        )
                    )
                ),
                'scopes' => array(
                    'activatedStatus',
                    'deletedStatus',
                    'moderDeletedStatus',
                    'truncatedStatus',
                ),
                'params' => \CMap::mergeArray($access['params'], array(
                        ':activatedStatus' => 1,
                        ':deletedStatus' => 0,
                        ':moderDeletedStatus' => 0,
                        ':truncatedStatus' => 0,
                    ))
            ),
            'albumInfo',
            'user',
        );
        $criteria->addInCondition('`t`.user_id', $idsUser);
        $criteria->order = '`t`.create_datetime desc';
        $criteria->group = '`t`.user_id, t.album_id, DATE_FORMAT(`t`.create_datetime, "%Y-%d-%m %H")';

        $dependency = new \CDbCacheDependency('SELECT MAX(update_datetime) FROM {{cache_event_item}} where item_type = :item_type');
        $dependency->params = array(':item_type' => \ItemTypes::ITEM_TYPE_AUDIO_ALBUM);
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\EventItemAudio::model()
            ->cache(\Yii::app()->paramsWrap->cache->subscribe, $dependency)
            ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->image;
        $pages->applyLimit($criteria);

        $models = \EventItemAudio::model()->cache(\Yii::app()->paramsWrap->cache->subscribe, $dependency, 2)->findAll($criteria);

        $this->controller->render('audio', array(
            'models' => $models,
            'pages' => $pages,
        ));
    }
}