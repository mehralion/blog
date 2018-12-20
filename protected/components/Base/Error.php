<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 21.11.12
 * Time: 15:19
 * To change this template use File | Settings | File Templates.
 *
 * @package application.components.base.Error
 */
class Error extends CApplicationComponent
{
    private $_errors = array();

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
}
