<?php
namespace application\modules\community\controllers;

class ProfileController extends \FrontController
{
    public $layout = 'community/profile';

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
                'actions'=>array('settings', 'create', 'own', 'reset', 'trunc', 'own', 'incommunity', 'request', 'invite', 'delete'),
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'own'            => '\application\modules\community\actions\profile\Own',
            'incommunity'    => '\application\modules\community\actions\profile\InCommunity',
            'request'        => '\application\modules\community\actions\profile\Request',
            'invite'         => '\application\modules\community\actions\profile\Invite',
            'settings'       => '\application\modules\community\actions\profile\Settings',
            'create'         => '\application\modules\community\actions\profile\Create',
            'delete'         => '\application\modules\community\actions\profile\Delete',
            'reset'          => '\application\modules\community\actions\profile\Reset',
            'trunc'          => '\application\modules\community\actions\profile\Trunc',
        );
    }
}