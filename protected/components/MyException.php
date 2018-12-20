<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 26.06.13
 * Time: 23:50
 * To change this template use File | Settings | File Templates.
 *
 * @package application.components
 */
class MyException
{
    public static function ShowError($code, $message)
    {
        if(Yii::app()->request->getIsAjaxRequest()) {
            Yii::app()->controller->renderPartial('ajax.error', array(
                'code' => $code,
                'message' => $message
            ));
        } else {
            Yii::app()->controller->render('themePath.views.www.site.error', array(
                'code' => $code,
                'message' => $message
            ));
        }
        Yii::app()->end();
    }

    public static function log(Exception $ex, $logFile = 'log')
    {
        try {
            $date = date('d.m.Y H:i:s', time());
            ob_start();
            VarDumper::dump($ex);
            $text = "--------START ".$date."-------<br>\n";
            $text .= ob_get_clean();
            $text .= "--------END ".$date."-------<br>\n\n\n";
            $path = Yii::app()->basePath.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR;
            $h = fopen($path.$logFile.".html","a");
            fwrite($h,$text);
            fclose($h);
        } catch (Exception $ex) {

        }

        \Yii::app()->message->setErrors('danger', 'Возникли проблемы, попробуйте позже!');
    }

    public static function logTxt($text, $fileName = 'logTxt', $ext = 'html')
    {
        try {
            $date = date('d.m.Y H:i:s', time());
            ob_start();
            VarDumper::dump($text);
            $text = "--------START ".$date."-------<br>\n";
            $text .= ob_get_clean();
            $text .= "--------END ".$date."-------<br>\n\n\n";
            $path = Yii::app()->basePath.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR;
            $h = fopen($path.$fileName.".".$ext, "a");
            fwrite($h,$text);
            fclose($h);
        } catch (Exception $ex) {

        }

        \Yii::app()->message->setErrors('danger', 'Возникли проблемы, попробуйте позже!');
    }

    public static function logFile($text, $fileName)
    {
        $path = Yii::app()->basePath.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR;
        try {
            $h = fopen($path.$fileName, "a");
            fwrite($h,$text);
            fclose($h);
        } catch (Exception $ex) {

        }
    }

    public static function mongoException(Exception $ex)
    {
        if($ex->getCode() == -1)
            return;

        try {
            Yii::app()->mongodb->log->insert([
                    'code' => $ex->getCode(),
                    'message' => $ex->getMessage()
                ]);
        } catch (Exception $ex) {

        }
    }

    public static function mongoLog($collection, $params)
    {
        try {
            Yii::app()->mongodb->$collection->insert($params);
        } catch (Exception $ex) {
            var_dump($ex->getMessage());
        }
    }
}