<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 25.02.14
 * Time: 2:44
 */

return array(
    '/radio/rusfm' => array('/radio/request/rusview'),
    '/radio/oldfm' => array('/radio/request/oldview'),
    '/radio/request/index' => array('/radio/request/index'),
    '/radio/request/rusfm' => array('/radio/request/rusfm'),
    '/radio/request/oldfm' => array('/radio/request/oldfm'),
    '/radio/request/test' => array('/radio/request/test'),
    '/radio/request/captcha' => array('/radio/request/captcha'),
    '/radio/request/<alias:\w+>' => array('/radio/request/validate'),
);