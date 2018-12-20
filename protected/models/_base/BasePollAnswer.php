<?php

/**
 * This is the model base class for the table "pool_answer".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "PoolAnswer".
 *
 * Columns in table "pool_answer" available as properties of the model,
 * and there are no model relations.
 *
 * @property integer $id
 * @property integer $pool_id
 * @property string $title
 * @property integer $value
 *
 */
abstract class BasePollAnswer extends MyAR {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'poll_answer';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'PoolAnswer|PoolAnswers', $n);
	}

	public static function representingColumn() {
		return 'title';
	}

	public function rules() {
		return array(
			array('poll_id, title', 'required'),
			array('poll_id, value', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
			array('value', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, pool_id, title, value', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'pool_id' => Yii::t('app', 'Pool'),
			'title' => Yii::t('app', 'Title'),
			'value' => Yii::t('app', 'Value'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('pool_id', $this->pool_id);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('value', $this->value);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}