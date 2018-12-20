<?php
/**
 * Class LdWidget
 *
 * @package application.user.widgets.ld
 */
class LdWidget extends CWidget
{
	public function run()
	{
        if(!Yii::app()->user->isModer())
            return;

        $criteria = new CDbCriteria();
        $criteria->with = array(
            'post',
            'image',
            'video',
            'comment',
            'user',
            'moder',
            'owner',
            'audio'
        );
        $criteria->addCondition('`t`.user_owner_id  = :user_id');
        $criteria->params = array(
            ':user_id' => Yii::app()->userOwn->id,
        );
        $criteria->order = '`t`.create_datetime desc';

        $dependency = new CDbCacheDependency('SELECT max(update_datetime) FROM moder_log');
        $dependency->reuseDependentData = true;

        $pages = new CPagination(ModerLog::model()
            ->cache(1, $dependency)
            ->count($criteria));
        $pages->pageSize = Yii::app()->params['page_size']['ld'];
        $pages->applyLimit($criteria);
        /** @var ModerLog[] $data */
        $data = array();
        /** @var ModerLog $model */
        foreach(ModerLog::model()
                    ->cache(1, $dependency)
                    ->findAll($criteria) as $model) {
            $Ld = new LdModel();
            $Ld->attributes = $model->attributes;
            $Ld->moder_reason = $model->moder_reason;
            $Ld->id = $model->id;
            $Ld->user = Yii::app()->userOwn;
            $Ld->moder = $model->moder;
            $Ld->post = $model->post;
            $Ld->image = $model->image;
            $Ld->video = $model->video;
            $Ld->comment = $model->comment;
            $Ld->silence_id = $model->silence_id;
            $Ld->operation_type = $model->operation_type;
            $Ld->datetime = date(Yii::app()->params['siteTimeFormat'], strtotime($model->create_datetime));
            $data[] = $Ld;
        }

        $this->render('index', array(
            'dataProvider' => new CArrayDataProvider($data, array('keyField'=>false)),
            'pages' => $pages
        ));
	}
}