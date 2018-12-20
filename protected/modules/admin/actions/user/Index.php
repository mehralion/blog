<?php
namespace application\modules\admin\actions\user;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.post.actions.index
 */
class Index extends \CAction
{
    public function run()
    {
        $this->controller->render('index');
    }
}