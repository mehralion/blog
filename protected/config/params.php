<?php

// this contains the application parameters that can be maintained via GUI
return array(
	// this is displayed in the header section
	'title'=>'My Yii Blog',
	// this is used in error pages
	'adminEmail'=>'webmaster@example.com',
	// number of posts displayed per page
	'postsPerPage'=>10,
	// maximum number of comments that can be displayed in recent comments portlet
	'recentCommentCount'=>10,
	// maximum number of tags that can be displayed in tag cloud portlet
	'tagCloudCount'=>20,
	// whether post comments need to be approved before published
	'commentNeedApproval'=>true,
	// the copyright information displayed in the footer section
	'copyrightInfo'=>'Copyright &copy; 2009 by My Company.',
    'dateTime' => array(
        'community' => 'dd.MM.yyyy | HH:mm'
    ),
    'timeDb' => 'yyyy-MM-dd HH:mm:ss',
    'dbTimeFormat' => 'Y-m-d H:i:s',
    'siteTimeFormat' => 'd.m.Y H:i:s',
    'siteTimeFormatShort' => 'd.m.Y H:i',
    'formatDB' => 'yyyy-MM-dd HH:mm:ss',
    'page_size' => array(
        'album' => 20,
        'image' => 20,
        'video' => 20,
        'comment' => 20,
        'post' => 20,
        'friend' => 20,
        'top_image' => 18,
        'top_video' => 18,
        'top_user' => 18,
        'ld' => 10,
        'community' => array(
            'users' => 20,
            'index' => 10,
        )
    ),
    'tagCount' => 100,
    'tagUse' => 1,
    'no_avatar' => 'images4444.jpg',
    'cache' => array(
        'userOwn' => 0,
        'tag' => 600,
        'event_news' => 5000,
        'friend' => 5000,
        'base' => 5000,
        'post' => 5000,
        'comment' => 5000,
        'album' => 5000,
        'image' => 5000,
        'video' => 5000,
        'moderlog' => 5000,
    ),
    'icons' => array(
        'edit' => 'edit16.png',
        'delete' => 'close16.png',
        'grid' => array(
            'delete' => 'no.png',
            'accept' => 'ok.png',
            'view' => 'album.png',
        )
    )
);
