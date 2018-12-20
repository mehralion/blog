<?php
/**
 * Class Access Права доступа для просмотра
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 17.06.13
 * Time: 20:32
 * To change this template use File | Settings | File Templates.
 *
 * @package application.components
 */
class RVS extends CApplicationComponent
{
    const LEVEL_MIN = 4;
    const LEVEL_MAX = 12;

    private $_links = array();
    private $_clans = array();
    private $_ignore = array();
    private $_pals = null;

    private $_replace = array();

    public function init()
    {
        parent::init();
        /** @var RapUrl[] $Urls */
        $Urls = RapUrl::model()->findAll();
        foreach($Urls as $model) {
            if($model->type == RapUrl::TYPE_RAP)
                $this->_links[] = $model->value;
            elseif($model->type == RapUrl::TYPE_IGNORE)
                $this->_ignore[] = $model->value;
            elseif($model->type == RapUrl::TYPE_CLANS)
                $this->_clans[] = $model->value;
        }

        $this->_pals = array(
            'u.to',
            'to.ly',
            'gu.ma',
            'bit.ly',
            'it.ac',
            'xav.cc',
            'ks.ua',
            'gu.ma',
            'goo.gl',
            'rlu.ru',
            'tiny.cc',
            'is.gd',
            'loh.ru',
            'b23.ru',
            'url.az',
            'удаляем пробелы',
            'вотссылка.рф',
            'url.ie',
            'sn.im',
            'ow.ly',
            'u*to',
            'Убераем пробелы',
            'бк-2.рф',
            'is.gd',
            'b23.ru'
        );
    }

