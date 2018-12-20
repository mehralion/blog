<?php

Yii::import('application.models._base.BaseSmiles');

class Smiles extends BaseSmiles
{
    const ACCESS_PUBLIC = 1;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}