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
                'password' => 'root',
                'charset' => 'utf8',
                'tablePrefix'=>'',
                'enableParamLogging' => true,
                'enableProfiling' => true,
                'schemaCachingDuration' => 1200,
            ),
             /*'db' => array(
                'connectionString' => 'mysql:host=25.71.53.119;dbname=blog_oldbk',
                'emulatePrepare' => true,
                'username' => 'work',
                'password' => 'megladon',
                'charset' => 'utf8',
                'tablePrefix'=>'',
                'enableParamLogging' => true,
                'enableProfiling' => true,
                'schemaCachingDuration' => 1200,
            ),*/

            'image' => array(
                'class' => 'application.extensions.image.CImageComponent',
                'driver' => 'GD',
            ),
            'session' => array (
                'class' => 'CHttpSession',
                'sessionName' => 'blog_oldbk',
                'timeout' => 1440,
                'cookieMode' => 'allow',
                'cookieParams' => array(
                    'path' => '/',
                    'domain' => '.blog.oldbk.loc',
                    'httpOnly' => true,
                ),
            ),
            'search' => array(
                'class' => 'ext.DGSphinxSearch.DGSphinxSearch',
                'server' => '127.0.0.1',
                'port' => 3312,
                'maxQueryTime' => 3000,
                'enableProfiling'=>0,
                'enableResultTrace'=>0,
                'fieldWeights' => array(
                    'name' => 10000,
                    'keywords' => 100,
                ),
            ),
        )
    )
);
?>