    public function search($where_to_search)
    {
        //setlocale (LC_ALL, 'ru_RU');

        $where_to_search = mb_strtolower($where_to_search);
        $iskluchenija = CMap::mergeArray($this->_clans, $this->_ignore);
        $iskluchenija[] = '@bk\.ru';
        $iskluchenija[] = 'blog\.oldbk\.com';
        $iskluchenija[] = 'blog\.oldbk\.loc';
        $iskluchenija[] = 'forum\.php';
        $iskluchenija[] = 'oldbk\.com';
        $iskluchenija[] = '\.combats\.com';
        $iskluchenija[] = 'oldbk\.net';
        $iskluchenija[] = 'OLDBK BRUTalov';
        $iskluchenija[] = 'OM\_oldbk\@mail\.ru';
        $iskluchenija[] = '_';
        $iskluchenija[] = 'private\s\[[a-zA-Zа-яА-Я0-9_\s-]{3,21}\]';
        $iskluchenija[] = 'рубиновые';
        $iskluchenija[] = 'арсенал';
        $iskluchenija[] = 'квартир';
        $iskluchenija[] = 'успешно';
        $iskluchenija[] = 'OlD_Bk';
        $iskluchenija[] = 'Полтергейст';
        $iskluchenija[] = 'в арсе';
        $iskluchenija[] = 'inf.php?login=Old_bk';
        $iskluchenija[] = 'dtoldbk.jr1.ru';
        $iskluchenija[] = 'олдбк круто';
        $iskluchenija[] = 'олдбк рулит';
        $iskluchenija[] = 'против';
        $iskluchenija[] = 'колдовской';
        $iskluchenija[] = 'Колдовской';
        $iskluchenija[] = 'в колдовской';
        $iskluchenija[] = 'в колдовско';
        $iskluchenija[] = 'колдовсом';
        $iskluchenija[] = 'кол';
        $iskluchenija[] = 'олдов';
        $iskluchenija[] = 'MortalKombat';
        $iskluchenija[] = 'titansoldbk';
        $iskluchenija[] = 'к в арс';
        $iskluchenija[] = 'knl-oldbk.blogsuper.ru';
        $iskluchenija[] = 'combats.stalkers.ru';
        $iskluchenija[] = 'установку образра';
        $iskluchenija[] = 'новку образр';
        $iskluchenija[] = 'новка образр';
        $iskluchenija[] = 'новке образр';
        $iskluchenija[] = 'новки образр';
        $iskluchenija[] = 'образ';
        $iskluchenija[] = 'Сдам в Аренду';
        $iskluchenija[] = 'rupor';
        $iskluchenija[] = 'руслан';
        $iskluchenija[] = 'navi-oldbkclan.ucoz.ru';
        $iskluchenija[] = 'Hero of OldBk';
        $iskluchenija[] = 'олдбк.рф';
        $iskluchenija[] = 'dimonti98@nm.ru';
        $iskluchenija[] = 'commerce';

        $where_to_search=preg_replace('/(.)\\1{3,}/ui','$1',$where_to_search);
        $where_to_search=preg_replace('/\s{2,}/ui','$1',$where_to_search);

        for($d=0;$d<count($iskluchenija);$d++) {
            $where_to_search=preg_replace('/'.$iskluchenija[$d].'/ui','',$where_to_search);
        }

        for($b=0;$b<count($this->_pals);$b++) {
            $pos=mb_stripos($where_to_search, $this->_pals[$b]);
            if($pos!==false)
                $this->_replace[] = $this->_pals[$b];
        }

        //$where_to_search='wer wer wr w f d*W*О*r*l*d*s(ОГОГОГО)net wer';
        //   x.ru
        //   кс
        $map_en = Array(
            '-'=>'–—','a'=>'а','b'=>'бв','c'=>'сцк','d'=>'д','e'=>'еф','f'=>'фй','j'=>'г',
            'h'=>'х','i'=>'ий','g'=>'г','k'=>'к','l'=>'л','m'=>'м','n'=>'н','o'=>'о0',
            'p'=>'п','q'=>'ку','r'=>'р','s'=>'с','t'=>'т','u'=>'у','v'=>'в','w'=>'в','x'=>'х','x'=>'кс','y'=>'ыюуя','y'=>'у','z'=>'з','y'=>'я','y'=>'ы','ya'=>'я',
            'а'=>'а','б'=>'б','в'=>'в','г'=>'г','д'=>'д','е'=>'е','ж'=>'ж','з'=>'з','и'=>'и','к'=>'к','л'=>'л','м'=>'м','н'=>'н','о'=>'о','п'=>'п','р'=>'р','с'=>'с','т'=>'т','у'=>'у','ф'=>'ф',
            'х'=>'х','ц'=>'ц','ч'=>'ч','ш'=>'ш','щ'=>'щ','ъ'=>'ъ','ы'=>'ы','ь'=>'ь','э'=>'э','ю'=>'ю','я'=>'я','ё'=>'ё');

        for($f=0;$f<count($this->_links);$f++) {
            $regexp='';

            for ($i=0; $i<=mb_strlen($this->_links[$f])-1; $i++) {
                //побуквенно собираем маски поиска.
                //'Берем адрес из словаря fdworlds.net и кладем его на маску, чтоб определять
                // f*dw*or*ld*s.net  f-d-w-o-r-lds.net  f d w o r l d s(точка)net  и тд
                //'[fф](\W*)[dд](\W*)[wв](\W*)[oо](\W*)[rр](\W*)[lл](\W*)[dд](\W*)[sс](\W*)(.?){10}(\W*)[nн](\W*)[eеэ](\W*)[tт]'
                if($this->_links[$f][$i]=='.') {
                    //$regexp=substr($regexp,0,-5);
                    $regexp.='(.?){3}';
                } else {
                    if(isset($map_en[$this->_links[$f][$i]]))
                        $regexp.='['.$this->_links[$f][$i].$map_en[$this->_links[$f][$i]].']';
                    else
                        $regexp.='['.$this->_links[$f][$i].']';

                    if($i<mb_strlen($this->_links[$f])-1)
                        $regexp.='(\W{0,1})';
                }
            }

            $regexp = '/'.$regexp.'/ui';

            if(preg_match($regexp,$where_to_search, $out)) {
                foreach($out as $value) {
                    if($value == '' || in_array($value, $this->_replace) || mb_strlen($value) < 4)
                        continue;

                    $this->_replace[] = $value;
                }
            }
        }

        return $this->_replace;
    }
}