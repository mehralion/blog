<?php

/**
 * Class Tag
 *
 * @package application.tag.models
 */
class Tag extends BaseTag
{
    /**
     * @param string $className
     * @return Tag
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    /**
     * @param $limit
     * @return array
     */
    public function findTagWeights($limit)
    {
        $return = array();
        $criteria = new CDbCriteria();
        $criteria->limit = $limit;
        $criteria->order = 'count desc';
        $criteria->addCondition('count >= :count');
        $criteria->params = array(
            ':count' => Yii::app()->params['tagUse']
        );
        /** @var Tag $models */
        $models = $this->findAll($criteria);
        foreach($models as $model)
            $return[$model->title] = $model->count;

        return $return;
    }
}