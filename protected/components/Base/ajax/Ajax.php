<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 21.11.12
 * Time: 15:19
 * To change this template use File | Settings | File Templates.
 *
 * @package application.components.base.ajax
 */
class Ajax extends CApplicationComponent
{
    private $_errors = array();
    private $_text = array();
    public $url = null;
    public $close = true;
    public $_otherParams = array();

    /**
     * @param $errors
     */
    public function setErrors($errors)
    {
        if(!is_array($errors))
            $this->_errors[] = $errors;
        else {
            foreach($errors as $field => $error)
                $this->_errors[$field] = $error[0];
        }
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * @param string $separator
     * @return string
     */
    public function getStringErrors($separator = '<br>')
    {
        $string = "";
        foreach ($this->_errors as $error) {
            if(!is_array($error))
                $string .= $error . $separator;
            else {
                foreach($error as $field => $er)
                    $string .= $er . $separator;
            }
        }

        return $string;
    }

    /**
     * @param $text
     */
    public function setText($text)
    {
        $this->_text[] = $text;
    }

    /**
     * @return array
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * @param string $separator
     * @return string
     */
    public function getStringText($separator = "<br>")
    {
        $string = "";
        foreach ($this->_text as $text)
            $string .= $text . $separator;

        return $string;
    }

    public function setOther($param = array()) {
        $this->_otherParams = CMap::mergeArray($this->_otherParams, $param);
    }

    /**
     *
     */
    public function showMessage()
    {
        $params = array();

        if(null === $this->url)
            $this->url = Yii::app()->request->getUrlReferrer();

        if ($this->getStringErrors() != "")
            $params = array(
                'error' => $this->getErrors()
            );
        elseif ($this->getStringText() != "")
            $params = array(
                'text' => $this->getStringText(),
                'url' => $this->url
            );
        if($this->url === false)
            unset($params['url']);

        $params = CMap::mergeArray($params, $this->_otherParams);
        echo CJSON::encode($params);
        Yii::app()->end();
    }

    /**
     * deprecated
     * @param $type
     * @param $message
     */
    public function message($type, $message)
    {
        switch ($type) {
            case 'error':
                if (!is_array($message))
                    echo CJSON::encode(array(
                        'error' => array(array($message))
                    ));
                else
                    echo CJSON::encode(array('error' => $message));
                break;
            case 'text':
                if (!is_array($message))
                    echo CJSON::encode(array(
                        'text' => $message
                    ));
                else
                    echo CJSON::encode($message);
                break;
        }
        Yii::app()->end();
    }
}
