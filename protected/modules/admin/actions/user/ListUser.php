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
class ListUser extends \CAction
{
    public function run()
    {
        if(\Yii::app()->request->isAjaxRequest && isset($_GET['q']))
        {
            /* q is the default GET variable name that is used by
            / the autocomplete widget to pass in user input
            */
            $name = $_GET['q'];
            // this was set with the "max" attribute of the CAutoComplete widget
            $limit = min($_GET['limit'], 50);
            $criteria = new \CDbCriteria;
            $criteria->condition = "login LIKE :sterm";
            $criteria->params = array(":sterm"=>"%$name%");
            $criteria->limit = $limit;
            $userArray = \User::model()->findAll($criteria);
            $returnVal = '';
            foreach($userArray as $userAccount)
            {
                $returnVal .= $userAccount->getAttribute('login').'|'
                    .$userAccount->getAttribute('id')."\n";
            }
            echo $returnVal;
        }
    }
}