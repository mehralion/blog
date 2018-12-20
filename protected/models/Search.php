<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 10.08.13
 * Time: 21:54
 * To change this template use File | Settings | File Templates.
 */

class Search extends CModel
{
    public $query = null;
    public $inTitle = false;
    public $inComment = false;
    public $inPost = false;
    public $inImage = false;
    public $inVideo = false;

    public $searchFlag = 0;

    public function attributeNames()
    {
        return array();
    }

    public function attributeLabels()
    {
        return array(
            'query' => 'Поисковая фраза',
            'inTitle' => 'Включить заголовки',
        );
    }

    public static function getSearchType()
    {
        return array(
            0 => 'Блоги',
            1 => 'Блогеры'
        );
    }


}