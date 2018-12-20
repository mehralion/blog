<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 28.08.13
 * Time: 16:23
 * To change this template use File | Settings | File Templates.
 */
include_once((__DIR__).'/../libs/strip_tags_smart.php');
class StringHelper extends CComponent
{
    public function init()
    {

    }
    /**
     *
     */
    public function subString($string, $length=300, $after='')
    {
        $string=strip_tags_smart($string);
        if(mb_strlen($string)>$length)
        {
            return mb_substr(html_entity_decode($string, ENT_QUOTES, 'UTF-8'), 0, $length, 'utf-8').$after;
        }
        return $string;
    }


    public function subStringNew($string, $length = 300, $after='')
    {
        if(mb_strlen($string)>$length)
        {
            return $this->truncate($string, $length, $after);
        }
        return $string;
    }

    public function code($string)
    {
        return '<code'.str_replace('<br>', '', $string[1]).'code>';
    }

    public function setBR($string)
    {
        //if(Yii::app()->user->id == 1)
        return '<pre class="blog">'.$string.'</pre>';

        $string = str_replace("\n", "<br>", $string);
        $string = preg_replace_callback("/<code(.+?)code>/ui", function($match) { return $this->code($match); }, $string);
        return $string;
    }


    private $openTag = array('[b]','[i]','[u]','[/b]','[/i]','[/u]','[table]','[th]','[tr]','[td]','[/table]','[/th]','[/tr]','[/td]', '[br]');
    private $closeTag = array('<b>','<i>','<u>','</b>','</i>','</u>','<table class="custom">','<th>','<tr>','<td>','</table>','</th>','</tr>','</td>', '<br>');
    private $link = "https://oldbk.com/inf.php?login=";
    public function parseTag($str = "")
    {
        $str = htmlspecialchars($str, ENT_NOQUOTES);
        $str = str_replace(array('>', '<'), array('&gt;', '&lt;'), $str);
        $str = str_replace($this->openTag, $this->closeTag, $str);
        //if(preg_match('/\[image\](.+?)\[\/image\]/ui', $str))
            $str = preg_replace('/\[image\](.+?)\[\/image\]/uis', '<figure class="img_border"><a rel="nofollow" class="fancybox preview_image" target="_blank" href="$1"><img src="$1" alt="" /></a></figure>', $str);

        //code highlightjs
        $str = preg_replace(
            ['/\[code="(.+?)"\]/ui', '/\[\/code\]/ui'],
            ['<code class="$1 hljs"><h4>$1</h4>', '</code>'],
            $str
        );

        //if(preg_match('/\[link="http:\/\/(.+?)"\](.+?)\[\/link\]/ui', $str))
            $str = preg_replace(
                array(
                    '/\[link="http:\/\/(.+?)"\]/ui',
                    '/\[\/link\]/ui'
                ),
                array(
                    '<a href="http://$1" target="_blank" rel="nofollow">',
                    '</a>',
                ),
                $str
            );

        //table
        $str = preg_replace(['/\[table class="loto"\]/ui'], ['<table class="loto">'], $str);

        /** quick fix */
        $str = preg_replace(
            ['/\[user align="(.+?)" klan="(.+?)" login="(.+?)" level="(.+?)" game_id="(.+?)"\]/ui'],
            ['<img src="https://i.oldbk.com/i/align_$1.gif" alt="">
                <img title="$2" src="https://i.oldbk.com/i/klan/$2.gif" alt="$2">
                <span style="vertical-align:middle;"><a href="/$5/">$3 [$4]</a></span>
                <a target="_blank" href="https://oldbk.com/inf.php?$5"><img style="margin-bottom:2px" src="https://i.oldbk.com/i/inf.gif" alt=""></a>'],
            $str);
        $str = preg_replace(
            ['/\[user align="(.+?)" login="(.+?)" level="(.+?)" game_id="(.+?)"\]/ui'],
            ['<img src="https://i.oldbk.com/i/align_$1.gif" alt="">
                <span style="vertical-align:middle;"><a href="/$4/">$2 [$3]</a></span>
                <a target="_blank" href="https://oldbk.com/inf.php?$4"><img style="margin-bottom:2px" src="https://i.oldbk.com/i/inf.gif" alt=""></a>'],
            $str);
        /** end */

        Yii::import('application.widgets.mp3.PlayerWidget');
        $Mp3 = new PlayerWidget();
        if(preg_match('/\[mp3\](.*)\[\/mp3\]/uis', $str)) {
            preg_match_all('/\[mp3\](.*)\[\/mp3\]/uis', $str, $out);
            for($i = 0; $i < count($out); $i++) {
                $uniq = uniqid($i);
                $playerString = '<noindex><object type="application/x-shockwave-flash" data="'.$Mp3->getPlayer().'" width="273" height="25" id="'.$uniq.'">
                        <param name="bgcolor" value="#ffffff">
                        <param name="allowScriptAccess" value="always">
                        <param name="movie" value="'.$Mp3->getPlayer().'">
                        <param name="flashvars" value="st='.$Mp3->getStyles().'&file=$1&m=audio">
                    </object></noindex>';
                $str = preg_replace('/\[mp3\](.*)\[\/mp3\]/uis', $playerString, $str, 1);
            }
        }

        $str = preg_replace('/\[info\]([^\[\]]+?)\[\/info\]/uis', '$1<a target="_blank" href="'.$this->link.'$1"><img src="https://i.oldbk.com/i/inf.gif" alt="" /></a>', $str);
        $str = preg_replace(
            array(
                '/\[color="(.+?)"\]/ui',
                '/\[\/color\]/ui'
            ),
            array(
                '<span style="color:$1;">',
                '</span>',
            ),
            $str
        );
        $str = preg_replace(
            '/\[smile="(.+?)"\]/ui',
            '<img src="'.Yii::app()->baseUrl.'/smiles/$1.gif" alt="" />',
            $str);


        if(preg_match('/\[hide="(.+?)"\](.*)\[\/hide\]/uis', $str)) {
            preg_match_all('/\[hide="(.+?)"\](.*)\[\/hide\]/uis', $str, $out);
            for($i = 0; $i < count($out); $i++) {
                $uniq = uniqid($i);
                $str = preg_replace(
                    array(
                        '/\[hide="(.+?)"\]/uis',
                        '/\[\/hide\]/uis'
                    ),
                    array(
                        '<div style="cursor:pointer;" onclick=\'$("#'.$uniq.'").toggle();\'><b> >> $1 &lt;&lt; </b></div><div id="'.$uniq.'" style="display:none;">',
                        '</div>',
                    ),
                    $str,
                    1
                );
            }
        }

        $str = preg_replace(
            array(
                '/\[quote="(.+?)"\]/ui',
                '/\[quote=""\]/ui',
                '/\[\/quote\]/ui'
            ),
            array(
                '<blockquote class="quote"><div class="quote_nick">$1</div>',
                '<blockquote class="quote">$1',
                '</blockquote>'
            ),
            $str
        );

        $str = preg_replace(
            array(
                '/\[highlight\]/ui',
                '/\[\/highlight\]/ui'
            ),
            array(
                '<strong class="highlight">',
                '</strong>',
            ),
            $str
        );

        $str = self::scheme($str);

        return $str;
    }

    public static function scheme($text)
    {
        $text = str_replace(array('http://'), array('https://'), $text);
        $text = str_replace(array('https://paladins'), array('http://paladins'), $text);

        return $text;
    }

    public function parseEditor($str = "")
    {
        $str = str_replace($this->closeTag, $this->openTag, $str);
        if(preg_match('/smiles/ui', $str))
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

    public function skipTag($str = "")
    {
        $str = str_replace($this->openTag, "", $str);
        $str = preg_replace('/\[image\](.+?)\[\/image\]/ui', "", $str);
        $str = preg_replace('/\[link="(.+?)"\](.+?)\[\/link\]/ui', "", $str);
        $str = preg_replace('/\[info\](.+?)\[\/info\]/ui', "", $str);
        $str = preg_replace('/\[color="(.+?)"\](.+?)\[\/color\]/ui', "", $str);
        $str = preg_replace('/\[hide=\"(.*?)\"\](.*?)\[\/hide\]/uis', "", $str);
        $str = preg_replace('/\[smile="(.+?)"\]/ui', "", $str);
        if(preg_match('/\[quote="(.+?)?"\](.*)\[\/quote\]/uis', $str)) {
            preg_match_all('/\[quote="(.+?)?"\](.*)\[\/quote\]/uis', $str, $out);
            for($i = 0; $i < count($out); $i++) {
                $str = preg_replace('/\[quote="(.+?)"\](.*)\[\/quote\]/uis', "", $str);
                $str = preg_replace('/\[quote=""\](.*)\[\/quote\]/uis', "",$str);
            }
        }
        return $str;
    }

    /**
     * @param string $text String to truncate.
     * @param integer $length Length of returned string, including ellipsis.
     * @param string $ending Ending to be appended to the trimmed string.
     * @param boolean $exact If false, $text will not be cut mid-word
     * @param boolean $considerHtml If true, HTML tags would be handled correctly
     * @return string Trimmed string.
     */
    public  function truncate($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true) {
        if ($considerHtml) {
            // if the plain text is shorter than the maximum length, return the whole text
            if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }
            // splits all html-tags to scanable lines
            preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
            $total_length = strlen($ending);
            $open_tags = array();
            $truncate = '';
            foreach ($lines as $line_matchings) {
                // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                if (!empty($line_matchings[1])) {
                    // if it's an "empty element" with or without xhtml-conform closing slash
                    if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                        // do nothing
                        // if tag is a closing tag
                    } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                        // delete tag from $open_tags list
                        $pos = array_search($tag_matchings[1], $open_tags);
                        if ($pos !== false) {
                            unset($open_tags[$pos]);
                        }
                        // if tag is an opening tag
                    } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                        // add tag to the beginning of $open_tags list
                        array_unshift($open_tags, strtolower($tag_matchings[1]));
                    }
                    // add html-tag to $truncate'd text
                    $truncate .= $line_matchings[1];
                }
                // calculate the length of the plain text part of the line; handle entities as one character
                $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
                if ($total_length+$content_length> $length) {
                    // the number of characters which are left
                    $left = $length - $total_length;
                    $entities_length = 0;
                    // search for html entities
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                        // calculate the real length of all entities in the legal range
                        foreach ($entities[0] as $entity) {
                            if ($entity[1]+1-$entities_length <= $left) {
                                $left--;
                                $entities_length += strlen($entity[0]);
                            } else {
                                // no more characters left
                                break;
                            }
                        }
                    }
                    $truncate .= substr($line_matchings[2], 0, $left+$entities_length);
                    // maximum lenght is reached, so get off the loop
                    break;
                } else {
                    $truncate .= $line_matchings[2];
                    $total_length += $content_length;
                }
                // if the maximum length is reached, get off the loop
                if($total_length>= $length) {
                    break;
                }
            }
        } else {
            if (strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = substr($text, 0, $length - strlen($ending));
            }
        }
        // if the words shouldn't be cut in the middle...
        if (!$exact) {
            // ...search the last occurance of a space...
            $spacepos = strrpos($truncate, ' ');
            if (isset($spacepos)) {
                // ...and cut the text in this position
                $truncate = substr($truncate, 0, $spacepos);
            }
        }
        // add the defined ending to the text
        $truncate .= $ending;
        if($considerHtml) {
            // close all unclosed html-tags
            foreach ($open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }
        return $truncate;
    }

    public static function doLink($text, $linkTitle = '$1')
    {
        return preg_replace(
            '/(?<!image\]|link="|mp3\]|"\])(http\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(?:\/\S*)?)/ui',
            '<a href="$1" target=_blank>'.$linkTitle.'</a>',
            $text
        );
    }
}