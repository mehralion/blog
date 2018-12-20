<?php
namespace application\modules\subscribe\actions\index;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
 */
class Debate extends \CAction
{
    public function run()
    {
        $Subscribe = new \SubscribeDebate('search');
        $Subscribe->unsetAttributes();

        if(isset($_REQUEST['SubscribeDebate']))
            $Subscribe->setAttributes($_REQUEST['SubscribeDebate']);

        $Subscribe->subscribe_user_id = \Yii::app()->user->id;
        $Subscribe->is_deleted = 0;
        $this->controller->render('debate', array(
            'model' => $Subscribe
        ));
    }
}