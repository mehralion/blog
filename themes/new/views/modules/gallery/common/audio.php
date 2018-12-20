<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 02.10.13
 * Time: 19:54
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryAudio $model
 */
$this->widget('application.widgets.mp3.PlayerWidget', array(
    'titleMp3'=>$model->title,
    'link' => $model->link
));
?>