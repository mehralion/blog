<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 23.10.12
 * Time: 14:04
 * To change this template use File | Settings | File Templates.
 */
class Online
{
    public static function addUser()
    {
        $Session = null;
        if(preg_match('/kishinev_md=(.*?)(?:;|$)/ui', $_SERVER['HTTP_COOKIE'], $out)){
            $Session = UserOnline::model()->find('session_value = :value OR user_id = :id', array(
                ':value' => $out[1],
                ':id' => Yii::app()->user->id
            ));

            if($Session === null){
                $Session = new UserOnline();
                $Session->session_value = $out[1];
                $Session->login = Yii::app()->user->getLogin();
                $Session->user_id = Yii::app()->user->id;
            }
            $Session->visited = date(Yii::app()->params['dbDateFormat'], time());
            return $Session->save();
        } else
            return false;
    }
}
