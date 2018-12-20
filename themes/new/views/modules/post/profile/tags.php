<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 12.06.13
 * Time: 1:03
 * To change this template use File | Settings | File Templates.
 */?>

[<?php if($tags){
    $total = count($tags) - 1;
    foreach ($tags as $i =>$tag){
        echo '{';
        echo '"id": "'.$tag->id.'",';
        echo '"label": "'.$tag->title.'",';
        echo '"value": "'.$tag->count.'"';
        echo '}';
        if($total !== $i){
            echo ',';
        }
    }
}?>]