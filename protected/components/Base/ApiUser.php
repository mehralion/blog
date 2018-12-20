<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnikitchenko
 * Date: 14.08.13
 * Time: 15:51
 * To change this template use File | Settings | File Templates.
 */

class ApiUser
{
    public static function add($info)
    {
        $info['login'] = mb_convert_encoding(urldecode($info['login']), 'utf-8', 'windows-1251');
        $info['klan'] = mb_convert_encoding(urldecode($info['klan']), 'utf-8', 'windows-1251');
        /** @var User $User */
        $User = User::model()->find('game_id = :game_id', array(
            ':game_id' => $info['id'],
        ));

        //var_dump($info);die;
        if(!isset($User))
            $User = new User();

        $User->login = $info['login'];
        $User->level = $info['level'];
        $User->align = $info['align'];
        $User->game_id = $info['id'];
        $User->clan = $info['klan'];
		$User->last_update = date('Y-m-d H:i:s', time());

        /** Если в блоке */
        if(isset($info['block']) && $info['block'] == '1')
            $User->is_blocked = 1;
        else
            $User->is_blocked = 0;

        $t = null;
        if(Yii::app()->db->getCurrentTransaction() === null)
            $t = Yii::app()->db->beginTransaction();

        $error = false;
        //echo '<pre>';
        //var_dump($User->getAttributes());die;
        try {
            if(!$User->save(false)) {
                $error = true;
                Yii::app()->message->setErrors('danger', $User);
            }

            $UserProfile = UserProfile::model()->find('user_id = :user_id', array(
                ':user_id' => $User->id
            ));
            if(!isset($UserProfile)) {
                $UserProfile = new UserProfile();
                $UserProfile->user_id = $User->id;

                if(!$UserProfile->save(false)) {
                    $error = true;
                    Yii::app()->message->setErrors('danger', $UserProfile);
                }
            }

            if(!$error) {
                if($t !== null)
                    $t->commit();
                return $User->game_id;
            } else {
                if($t !== null)
                    $t->rollback();
                return false;
            }

        } catch (Exception $ex) {
            if($t !== null)
                $t->rollback();
            MyException::log($ex);
            //var_dump($ex->getMessage());die;
        }

        return false;
    }

    private static $_solt = 'I9RdXHeFYNlufui3TrRZ38U8';
    private static $_apiUrl = 'http://capitalcity.oldbk.com/blog_form.php';
    private static $_apiUrlCheck = 'http://capitalcity.oldbk.com/blog_check.php';
    public static function checkUser($login, $password, $game_id = null, $api = false)
    {
        $curl = Yii::app()->curl;
        $curl->run('https://oldbk.com');

        if(null === $game_id)
            $result = $curl->run(self::$_apiUrl, false, array(
                'login' => urlencode(iconv('utf8', 'windows-1251', $login)),
                'password' => urlencode(iconv('utf8', 'windows-1251', $password)),
                'key' => self::$_solt
            ));
        else
            $result = $curl->run(self::$_apiUrlCheck, false, array(
                'game_id' => $game_id,
                'key' => self::$_solt
            ));

        if($result === false)
            return false;

        $info = CJSON::decode($result);
        if(isset($info['answ']) || !isset($info['id']) || !isset($info['login']) || (isset($info['login']) && trim($info['login']) == ''))
            return false;

        $userGameId = ApiUser::add($info);
        if(false !== $userGameId)
            return $userGameId;
        else
            return false;
    }
}