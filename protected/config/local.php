<?php

return CMap::mergeArray(
// наследуемся от main.php
    require(dirname(__FILE__).'/main.php'),
    array(
        'components' => array(
            'db' => array(
                'connectionString' => 'mysql:host=localhost;dbname=blog_oldbk',
                'emulatePrepare' => true,
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8',
                'tablePrefix'=>'',
                'enableParamLogging' => true,
                'enableProfiling' => true,
                'schemaCachingDuration' => 1200,
            ),
            /*'image' => array(
                'class' => 'application.extensions.image.CImageComponent',
                'driver' => 'GD',
            ),*/
            'session' => array (
                'class' => 'CHttpSession',
                'sessionName' => 'blogoldbk',
                'timeout' => 1440,
                'cookieMode' => 'allow',
                'cookieParams' => array(
                    'domain' => 'blog.oldbk.loc',
                    'httpOnly' => true,
                ),
            ),
            /*'cache'=>array(
                'class' => 'system.caching.CMemCache',
                'servers'=>array(
                    array('host'=>'localhost', 'port'=>11211),
                ),
            ),*/
            'cache'=>array(
                'class' => 'CFileCache',
            ),
        )
    )
);