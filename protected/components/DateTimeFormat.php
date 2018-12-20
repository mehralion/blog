<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 20.11.13
 * Time: 5:38
 */

class DateTimeFormat
{
    public static function format($pattern = null,$time = null)
    {
        if($pattern === null)
            $pattern = Yii::app()->params['timeDb'];
        if($time === null)
            $time = time();

        return Yii::app()->dateFormatter->format($pattern, $time);
    }
} 