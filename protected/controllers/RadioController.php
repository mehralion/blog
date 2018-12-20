<?php
/** * *
 * @package application.controllers
 */
class RadioController extends FrontController
{
    public function actionIndex()
    {
        $page = Yii::app()->curl->run('http://capitalcity.oldbk.com/blog_dj.php?key=I9RdXHeFYNlufui3TrRZ38U8');

        $return = array(
            1 => array(),
            2 => array()
        );
        $rusEfir = array();
        $oldEfir = array();

        foreach(CJSON::decode($page) as $user) {
            $user['login'] = iconv('cp1251', 'utf-8', urldecode($user['login']));
            $user['clan'] = iconv('cp1251', 'utf-8', urldecode($user['klan']));
            if($user['in_efir']) {
                if($user['id_radio'] == 1)
                    $rusEfir[] = array(
                        'id' => $user['id'],
                        'login' => User::getLogin($user['align'], $user['klan'], $user['login'], $user['level'], $user['id']),
                        'klan' => $user['klan'],
                        'align' => $user['align'],
                        'level' => $user['level'],
                        'in_efir' => $user['in_efir'],
                        'icq' => User::buildIcq($user['icq']),
                        'skype' => User::buildSkype($user['skype']),
                    );
                else
                    $oldEfir[] = array(
                        'id' => $user['id'],
                        'login' => User::getLogin($user['align'], $user['klan'], $user['login'], $user['level'], $user['id']),
                        'klan' => $user['klan'],
                        'align' => $user['align'],
                        'level' => $user['level'],
                        'in_efir' => $user['in_efir'],
                        'icq' => User::buildIcq($user['icq']),
                        'skype' => User::buildSkype($user['skype']),
                    );
            } else
                $return[$user['id_radio']][] = array(
                    'id' => $user['id'],
                    'login' => User::getLogin($user['align'], $user['klan'], $user['login'], $user['level'], $user['id']),
                    'klan' => $user['klan'],
                    'align' => $user['align'],
                    'level' => $user['level'],
                    'in_efir' => $user['in_efir'],
                    'icq' => User::buildIcq($user['icq']),
                    'skype' => User::buildSkype($user['skype']),
                );
        }

        $this->render('index', array('users' => $return, 'rus' => $rusEfir, 'old' => $oldEfir));
    }

    public function actionRus()
    {
        $this->renderPartial('rus');
    }

    public function actionOld()
    {
        $this->renderPartial('old');
    }
}
