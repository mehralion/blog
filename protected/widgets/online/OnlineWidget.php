<?php
/**
 * Class PollWidget
 *
 * @package application.widgets
 *
 * @property FrontController $controller
 */
class OnlineWidget extends CWidget
{

    public function init()
    {

    }

    public function run()
    {
        if(!Yii::app()->user->isAdmin())
            return;

        $count = 0;
        $users = [];
        $guests = 0;

        $Online = Yii::app()->cache->get('online');
        if($Online !== false) {
            foreach ($Online as $key => $item) {
                if(time() - $item['update_at'] > 60 * 5)
                    continue;

                if($item['login'] == 'guest')
                    $guests++;
                else {
                    $users[] = [
                        'login' => $item['login'],
                        'ip_address' => $item['ip_address']
                    ];
                    $count++;
                }
            }
        }

        $this->render('index', ['count' => $count, 'guests' => $guests, 'users' => $users]);
    }
}