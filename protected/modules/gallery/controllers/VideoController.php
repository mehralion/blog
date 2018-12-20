<?php
namespace application\modules\gallery\controllers;
/**
 * Class VideoController
 *
 * @method \CAction add()
 * @method \CAction update()
 * @method \CAction delete()
 * @method \CAction show()
 * @method \CAction index()
 * @method \CAction top()
 * @method \CAction reset()
 *
 * @package application.gallery.controllers
 */
class VideoController extends \FrontController
{
    public $ownCategory = true;
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
                'actions'=>array('add', 'update', 'delete', 'reset'),
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'add'   =>'\application\modules\gallery\actions\video\Add',
            'update'=>'\application\modules\gallery\actions\video\Update',
            'delete'=>'\application\modules\gallery\actions\video\Delete',
            'show'  =>'\application\modules\gallery\actions\video\Show',
            'index' =>array(
                'class' => '\application\modules\gallery\actions\video\Index',
                'ownProfile' => true
            ),
            'top'   =>'\application\modules\gallery\actions\video\Top',
            'reset' =>'\application\modules\gallery\actions\video\Reset',
            'preview' =>'\application\modules\gallery\actions\video\Preview',
            'trunc' =>'\application\modules\gallery\actions\video\Trunc',
        );
    }
}