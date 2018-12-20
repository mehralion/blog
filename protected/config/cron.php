<?php

return array(
    // У вас этот путь может отличаться. Можно подсмотреть в config/main.php.
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Cron',
    'preload' => array('log'),
    'import'=>array(
        'application.helpers.*',
        'application.models._base.*',
		'application.models.*',
		'application.models.cache.*',
		'application.models.commentItem.*',
		'application.models.eventComment.*',
		'application.models.eventItem.*',
		'application.models.EventViewDatetime.*',
		'application.models.galleryAlbum.*',
		'application.models.itemInfo.*',
		'application.models.moderLog.*',
		'application.models.post.*',
		'application.models.ratingItem.*',
		'application.models.report.*',
		'application.models.subscribe.*',
		'application.models.subscribeDebate.*',
		'application.models.radio.*',
		'application.models.rights.*',
		'application.models.log.*',
        'application.components.*',
        'application.extensions.*', // giix components
        'ext.giix-components.*', // giix components
        'application.components.Base.*',
        'application.components.Radio.*',
        'application.extensions.yii-mail.*',
        'application.behaviors.menu.*',
        'application.behaviors.models.*',
    ),
    // Перенаправляем журнал для cron-а в отдельные файлы
    'components' => array(
        'rvs' =>array(
            'class' => 'application.components.RVS',
        ),
        'radio' =>array(
            'class' => 'application.components.Radio.RadioInfo',
            'host' => '127.0.0.1',
            'port' => 8000,
            'login' => 'admin',
            'password' => 'Gnngzwctk013RKieTZiG'
        ),
        'curl' =>array(
            'class' => 'application.extensions.curl.Curl',
            //you can setup timeout,http_login,proxy,proxylogin,cookie, and setOPTIONS
        ),
        'message' =>array(
            'class' => 'application.components.Base.ajax.Message',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'cron.log',
                    'levels' => 'error, warning',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'cron_trace.log',
                    'levels' => 'trace',
                ),
            ),
        ),
        // Соединение с СУБД
        'db' => array(
            //'connectionString' => 'mysql:host=blogdb.c4c2zvyoc0zt.eu-west-1.rds.amazonaws.com;dbname=blogdb',
			//'username' => 'blog',
            //'password' => 'Gdn8dke19bb',
			'connectionString' => 'mysql:host=88.198.205.122;dbname=blogdb',
			'username' => 'blogdb',
            'password' => 'zmtbqYeIRZaNwjqyOIio',
            'emulatePrepare' => true,
            'charset' => 'utf8',
            'enableParamLogging' => true,
            'enableProfiling' => true,
            'tablePrefix'=>'',
            'schemaCachingDuration' => 1200
        ),
    ),
);
?>