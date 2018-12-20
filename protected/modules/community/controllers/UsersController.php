<?php
namespace application\modules\community\controllers;

class UsersController extends \FrontController
{
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array('deny',
                'actions'=>array(
                    'index', 'page', 'update', 'moders', 'invite', 'request', 'delete_user', 'delete_moder', 'delete_invite', 'delete_request',
                    'get', 'accept_request'
                ),
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'index'  => '\application\modules\community\actions\users\Index',
            'page'   => '\application\modules\community\actions\users\Page',
            'update' => '\application\modules\community\actions\users\Update',
            'moders' => '\application\modules\community\actions\users\Moders',
            'invite' => '\application\modules\community\actions\users\Invite',
            'request' => '\application\modules\community\actions\users\Request',
            'delete_user' => '\application\modules\community\actions\users\DeleteUser',
            'delete_moder' => '\application\modules\community\actions\users\DeleteModer',
            'delete_invite' => '\application\modules\community\actions\users\DeleteInvite',
            'delete_request' => '\application\modules\community\actions\users\DeleteRequest',
            'accept_request' => '\application\modules\community\actions\users\AcceptRequest',
            'get' => '\application\modules\community\actions\users\Get',
        );
    }
}