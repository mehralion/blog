
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <title></title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/style.css" type="text/css" media="screen, projection" />
    <!--[if lte IE 7]>
    <style type="text/css">
        html, body {
            height: 100%;
            overflow: hidden;
        }

        .oldIE {
            color: #000;
            position: absolute;
            height: 100%;
            width: 100%;
        }
    </style>
    <![endif]-->
</head>

<body>
<!--[if lte IE 7]>
<div class="oldIE">
    <h1>Please, download or update new browser.</h1>
</div>
<![endif]-->
<div id="wrapper">

    <header id="header">
        <section id="headerInfo">
            <div class="content"></div>
        </section>

        <div id="logo" style="float: none; left 0;">
            <a href="<?php echo Yii::app()->createUrl('/site/index'); ?>" title=""><img src="<?php echo Yii::app()->theme->baseUrl; ?>/images/logo.png" alt="oldbk portal, best browser game, etc."></a>
        </div>

    </header><!-- #header-->

    <section id="middle">
            <?php echo $content; ?>

    </section><!-- #middle-->

    <footer id="footer">
        ©2012 OLDBK.COM. All rights reserved
    </footer><!-- #footer -->

</div><!-- #wrapper -->

</body>
</html>