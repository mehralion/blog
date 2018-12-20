<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnikitchenko
 * Date: 07.08.13
 * Time: 18:37
 * To change this template use File | Settings | File Templates.
 */

Yii::setPathOfAlias('themePath',dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.'new');
Yii::setPathOfAlias('galleryModule',dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.'new'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'gallery');
Yii::setPathOfAlias('postModule',dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.'new'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'post');
Yii::setPathOfAlias('ajax',dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.'new'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR.'ajax');
Yii::setPathOfAlias('xupload', dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'extensions'.DIRECTORY_SEPARATOR.'xupload-0.5.1');