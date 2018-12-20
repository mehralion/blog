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
class Message extends CApplicationComponent
{
    private $_errors = array();
    private $_text = array();
    public $url = null;
    public $close = true;
    public $_otherParams = array();

    /**
     * @param $type
     * @param array|string|CModel $model
     * @param null|integer $number
     */
    public function setErrors($type, $model, $number = null)
    {
        if(is_object($model) !== false) {
            foreach($model->getErrors() as $attribute=>$errors) {
                if($number === null)
                    $this->_errors[$type][CHtml::activeId($model,$attribute)] = $errors[0];
                else
                    $this->_errors[$type][get_class($model).'_'.$number.'_'.$attribute] = $errors;
            }
        } else {
            if(!is_array($model))
                $this->_errors[$type][] = $model;
            else {
                foreach($model as $field => $error)
                    $this->_errors[$type][$field] = $error[0];
            }
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
        foreach ($this->_errors as $type => $errors) {
            if(!is_array($errors))
                $string .= $errors . $separator;
            else {
                foreach($errors as $field => $error)
                    $string .= $error . $separator;
            }
        }

        return $string;
    }

    /**
     * @param $type
     * @param $text
     */
    public function setText($type, $text)
    {
        $this->_text[$type][] = $text;
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
        foreach ($this->_text as $type =>  $textList) {
            foreach($textList as $text)
                $string .= $text . $separator;
        }

        return $string;
    }

    public function setOther($param = array()) {
        $this->_otherParams = CMap::mergeArray($this->_otherParams, $param);
    }

    /**
     *
     */
    public function showMessage($force = false)
    {
        $params = array();

        if(Yii::app()->request->isAjaxRequest && !$force) {
            $errorsArray = $this->getErrors();
            if(!empty($errorsArray))
                $params = CMap::mergeArray($params, array('error' => $errorsArray));

            $textArray = $this->getText();
            if(!empty($textArray))
                $params = CMap::mergeArray($params, array('text' => $textArray));
        } else {
            foreach($this->getErrors() as $type => $errors) {
                foreach($errors as $error)
                    Yii::app()->user->setFlash($type, $error);
            }
            foreach($this->getText() as $type => $textList) {
                foreach($textList as $text)
                    Yii::app()->user->setFlash($type, $text);
            }
        }

        if(Yii::app()->request->isAjaxRequest) {
            if($this->url !== false && $this->url !== null)
                $params = CMap::mergeArray($params, array('url' => $this->url));

            $params = CMap::mergeArray($this->_otherParams, $params);
            echo CJSON::encode($params);
            Yii::app()->end();
        } else {
            if($this->url !== false && $this->url !== null)
                Yii::app()->controller->redirect($this->url);
            else
                Yii::app()->controller->redirect(Yii::app()->request->getUrlReferrer());
        }
    }
}
