<?php
namespace application\modules\user\controllers;
/**
 * Class ShowController
 *
 * @method \CAction posts()
 * @method \CAction albums()
 * @method \CAction videos()
 * @method \CAction images()
 *
 * @package application.user.controllers
 */
class ShowController extends \FrontController
{
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
        );
    }

    public function actions()
    {
        return array(
            'posts' => array(
                'class' => '\application\modules\post\actions\index\Index',
                'userId' => \Yii::app()->userOwn->id,
                'viewName' => 'post'
            ),
            'album_image' => array(
                'class' => '\application\modules\gallery\actions\album\image\Index',
                'userId' => \Yii::app()->userOwn->id,
                'viewName' => 'album/image'
            ),
            'show_image'=> array(
                'class' => '\application\modules\gallery\actions\album\image\Show',
                'userId' => \Yii::app()->userOwn->id,
                'viewName' => 'image'
            ),
            'album_audio'=> array(
                'class' => '\application\modules\gallery\actions\album\audio\Index',
                'userId' => \Yii::app()->userOwn->id,
                'viewName' => 'album/audio'
            ),
            'show_audio'=> array(
                'class' => '\application\modules\gallery\actions\album\audio\Show',
                'userId' => \Yii::app()->userOwn->id,
                'viewName' => 'audio'
            ),
            'album_video'=> array(
                'class' => '\application\modules\gallery\actions\album\video\Index',
                'userId' => \Yii::app()->userOwn->id,
                'viewName' => 'album/video'
            ),
            'show_video'=> array(
                'class' => '\application\modules\gallery\actions\album\video\Show',
                'userId' => \Yii::app()->userOwn->id,
                'viewName' => 'video'
            ),
        );
    }

    public  $_user;
    public function beforeAction($action)
    {
        parent::beforeAction($action);

        return true;
    }

    public function actionTop()
    {
        $dependency = new \CDbCacheDependency('SELECT MAX(last_update) FROM {{user}}');
        $dependency->reuseDependentData = true;

        $criteria = new \CDbCriteria();
        $pages = new \CPagination(\User::model()
                ->cache(24 * 3600, $dependency)
                ->count($criteria));
        $pages->pageSize = \Yii::app()->params['page_size']['top_user'];
        $pages->applyLimit($criteria);

        $criteria->select = '`t`.game_id, `t`.last_update';
        $criteria->with = ['userProfile' => ['select' => false]];
        $criteria->order = '`userProfile`.rating desc, `t`.id';

        /** @var \User[] $Users */
        $Users = \User::model()
            ->cache(24 * 3600, $dependency)
            ->findAll($criteria);

        $UsersToView = [];
        $_userIdsToGetInfo = [];
        foreach ($Users as $User) {
            $UsersToView[$User->game_id] = null;
            $cache_id = 'user_full_info_'.$User->game_id.'_'.strtotime($User->last_update);

            $_User = \Yii::app()->cache->get($cache_id);
            if($_User !== false) {
                $UsersToView[$User->game_id] = $_User;
                continue;
            }

            $_userIdsToGetInfo[] = $User->game_id;
        }

        if(!empty($_userIdsToGetInfo)) {
            $criteria = new \CDbCriteria();
            $criteria->addInCondition('game_id', $_userIdsToGetInfo);
            $criteria->with = ['userProfile'];
            /** @var \User $User */
            foreach(\User::model()->findAll($criteria) as $User) {
                $cache_id = 'user_full_info_'.$User->game_id.'_'.strtotime($User->last_update);
                $UsersToView[$User->game_id] = $User;

                \Yii::app()->cache->set($cache_id, $User, 24 * 3600);
            }
        }

        $this->render('top', array(
            'models' => $UsersToView,
            'pages' => $pages,
            'dependency' => $dependency,
        ));
    }
}