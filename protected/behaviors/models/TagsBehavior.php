<?php
include_once(Yii::app()->basePath.'/libs/strip_tags_smart.php');
/**
 * Class TagsBehavior
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 21.06.13
 * Time: 16:44
 * To change this template use File | Settings | File Templates.
 *
 * @property Post|GalleryImage|GalleryVideo $owner
 *
 * @package application.behaviors.models
 */
class TagsBehavior extends CActiveRecordBehavior
{
    public function beforeSave($event)
    {
        foreach($this->owner->attributes as $name => $val) {
            if(is_string($val)) {
                if($this->owner->getAttribute('admin_text') != 1)
                    $val = strip_tags_smart($val);

                if($name == 'description' && $this->owner->getAttribute('admin_text') != 1)
                    $val = $this->tt($val);

                $replace = Yii::app()->rvs->search($val);
                if(count($replace) > 0) {
                    $val = str_replace($replace, '[ВЦ]', $val);
                }

                $this->owner->setAttribute($name, $val);
            }

        }

        return parent::beforeSave($event);
    }

    public function tt($text)
    {
        return preg_replace(
            '/(?<!image\]|link="|mp3\]|"\])(http\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(?:\/\S*)?)/ui',
            '[link="$1"]$1[/link]',
            $text
        );
    }

    private $openTag = array('[b]','[i]','[u]', '[/b]','[/i]','[/u]');
    private $closeTag = array('<b>','<i>','<u>', '</b>','</i>','</u>');
    private $link = "https://oldbk.com/inf.php?login=";
    public function parseTag($str = "")
    {
        $uniq = uniqid(time());
        $str = str_replace($this->openTag, $this->closeTag, $str);
        $str = preg_replace('/\[image\](.+?)\[\/image\]/ui', '<img src="$1">', $str);
        $str = preg_replace('/\[link="(.+?)"\](.+?)\[\/link\]/ui', '<a href="$1">$2</a>', $str);
        $str = preg_replace('/\[info\](.+?)\[\/info\]/ui', '$1<a target="_blank" href="'.$this->link.'$1"><img src="https://i.oldbk.com/i/inf.gif"></a>', $str);
        $str = preg_replace('/\[color="(.+?)"\](.+?)\[\/color\]/ui', '<span style="color:$1;">$2</span>', $str);
        /*$str = preg_replace(
            '/\[youtube="(.+?)"\]/ui',
            '<iframe width="420" height="315" src="$1Y" frameborder="0" allowfullscreen=""></iframe>',
            $str);*/
        $str = preg_replace(
            '/\[hide="(.+?)"\](.+?)\[\/hide\]/ui',
            '<div style="cursor:pointer;" onclick=\'$("#'.$uniq.'").toggle();\'>$1</div><div id="'.$uniq.'" style="display:none;">$2</div>',
            $str);
        $str = preg_replace(
            '/\[smile="(.+?)"\]/ui',
            '<img src="'.Yii::app()->baseUrl.'/smiles/$1.gif">',
            $str);
        return $str;
    }

    public function parseEditor()
    {
        $str = $this->owner->description;
        $str = str_replace($this->closeTag, $this->openTag, $str);
        if(preg_match('/smiles/ui', $this->owner->description))
            $str = preg_replace('/<img src="\/smiles\/(.+?).gif">/ui', '[smile="$1"]', $str);
        $str = preg_replace('/<img src="(.+?)">/ui', '[image]$1[/image]', $str);
        $str = preg_replace('/<a href="(.+?)">(.+?)<\/a>/ui', '[link="$1"]$2[/link]', $str);
        $str = preg_replace('/<a href="{addslashes($this->link)}(.+?)">(.+?)<\/a>/ui', '[info]$1[/info]', $str);
        $str = preg_replace('/<span style="color:(.+?);">(.+?)<\/span>/ui', '[color="$1"]$2[/color]', $str);
        $str = preg_replace(
            '/<div style="cursor:pointer;"(?:.+?)>(.+?)<\/div><div(?:.*?)style="display:none;">(.+?)<\/div>/ui',
            '[hide="$1"]$2[/hide]',
            $str);
        return $str;
    }
}