<?php
/**
 * Class PreviewController
 *
 * @package application.controllers
 */
class ApiController extends FrontController
{
    private $_authUrl = 'http://capitalcity.oldbk.com/blog_login.php';
    public function actionAuth()
    {
        if(Yii::app()->request->getUrlReferrer() != 'http://capitalcity.oldbk.com/blog_auth.php')
            MyException::ShowError(404,'Страница не найдена');

        Yii::app()->user->logout();
        $uid = Yii::app()->request->getPost('uid');
        $hash = Yii::app()->request->getPost('hash');
        if(!$uid || !$hash)
            MyException::ShowError(404,'Страница не найдена');

        $result = Yii::app()->curl->run($this->_authUrl, false, array(
            'uid' => $uid,
            'hash' => $hash,
        ));
        if($result === false)
            MyException::ShowError(404,'Страница не найдена');
        $info = CJSON::decode($result);
        if(isset($info['answ']))
            MyException::ShowError(404,'Страница не найдена');

        //var_dump($info);die;
        $userGameId = ApiUser::add($info);
        if(false !== $userGameId) {
            /** @var User $User */
            $User = User::model()->find('game_id = :game_id', array(
                ':game_id' => $userGameId
            ));
            $identity = new UserIdentity($User->login, null);
            $identity->authenticate();
            switch ($identity->errorCode) {
                case UserIdentity::ERROR_NONE:
                    $duration = 3600*24;
                    Yii::app()->user->login($identity, $duration);
                    break;
                case UserIdentity::ERROR_USERNAME_INVALID:
                    Yii::app()->user->setFlash('error', 'Нет такого пользователя');
                    break;
                case UserIdentity::ERROR_BLOCK:
                    Yii::app()->user->setFlash('error', 'Персонаж заблокирован');
                    break;
            }
            $this->redirect(Yii::app()->createUrl('/post/index/index'));
        } else
            MyException::ShowError(403,'Возникла ошибка, попробуй позже');
    }

    public function actionIuser()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('`t`.code = :code');
        $criteria->params = [':code' => Yii::app()->request->getPost('hash')];
        /** @var User $User */
        $User = User::model()->find($criteria);
        if(!$User) die('false');

        $User->code = md5(mt_rand() . mt_rand() . mt_rand());
        $User->save(false);

