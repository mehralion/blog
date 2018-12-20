<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 13.01.14
 * Time: 12:26
 */

class ItemTypes {
    const ITEM_TYPE_POST         = 1;
    const ITEM_TYPE_IMAGE        = 2;
    const ITEM_TYPE_VIDEO        = 3;
    const ITEM_TYPE_AUDIO_ALBUM  = 4;
    const ITEM_TYPE_COMMUNITY    = 5;
    const ITEM_TYPE_COMMENT      = 6;
    const ITEM_TYPE_SILENCE      = 7;

    const SUBSCRIBE_USER         = 0;
    const SUBSCRIBE_COMMUNITY    = 1;

    private static $string_types = [
        self::ITEM_TYPE_POST        => 'post',
        self::ITEM_TYPE_IMAGE       => 'image',
        self::ITEM_TYPE_VIDEO       => 'video',
        self::ITEM_TYPE_AUDIO_ALBUM => 'album_audio',
        self::ITEM_TYPE_COMMUNITY   => 'community',
    ];

    public static function getStringType($type)
    {
        return self::$string_types[$type];
    }
}