<?php
Yii::import('application.behaviors.menu.menu.*');
/**
 * Class MenuBehaviors Класс для построения меню
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 14.06.13
 * Time: 19:42
 * To change this template use File | Settings | File Templates.
 *
 * @package application.behaviors.menu
 */
class MenuBehaviors extends CBehavior
{
    /**
     *
     */
    public function __construct()
    {
        $this->init();
    }

    /** @var array  */
    private $menuShowArray = array(
        'user'      => array('*' => 'run'),
        'rating'    => array('*' => 'run'),
        'event'     => array('*' => 'run'),
        'moder'     => array('*' => 'run'),
        'friend'    => array('*' => 'profile'),
        'guest'     => array('*' => 'run'),
        'subscribe' => array('*' => 'run'),
        'community' => array('*' => 'run'),
    );

    /**
     *
     */
    public function init()
    {
    }

    private $_action;
    private $_controller;
    private $_module;

    /**
     * @param CAction $action
     */
    public function createMenu($action){
        $this->_action = $action->id;
        $this->_controller = $action->controller->id;
        $this->_module = null;
        if(null !== $action->controller->module)
            $this->_module = $action->controller->module->id;

        foreach($this->menuShowArray as $menu => $params) {
            $menuName = $menu.'Menu';
            $className = ucfirst($menu).'Menu';
            foreach($params as $paramAction => $method) {
                if($paramAction == '*') {
                    $class = new $className();
                    $action->controller->$menuName = call_user_func(array($class, $method));
                } else {
                    $flag = true;
                    $route = explode('.', $paramAction);
                    if(count($route) == 3) {
                        $flag = $route[2] == '*' || $route[2] == $this->_action;
                        if($flag)
                            $flag = $route[1] == '*' || $route[1] == $this->_controller;
                        if($flag)
                            $flag = $route[0] == '*' || $route[0] == $this->_module;
                    }
                    if($flag) {
                        $class = new $className();
                        $action->controller->$menuName = call_user_func(array($class, $method));
                        break;
                    }
                }
            }
        }
    }
}