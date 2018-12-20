<?php
/**
 * Class MenuWidget
 *
 * @package application.widgets.menu
 *
 * @property FrontController $controller
 */
class MenuWidget extends CWidget
{
    public $main         = false;
    public $user         = false;
    public $rating       = false;
    public $event        = false;
    public $friend       = false;
    public $moder        = false;
    public $guest        = false;
    public $subscribe    = false;
    public $community    = false;
    public $advert       = false;

    public function init()
    {
        // this method is called by CController::beginWidget()
    }

    public function run()
    {
        $this->render('index', array(
            'friendMenu'     => $this->controller->friendMenu,
            'mainMenu'       => $this->controller->mainMenu,
            'userMenu'       => $this->controller->userMenu,
            'ratingMenu'     => $this->controller->ratingMenu,
            'eventMenu'      => $this->controller->eventMenu,
            'moderMenu'      => $this->controller->moderMenu,
            'guestMenu'      => $this->controller->guestMenu,
            'subscribeMenu'  => $this->controller->subscribeMenu,
            'communityMenu'  => $this->controller->communityMenu,
        ));
    }
}