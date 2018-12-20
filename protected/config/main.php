<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

include_once("alias.php"); //include aliases
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Blog',
    'id' => 'blog.oldbk.com',

	// preloading 'log' component
	'preload'=>array('log', 'bootstrap'),

	// autoloading model and component classes
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
		'application.libs.sammaye.mongoyii.*',
        'application.libs.sammaye.mongoyii.validators.*',
        'application.libs.sammaye.mongoyii.behaviors.*',
        'application.libs.sammaye.mongoyii.util.*'
	),
	'modules'=>array(
        'gii'=>array(
			'class' => 'system.gii.GiiModule',
            'password' => '123',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('127.0.0.1', '::1'),
            'generatorPaths'=>array(
                'ext.giix-core', // giix generators
                'bootstrap.gii',
            ),
        ),
        'event' => array(),
        'gallery' => array(),
        'comment' => array(),
        'opros' => array(),
        'post' => array(),
        'user' => array(),
        'friend' => array(),
        'rating' => array(),
        'report' => array(),
        'moder' => array(),
        'trunc' => array(),
        'subscribe' => array(),
        'admin' => array(),
        'poll' => array(),
        'community' => array(),
		'radio' => array(),
    ),
	'defaultController'=>'site',
	'theme'=>'new',
	'language' => 'ru',
    'sourceLanguage' => 'ru',
	// application components
	'components'=>array(
		'stringHelper' => array(
            'class' => 'application.helpers.StringHelper',
        ),
		'elasticsearch' => array(
            'class' => 'application.components.Elasticsearch',
            //'server' => '54.217.224.194',
            'server' => 'localhost',
            'port' => 9200,
            'dbServer' => '54.216.213.109',
            'dbPort' => 3306,
            'db' => 'blogdb',
            'dbUser' => 'blog',
            'dbPassword' => 'Gdn8dke19bb',
            //'riverInterval' => '60s' //60s or 5m etc
        ),
        'elastic' => array(
            'class' => 'application.extensions.sherlock.ElasticYii',
            'server' => '54.217.224.194',
            //'server' => 'localhost',
            'port' => 9200,
            'dbServer' => '54.216.213.109',
            'dbPort' => 3306,
            'db' => 'blogdb',
            'dbUser' => 'blog',
            'dbPassword' => 'Gdn8dke19bb',
            //'riverInterval' => '60s' //60s or 5m etc
        ),
		'theme' => array(
            'class' => 'application.components.Theme',
            'name' => 'new',
            'domain' => '//iblog.oldbk.com',
        ),
		'curl' =>array(
            'class' => 'application.extensions.curl.Curl',
            //you can setup timeout,http_login,proxy,proxylogin,cookie, and setOPTIONS
        ),
		'vk' =>array(
            'class' => 'application.components.VK.VK',
            'apiKey' => 'HR5p38l0MwV9CNVfhTl0',
            'apiId' => '3916734',
        ),
		'aws' =>array(
            'class' => 'application.components.AWS',
            'key' => 'AKIAI7UQABVHUXWNAG2Q',
            'secret' => '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',
            'bucket' => 'oldbkblog',
        ),
		'message' =>array(
            'class' => 'application.components.Base.ajax.Message',
        ),
        'access' =>array(
            'class' => 'application.components.Access',
        ),
		'radio' =>array(
            'class' => 'application.components.Radio.RadioInfo',
            'host' => '127.0.0.1',
            'port' => 8000,
            'login' => 'admin',
            'password' => 'Gnngzwctk013RKieTZiG'
        ),
        'rvs' =>array(
            'class' => 'application.components.RVS',
        ),
        'cache'=>array(
            'class' => 'system.caching.CMemCache',
			'useMemcached' => true,
            'servers'=>array(
                array('host'=>'88.198.205.125', 'port'=>11211),
            ),
        ),
        'userOwn' =>array(
            'class' => 'application.components.Base.users.UserOwn',
        ),
		'user'=>array(
            // enable cookie-based authentication
            'allowAutoLogin' => true,
            'loginUrl' => array('/post/index/index'),
            'returnUrl' => array('/post/index/index'),
            'class' => 'application.components.Base.users.WebUser',
        ),
		'community' =>array(
            'class' => 'application.components.Base.CommunityInfo',
        ),
		'messages' => array(
            'class' => 'CPhpMessageSource',
            'forceTranslation' => true,
            'basePath' => null,
        ),
        'bootstrap' => array(
            'class'          => 'ext.booster.components.Bootstrap',
            //'coreCss'        => false,
            //'responsiveCss'  => false,
            'yiiCss'         => false,
            'jqueryCss'      => false,
            //'enableJS'       => false,
            'fontAwesomeCss' => false,
        ),
		'request' => array(
            'class' => 'application.components.EHttpRequest',
            'enableCsrfValidation' => true,
        ),
		'paramsWrap' => array(
            'class' => 'application.components.ParamsWrap',
        ),
		'ih'=>array('class'=>'CImageHandler'),
		'mail' => array(
            'class' => 'application.extensions.yii-mail.YiiMail',
            //'transportType'=>'smtp', /// case sensitive!
            /* 'transportOptions'=>array(
              'host'=>'smtp.gmail.com',
              'username'=>'yourgoogleemail@gmail.com',
              // or email@googleappsdomain.com
              'password'=>'yourgooglemailpassword',
              'port'=>'465',
              'encryption'=>'ssl',
              ), */
            'viewPath' => 'webroot.themes.bootstrap.views.mail',
            'logging' => true,
            'dryRun' => false
        ),
		'clientScript' => array(
            //'class' => 'ext.NLSClientScript.NLSClientScript',
            'class' => 'CClientScript',
            //'excludePattern' => '/\.tpl/i', //js regexp, files with matching paths won't be filtered is set to other than 'null'
            //'includePattern' => '/\.php/', //js regexp, only files with matching paths will be filtered if set to other than 'null'

            //'mergeJs' => true, //def:true
            //'compressMergedJs' => false, //def:false

            //'mergeCss' => false, //def:true
            //'compressMergedCss' => false, //def:false

            //'mergeJsExcludePattern' => '/edit_area/', //won't merge js files with matching names

            //'mergeIfXhr' => true, //def:false, if true->attempts to merge the js files even if the request was xhr (if all other merging conditions are satisfied)

            //'serverBaseUrl' => 'http://blog.oldbk.loc', //can be optionally set here
            //'mergeAbove' => 1, //def:1, only "more than this value" files will be merged,
            //'curlTimeOut' => 0, //def:10, see curl_setopt() doc
            //'curlConnectionTimeOut' => 0, //def:10, see curl_setopt() doc

            //'appVersion'=>1.2, //if set, it will be appended to the urls of the merged scripts/css
            'scriptMap' => array(
                'jquery.js' => false,
                'jquery.min.js' => false,
            ),
            'packages' => array( //register your packages
                'jquery-1.8.2' => array(
                    'baseUrl' => '//iblog.oldbk.com/js/',
                    'js' => array('jquery-1.8.2.min.js'),
                    //'js' => array('jquery-1.10.2.min.js'),
                ),
                'jgrowl' => array(
                    'baseUrl' => '//iblog.oldbk.com/js/jgrowl/',
                    'js' => array('jquery.jgrowl_minimized.js'),
                    'css' => array('jquery.jgrowl.css'),
                ),
                'jcrop' => array(
                    'baseUrl' => '//iblog.oldbk.com/js/jcrop/',
                    'js' => array('js/jquery.Jcrop.min.js'),
                    'css' => array('css/jquery.Jcrop.min.css'),
                ),
                'fancy' => array(
                    'baseUrl' => '//iblog.oldbk.com/js/fancybox/',
                    'js' => array('source/jquery.fancybox.pack.js', 'lib/jquery.mousewheel-3.0.6.pack.js'),
                    'css' => array('source/jquery.fancybox.css'),
                ),
                'fancyHelpers' => array(
                    'baseUrl' => '//iblog.oldbk.com/js/fancybox/source/helpers/',
                    'js' => array('jquery.fancybox-buttons.js', 'jquery.fancybox-media.js', 'jquery.fancybox-thumbs.js'),
                    'css' => array('jquery.fancybox-buttons.css', 'jquery.fancybox-thumbs.css'),
                ),
                'j-ui' => array(
                    'baseUrl' => 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/',
                    'js' => array('jquery-ui.min.js', 'i18n/jquery-ui-i18n.min.js'),
                    'css' => array('themes/humanity/jquery-ui.css'),
                ),
                'images' => array(
                    'baseUrl' => '//iblog.oldbk.com/js/',
                    'js' => array('images.js'),
                ),
                'audio' => array(
                    'baseUrl' => '//iblog.oldbk.com/js/',
                    'js' => array('audio.js'),
                ),
                'video' => array(
                    'baseUrl' => '//iblog.oldbk.com/js/',
                    'js' => array('video.js'),
                ),
                'tooltip' => array(
                    'baseUrl' => '//iblog.oldbk.com/js/tooltip/',
                    'js' => array('jquery.tipTip.minified.js'),
                    'css' => array('tipTip.css'),
                )
            ),
        ),
		'db' => array(
            //'connectionString' => 'mysql:host=blogdb.c4c2zvyoc0zt.eu-west-1.rds.amazonaws.com;dbname=blogdb',
			//'username' => 'blog',
			//'password' => 'Gdn8dke19bb',
            'connectionString' => 'mysql:host=88.198.205.122;dbname=blogdb',
			'username' => 'blogdb',
            'password' => 'zmtbqYeIRZaNwjqyOIio',
            'emulatePrepare' => true,
            'charset' => 'utf8',
            'enableParamLogging' => false,
            'enableProfiling' => false,
            'tablePrefix'=>'',
            'schemaCachingDuration' => 1200
        ),
        'advert' => array(
            'connectionString' => 'mysql:host=blogdb.c4c2zvyoc0zt.eu-west-1.rds.amazonaws.com;dbname=advert',
            'emulatePrepare' => true,
            'username' => 'blog',
            'password' => 'Gdn8dke19bb',
            'charset' => 'utf8',
            'enableParamLogging' => false,
            'enableProfiling' => false,
            'tablePrefix'=>'',
            'schemaCachingDuration' => 1200
        ),
        'session' => array (
            'class' => 'CHttpSession',
            'sessionName' => 'blog_oldbk',
            'timeout' => 1440,
            'cookieMode' => 'allow',
            'cookieParams' => array(
                'path' => '/',
                'domain' => '.blog.oldbk.com',
                'httpOnly' => true,
            ),
        ),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'urlManager'=>array(
        	'urlFormat'=>'path',
            'showScriptName' => false,
            'urlSuffix' => '.html',
        	'rules' => require(dirname(__FILE__).'/rules.php'),
        ),
		'authManager' => array(
            'class' => 'CDbAuthManager',
            'connectionID' => 'db',
            'defaultRoles' => array('Authenticated', 'Guest'),
        ),
		'mongodb' => [
            'class' => 'EMongoClient',
            'server' => 'mongodb://127.0.0.1:27017',
            'db' => 'blog'
        ],
		'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                /*array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning',
                ),*/
                /*array(
                    'class'=>'ext.yii-debug.YiiDebugToolbarRoute',
                    'ipFilters'=>array('178.151.80.59'),
                ),*/
                /*array(
                    'levels' => 'info, profile, warning, error',
                    'class' => 'CWebLogRoute',
                    //'categories' => '*',
                    'showInFireBug' => true
                ),*/
				array(
                    'class'=>'ext.yii-debug.YiiDebugToolbarRoute',
                    'ipFilters'=>array('127.0.0.1', '178.151.80.59', '193.138.244.26', '93.125.108.207'),
                ),
            ),
		),
		'image'=>array(
            'class'=>'application.extensions.image.CImageComponent',
            // GD or ImageMagick
            'driver'=>'ImageMagick',
            // ImageMagick setup path
            'params'=>array('directory'=>'/usr/bin'),
        ),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>require(dirname(__FILE__).'/params.php'),
);