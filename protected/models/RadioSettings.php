<?php

Yii::import('application.models._base.BaseRadioSettings');

class RadioSettings extends BaseRadioSettings
{
    /**
     * @param string $className
     * @return RadioSettings
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public $hour_cost;
    public $shtraf_cost;
    public $coff_for_hour;
    public $hour_for_coff;

    public $settings = array(
        'hour_cost',
        'shtraf_cost',
        'coff_for_hour',
        'hour_for_coff'
    );

    public function rules() {
        return array(
            array('name, value', 'required'),
            array('name, value', 'length', 'max'=>255),
            array('id, name, value', 'safe', 'on'=>'search'),
        );
    }

    public function attributeLabels() {
        return array(
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'value' => Yii::t('app', 'Value'),
            'hour_cost' => Yii::t('app', 'Цена за час'),
            'shtraf_cost' => Yii::t('app', 'Цена за штраф'),
            'coff_for_hour' => Yii::t('app', 'Кофф подъема'),
            'hour_for_coff' => Yii::t('app', 'Кол-во часов для подъема коэфициента'),
        );
    }

    /**
     * @param $name
     * @return int|mixed
     */
    public static function getKey($name)
    {
        return array_search($name, self::model()->settings) !== false ? array_search($name, self::model()->settings) : 0;
    }

    /**
     * @return RadioSettings
     */
    public static function getSettings()
    {
        /** @var RadioSettings[] $models */
        $models = self::model()->findAll();
        foreach($models as $model)
            self::model()->{$model->name} = $model->value;

        return self::model();
    }

    /**
     * @return bool
     */
    public function saveSettings()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('name = :name');

        $error = false;
        $t = Yii::app()->db->beginTransaction();
        try {

            foreach($this->settings as $key => $name) {
                $criteria->params = array(':name' => $name);
                $model = self::model()->find($criteria);
                if($model) {
                    $model->value = $this->{$name};
                    if(!$model->save()) {
                        $error = true;
                        Yii::app()->message->setErrors('danger', $model, $key);
                    }
                }
            }

            if(!$error)
                $t->commit();
            else
                $t->rollback();
        } catch (Exception $ex) {
            $t->rollback();
        }

        return !$error;
    }
}