<?php
namespace application\modules\post\actions\profile;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.post.actions.profile
 */
class Tags extends \CAction
{
    public function run()
    {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');
        if(isset($_GET['tag'])){
            $criteria=new \CDbCriteria(array(
                'limit' => 10
            ));
            $criteria->addSearchCondition('title', $_GET['tag']);
            $tags = \Tag::model()->findAll($criteria);
            $this->controller->renderPartial('tags', array('tags' => $tags), false, false);
        }
    }
}