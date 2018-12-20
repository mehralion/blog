<?php
return CMap::mergeArray(
// наследуемся от main.php
    require(dirname(__FILE__).'/main.php'),
    array(
        'components' => array(
            'db' => array(
                'connectionString' => 'mysql:host=localhost;dbname=blogoldbk',
                'emulatePrepare' => true,
                'username' => 'blogoldbk',
                'password' => 'Cif6w4_1',
                'charset' => 'utf8',
                'tablePrefix'=>'',
                'enableParamLogging' => true,
                'enableProfiling' => true,
                'schemaCachingDuration' => 1200,
            ),
            'image' => array(
                'class' => 'application.extensions.image.CImageComponent',
                'driver' => 'GD',
            ),
            'session' => array (
                'class' => 'CCacheHttpSession',
                'sessionName' => 'blog_oldbk',
                'timeout' => 1440,
                'cookieMode' => 'allow',
                'cookieParams' => array(
                    'path' => '/',
                    'domain' => '.blogoldbk.nnick.ru',
                    'httpOnly' => true,
                ),
            ),
        )
    )
);
?>