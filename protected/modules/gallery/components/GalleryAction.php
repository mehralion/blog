<?php
namespace application\modules\gallery\components;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 04.06.13
 * Time: 17:51
 * To change this template use File | Settings | File Templates.
 *
 * @property \Controller $controller
 *
 * @package application.components.base
 */
class GalleryAction extends \CAction
{
    public $communityId = 0;
    public $isCommunity = 0;
    public $viewName = null;
    public $userId = null;

    public $successLinkRoute = null;
    public $successLinkParams = array();
}