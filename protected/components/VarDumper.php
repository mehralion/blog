<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 14.03.12
 * Time: 15:36
 * To change this template use File | Settings | File Templates.
 *
 * @package application.components
 */
class VarDumper extends CVarDumper {
    /**
     * Displays a variable.
     * This method achieves the similar functionality as var_dump and print_r
     * but is more robust when handling complex objects such as Yii controllers.
     * @param mixed variable to be dumped
     * @param integer maximum depth that the dumper should go into the variable. Defaults to 10.
     * @param boolean whether the result should be syntax-highlighted
     */
    public static function dump($var,$depth=10,$highlight=true){
        echo self::dumpAsString($var,$depth,$highlight);
    }

    public function D($vars)
    {
        $text = '';
        if(is_array($vars)) {
            foreach($vars as $key => $value) {
                $text .= $key.' === '.$value.'\n';
            }
        } else {
            $text = $vars;
        }
        $fp=fopen(dirname(__FILE__).'/../../logs/dumper.txt','w+');
        fwrite($fp, $text . "\n\n");

        fclose($fp);  // close file
    }
}