<?php
namespace application\modules\post\controllers;
/**
 * Class IndexController
 *
 * @package application.post.controllers
 */
class IndexController extends \FrontController
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
        );
    }

    public function actions()
    {
        return array(
            'index'      => '\application\modules\post\actions\index\Index',
            'show'       => '\application\modules\post\actions\index\Show',
            'top'        => '\application\modules\post\actions\index\Top',
            'most'       => '\application\modules\post\actions\index\Most',
            'listrate'   => '\application\modules\post\actions\index\ListRate',
        );
    }
}