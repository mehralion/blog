<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 05.04.14
 * Time: 21:27
 */

namespace application\modules\radio\components;

class MуCaptcha extends \CCaptchaAction
{
    public $testLimit = 1;

    /**
     * Generates a new verification code.
     * @return string the generated verification code
     */
    protected function generateVerifyCode()
    {
        if($this->minLength<3)
            $this->minLength=3;
        if($this->maxLength>20)
            $this->maxLength=20;
        if($this->minLength>$this->maxLength)
            $this->maxLength=$this->minLength;
        $length=rand($this->minLength,$this->maxLength);

        // Тут указываем символы которые будут
        // выводится у нас на капче.
        $letters='1234567890';
        $code='';
        for($i=0;$i<$length;++$i)
        {
            $code.=$letters[rand(0, strlen($letters)-1)];
        }
        return $code;
    }
} 