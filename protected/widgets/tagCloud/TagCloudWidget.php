<?php
Yii::import('zii.widgets.CPortlet');
/**
 * Class TagCloudWidget
 *
 * @package application.widgets.tagCloud
 */
class TagCloudWidget extends CPortlet
{
    public $title = false;
    public $maxTags=100;

    protected function renderContent()
    {
        $dependency = new CDbCacheDependency('select count(*) from {{post_tag}}');
        $tags=Tag::model()->cache(1000, $dependency)->findTagWeights($this->maxTags);

        foreach($tags as $tag=>$count)
        {
            $weight = 0;
            switch ($count) {
                case $count <= 5:
                    $weight = 5;
                    break;
                case $count <= 10:
                    $weight = 10;
                    break;
                case $count <= 15:
                    $weight = 15;
                    break;
                case $count <= 30:
                    $weight = 20;
                    break;
                case $count <= 60:
                    $weight = 25;
                    break;
                case $count <= 70:
                    $weight = 26;
                    break;
                case $count <= 80:
                    $weight = 27;
                    break;
                case $count >= 80:
                    $weight = 30;
                    break;
            }
            $link=CHtml::link(CHtml::encode($tag), array('/post/index/index','tag'=>$tag));
            echo CHtml::tag('div', array(
                'class'=>'tag',
                'title' => $tag,
                'style'=>"font-size:{$weight}px;display:inline-block;height:{$weight}px;",
            ), $link)." ";
        }
    }
}