<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 23.10.12
 * Time: 14:59
 * To change this template use File | Settings | File Templates.
 *
 * @package application.components.base
 */
class Base
{
    /**
     * @param $model
     * @param null $attributes
     * @param bool $input
     * @param bool $return
     * @return bool|string
     */
    public static function ajaxValidate($model, $attributes = null, $input = true, $return = false)
    {
        $tmpArray = self::validate($model, $attributes, $input);
        if(!CJSON::decode($tmpArray)) {
            return true;
        } else {
            if(!$return) {
                echo CJSON::encode(array('error' => CJSON::decode($tmpArray)));
                Yii::app()->end();
            } else
                return $tmpArray;
        }
    }

    /**
     * @param $models
     * @param null $attributes
     * @param bool $loadInput
     * @return string
     */
    private static function validate($models, $attributes=null, $loadInput=true)
    {
        $result=array();
        if(!is_array($models))
            $models=array($models);
        foreach($models as $model)
        {
            if ($loadInput && isset($_POST[get_class($model)]))
                $model->attributes = $_POST[get_class($model)];
            elseif(!is_null($attributes))
                $model->attributes = $attributes;
            $model->validate();
            foreach($model->getErrors() as $attribute=>$errors)
                $result[CHtml::activeId($model,$attribute)]=$errors;
        }
        return function_exists('json_encode') ? json_encode($result) : CJSON::encode($result);
    }

    /**
     * @param int $length
     * @return string
     */
    public static function generatePassword($length = 10)
    {
        $chars = '1234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $i = 0;
        $password = '';
        while ($i <= $length) {
            $password .= $chars{mt_rand(0, strlen($chars) - 1)};
            $i++;
        }
        return $password;
    }

    /**
     * @param $value
     * @return bool
     */
    public static function setCookie($value)
    {
        if (Yii::app()->request->cookies[$value] === null) {
            // создаем объект класса для работу с куками и устанавливаем переменную count  в true
            $cookie = new CHttpCookie($value, true);
            //устанавливаем время жизни куков, в моем случае 2 часа
            $curr = explode(':',date('H:i:s', time()));
            $dayEnd = (3600 * 24) - ($curr[0]*3600+$curr[1]*60+$curr[2]);
            $cookie->expire = time() + $dayEnd;
            //$cookie->expire = 0;
            // в качестве пути для куков указываем относительный url текущей страницы
            $cookie->path = '/';
            // получаем значение переменной куков count
            Yii::app()->request->cookies[$value] = $cookie;
            return true;
        } else
            return false;
    }
}
