<?php
namespace application\modules\community\components;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 04.06.13
 * Time: 17:51
 * To change this template use File | Settings | File Templates.
 *
 * @package application.components.base
 */
class CommunityAction extends \CAction
{
    /**
     * Constructor.
     * @param \CController $controller the controller who owns this action.
     * @param string $id id of the action.
     */
    public function __construct($controller,$id)
    {
        $controller->layout = 'community/community';
        parent::__construct($controller, $id);
    }
}