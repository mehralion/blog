<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Nick Nikitchenko
 * Skype: quietasice
 * E-mail: quietasice123@gmail.com
 * Date: 04.07.13
 * Time: 17:38
 * To change this template use File | Settings | File Templates.
 *
 * @package application.widgets.itembuttons
 */
class ItemButtonsWidget extends CWidget
{
    /** @var null | Post | GalleryImage | GalleryVideo */
    public $model = null;

    public $editLink = null;
    public $deleteLink = null;
    public $reportLink = null;
    public $deleteModerLink = null;
    public $logoutLink = null;

    public $quote = false;
    
    public $owner_id = null;

    public $edit = false;
    public $delete = false;
    public $report = false;
    public $moderDelete = false;
    public $logout = false;

    public $deleteText = '';

    public function run()
    {
        if(Yii::app()->user->isGuest)
            return;
        foreach($this->model->rights->getAvailableButtons($this->owner_id) as $name => $value)
            $this->{$name} = $value;

        $this->render('index', array(
            'model' => $this->model
        ));
    }
}