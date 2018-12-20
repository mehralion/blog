<?php

Yii::import('application.models._base.BaseRadio');

/**
 * Class Radio
 *
 * @property integer is_send_link
 * @property float sum_hours
 *
 * @property User user
 */
class Radio extends BaseRadio
{
    const MAX_ERROR_COUNT = 3;

    const FINISH_CAPTCHA_LIMIT   = 0; //Лимит по каптче
    const FINISH_NO_CHECK        = 1; //Не открыл проверку урла
    const FINISH_OFFLINE         = 2; //Вырубился в офф
    const FINISH_SUCCESS         = 3; //Закончил вещание

    const RADIO_FREE_TIME = 10; //Время погрешности в минутах

    const RADIO_TYPE_OLDFM_D = 3;
    const RADIO_TYPE_RUSFM_D = 4;
    const RADIO_TYPE_RUSFM   = 1;
    const RADIO_TYPE_OLDFM   = 2;

    public $validation;
    public $date_start;
    public $date_end;

    public $sum = 0;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function rules() {
        return array(
            array('user_id', 'required'),
            array('user_id, radio_type, finish_type, is_online, captcha_errors', 'numerical', 'integerOnly'=>true),
            array('alias', 'length', 'max'=>255),
            array('start_datetime, end_datetime, last_update_datetime, next_update_datetime, sum_hours', 'safe'),
            array('radio_type, finish_type, is_online, start_datetime, end_datetime, last_update_datetime, next_update_datetime, captcha_errors, alias', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, user_id, radio_type, finish_type, is_online, start_datetime, end_datetime, last_update_datetime, next_update_datetime, captcha_errors, alias', 'safe', 'on'=>'search'),
            array('validation', 'captcha', 'on' => 'captcha'),
            //array('validation', 'application.extensions.recaptcha.EReCaptchaValidator', 'privateKey'=>'6LczH-8SAAAAANWkhb8VJDyl1QckYAcXylgVkdXU', 'on' => 'captcha'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'DJ'),
            'date_start' => Yii::t('app', 'Начало'),
            'date_end' => Yii::t('app', 'Конец'),
            'radio_type' => Yii::t('app', 'Radio Type'),
            'finish_type' => Yii::t('app', 'Finish Type'),
            'is_online' => Yii::t('app', 'Is Online'),
            'start_datetime' => Yii::t('app', 'Start Datetime'),
            'end_datetime' => Yii::t('app', 'End Datetime'),
            'last_update_datetime' => Yii::t('app', 'Last Update Datetime'),
            'next_update_datetime' => Yii::t('app', 'Next Update Datetime'),
            'captcha_errors' => Yii::t('app', 'Captcha Errors'),
            'alias' => Yii::t('app', 'Captcha Alias'),
            'validation'=>Yii::t('demo', 'Введите каптчу'),
        );
    }

    public function relations()
    {
        return array(
            'user' => array(
                self::BELONGS_TO,
                'User',
                'user_id',
                'joinType' => 'inner join',
            ),
        );
    }

    /**
     * Следующее время отправления урла
     *
     * @return string
     */
    public static function getNextUpdate()
    {
        $r = rand(15, 60);
        return date('Y-m-d H:i:s', strtotime('+'.$r.' minutes'));
    }

    /**
     * Получаем минуты погрешности, вдруг куда-то отошел
     *
     * @return int
     */
    public static function getMaxTime()
    {
        return self::RADIO_FREE_TIME * 60;
    }

    public $streams = array(
        Radio::RADIO_TYPE_OLDFM_D => 'oldfm_default',
        Radio::RADIO_TYPE_RUSFM_D => 'rusfm_default',
        Radio::RADIO_TYPE_RUSFM => 'rusfm',
        Radio::RADIO_TYPE_OLDFM => 'oldfm'
    );

    /**
     * @return null
     */
    public function getUserRadio()
    {
        return isset($this->streams[$this->radio_type]) ? $this->streams[$this->radio_type] : null;
    }

    /**
     * @param $type
     * @return null
     */
    public static function getRadioName($type)
    {
        return isset(self::model()->streams[$type]) ? self::model()->streams[$type] : null;
    }

    public function off($finish)
    {
        $this->is_online = 0;
        $this->end_datetime = date('Y-m-d H:i:s', time());
        $this->finish_type = $finish;

        $end = strtotime($this->end_datetime);
        $begin = strtotime($this->start_datetime);
        $this->sum_hours += round(($end - $begin) / 3600, 2);

        Yii::app()->curl->run('http://capitalcity.oldbk.com/friends.php?key=246y426514256135y4315y1&radio='.$this->radio_type);

        \Yii::app()->radio->streamOff($this->radio_type);

        return $this->save();
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('`t`.id', $this->id);
        $criteria->compare('`t`.user_id', $this->user_id);
        $criteria->compare('`t`.radio_type', $this->radio_type);
        $criteria->compare('`t`.finish_type', $this->finish_type);
        $criteria->compare('`t`.is_online', $this->is_online);
        $criteria->compare('`t`.start_datetime', $this->start_datetime, true);
        $criteria->compare('`t`.end_datetime', $this->end_datetime, true);
        $criteria->compare('`t`.last_update_datetime', $this->last_update_datetime, true);
        $criteria->compare('`t`.next_update_datetime', $this->next_update_datetime, true);
        $criteria->compare('`t`.captcha_errors', $this->captcha_errors);
        $criteria->compare('`t`.alias', $this->alias, true);

        $criteria->order = '`t`.start_datetime desc';

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function buildRadio($data) {
        $string = '';
        if($data['rus'])
            $string .= 'Рус';

        if($data['old']) {
            if($string != '')
                $string .= '/';
            $string .= 'Олд';
        }

        return $string;
    }

    /**
     * @param Radio $data
     * @return int
     */
    public static function getEfirTime($data) {
        $start = strtotime($data->start_datetime);
        $end = time();
        if($data->end_datetime !== null)
            $end = strtotime($data->end_datetime);

        $diff = round(($end-$start)/3600, 2);

        return $diff;
    }

    private static $priceTime = array(
        0 => 1,
        50 => 5,
        400 => 8,
        1200 => 10,
        10000 => 12
    );

    /**
     * @param $finish_shtraf Сами выкинули
     * @param $hoursInSession Всего часов за сессию
     * @param $totalHours Всего часов
     * @return int
     */
    public static function getPrice($finish_shtraf, $hoursInSession, $totalHours) {
        $sum = 0;

        /*krsort(self::$priceTime);
        foreach(self::$priceTime as $limit => $cost) {
            if($totalHours > $limit) {
                if($totalHours - $hoursInSession >= 50 || $totalHours <= 50)
                    $sum = $cost * $hoursInSession;
                else { //Считаем для стажера, первые 50ч всегда по 1екр/ч
                    $add = 50 - ($totalHours - $hoursInSession);
                    $sum = 1 * $add;
                    $sum += $cost * ($hoursInSession - $add);
                }
                break;
            }
        }*/
        $sum = $hoursInSession * 2;

        $shtraf = 0;
        if($finish_shtraf) {
            for ($i = 1; $i <= $finish_shtraf; $i++) {
                if($i == 1)
                    $shtraf += 2;
                else
                    $shtraf += (1 + $i / 10) * 2;
            }
        }


        $result = $sum - $shtraf;
        if($result < 0)
            $result = 0;
        return $result;
    }

    /**
     * @param $hours
     * @return int
     */
    public static function getTax($hours) {
        krsort(self::$priceTime);
        foreach(self::$priceTime as $limit => $cost) {
            if($hours > $limit)
                return $cost;
        }

        return 1;
    }
}