<?php
Yii::import('application.models._base.BaseRapUrl');
/**
 * Class RapUrl
 *
 * @property integer $type
 */
class RapUrl extends BaseRapUrl
{
    const TYPE_IGNORE   = 1;
    const TYPE_RAP      = 2;
    const TYPE_CLANS    = 3;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}