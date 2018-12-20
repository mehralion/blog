<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 21.06.13
 * Time: 16:35
 * To change this template use File | Settings | File Templates.
 *
 * @package application.models
 */
abstract class MyAR extends GxActiveRecord
{
    /**
     * @param string $className
     * @return MyAR
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return array(
            'application.behaviors.models.TagsBehavior'
        );
    }
}