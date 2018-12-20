<?php
namespace application\modules\post\controllers;
/**
 * Class ProfileController
 *
 * @package application.post.controllers
 */
class ProfileController extends \FrontController
{
    public function filters()
    {
        return array(
            'accessControl',
            'ajaxOnly + update, add, tags',
        );
    }

    public function accessRules()
    {
        return array(
            array('deny',
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'index' => '\application\modules\post\actions\profile\Index',
            'update'=> '\application\modules\post\actions\profile\Update',
            'tags'  => '\application\modules\post\actions\profile\Tags',
            'add'   => '\application\modules\post\actions\profile\Add',
            'delete'=> '\application\modules\post\actions\profile\Delete',
            'reset' => '\application\modules\post\actions\profile\Reset',
            'trunc' => '\application\modules\post\actions\profile\Trunc',
        );
    }
}