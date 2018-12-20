<?php
namespace application\modules\gallery\controllers;
/**
 * Class ImageController
 *
 * @package application.gallery.controllers
 *
 * @method \CAction add()
 * @method \CAction update()
 * @method \CAction updatelist()
 * @method \CAction show()
 * @method \CAction index()
 * @method \CAction top()
 * @method \CAction delete()
 * @method \CAction reset()
 */
class ImageController extends \FrontController
{
    public $ownCategory = true;
    public function filters()
    {
        return array(
            'accessControl',
            'ajaxOnly + update',
        );
    }

    public function accessRules()
    {
        return array(
            array('deny',
                'actions'=>array('add', 'update', 'delete', 'updatelist', 'reset', 'index', 'upload'),
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'upload'       => '\application\modules\gallery\actions\image\Add',
            'update'    => '\application\modules\gallery\actions\image\Update',
            'updatelist'=> '\application\modules\gallery\actions\image\UpdateList',
            'show'      => '\application\modules\gallery\actions\image\Show',
            'index'     => array(
                'class' => '\application\modules\gallery\actions\image\Index',
                'ownProfile' => true
            ),
            'top'       => '\application\modules\gallery\actions\image\Top',
            'delete'    => '\application\modules\gallery\actions\image\Delete',
            'reset'     => '\application\modules\gallery\actions\image\Reset',
            'preview'     => '\application\modules\gallery\actions\image\Preview',
            'trunc'     => '\application\modules\gallery\actions\image\Trunc',
        );
    }
}