        echo CJSON::encode($User->getAttributes());
    }

    public function actionAdvert()
    {
        if(Yii::app()->user->isGuest) {
            $this->redirect('http://blogadv.oldbk.com/login.html');
            Yii::app()->end();
        }

        /** @var User $User */
        $User = User::model()->findByPk(Yii::app()->user->id);
        if(!$User)
            MyException::ShowError(404, 'Страница не найдена');

        if(!$User->code) {
            $User->code = md5(mt_rand() . mt_rand() . mt_rand());
            $User->save(false);
        }

        $key = '';
        $data = Yii::app()->curl->run('http://blogadv.oldbk.com/api/user/hash', false, array('login' => Yii::app()->user->login));
        if($data !== false && ($data = CJSON::decode($data)))
            $key = $data['key'];

        $this->renderPartial('advert', ['hash'  => $key, 'hash2' => $User->code, 'link' => 'http://blogadv.oldbk.com/api/user/game']);
    }

    public function actionRefresh()
    {
        $gameId = Yii::app()->request->getParam('game_id');
        /** @var \User $User */
        $User = \User::model()->find('game_id = :game_id', array(':game_id' => $gameId));
        if($User) {
            ApiUser::checkUser(null, null, $User->game_id, true);
        }
    }

    public function actionLoto()
    {
        $bk_key = Yii::app()->request->getPost('oldbk_key', null);
        if($bk_key != '29vm8gyuq789agerui67jaer') {
            MyException::ShowError(404, 'Страница не найдена');
        }

        try {
            $items = $post = Yii::app()->request->getPost('Loto', []);
            $json = CJSON::encode(array_merge(array('remote_ip' => Yii::app()->request->getUserHostAddress()), $items));
            MyException::logFile($json, 'loto'.date('d.m.Y H:i:s').'.json');

            $post['create_at_string'] = date('d.m.Y H:i:s', time());
            $post['create_at'] = time();
            $users = [];

            foreach ($items as $kry => $item) {
                $item['item'] = iconv('windows-1251', 'utf-8', $item['item']);
                if(!isset($item['uid']) || !isset($item['item'])) {
                    $item['create_at_string'] = date('d.m.Y H:i:s');
                    $item['create_at'] = time();
                    $item['error_msg'] = 'Некорректный запрос';
                    $item['error_code'] = 100;
                    MyException::logFile(CJSON::encode($item), 'loto_log');

                    continue;
                }

                $criteria = new CDbCriteria();
                $criteria->addCondition('game_id = :game_id');
                $criteria->params = [':game_id' => $item['uid']];
                $User = User::model()->find($criteria);
                if(!$User || ($r = ApiUser::checkUser(null, null, $item['uid'])) === false) {
                    continue;
                }

                $criteria->params = [':game_id' => $r];
                $User = User::model()->find($criteria);

                if(!in_array($item['img'], ['fighttype3.gif', 'batt_repa.gif'])) {
                    $item['img'] = 'sh/'.$item['img'];
                }

                $item['img'] = sprintf('http://i.oldbk.com/i/%s', $item['img']);
                $users[] = [
                    'User' => $User,
                    'loto' => $item
                ];
            }

            if(empty($users)) {
                echo 'falsesozdalsya_user';
                return;
            }

            $criteria = new CDbCriteria();
            $criteria->addCondition('custom = "udacha"');
            $criteria->addCondition('on_top = 1');
            /** @var Post $Post */
            $Post = Post::model()->find($criteria);
            if($Post) {
                $Post->on_top = 0;
                $Post->save();
            }

            $Post = new Post('create');
            $Post->user_id = 2432;
            $Post->title = 'Результаты розыгрыша ежедневной беспроигрышной "Зимней лотереи" за '.date('d.m.Y');
            $Post->is_comment = 1;
            $Post->on_top = 1;
            $Post->description = $this->renderPartial('_loto', ['users' => $users], true);
            $Post->admin_text = 1;
            $Post->custom = 'udacha';
            $Post->view_role = Access::VIEW_ROLE_ALL;
            $Post->update_datetime = date('Y-m-d H:i:s');
            if($Post->create())
                echo 'truesozdalsya'; //echo Yii::app()->createAbsoluteUrl('/post/index/show', ['id' => $Post->id, 'gameId' => 2]);
            else {
                echo 'falsesozdalsya';
            }

        } catch (Exception $ex) {
            echo 'falsesozdalsya';
        }
    }

    public function actionTest()
    {
        $r = Yii::app()->curl->run('http://blog.oldbk.com/api/loto.html', false, [
                'Loto' => [
                    0 => ['uid' => '326', 'item' => 'Кровавое Нападение']
                ],
                'oldbk_key' => '29vm8gyuq789agerui67jaer'
            ]);

        VarDumper::dump($r);die;
    }

    public function actionTest2()
    {
        $arr = [
            'Loto' => [
                0 => ['uid' => '326', 'item' => 'Кровавое Нападение']
            ],
            'oldbk_key' => '29vm8gyuq789agerui67jaer'
        ];
        $content = http_build_query($arr);

        $fp = fsockopen('blog.oldbk.com', 80);
        if ($fp) {
            fwrite($fp, "POST /api/loto.html HTTP/1.0\r\n");
            fwrite($fp, "Host: blog.oldbk.com\r\n");
            fwrite($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
            fwrite($fp, "Content-Length: ".strlen($content)."\r\n");
            fwrite($fp, "Connection: close\r\n");
            fwrite($fp, "\r\n");

            fwrite($fp, $content);
        }
    }
}
