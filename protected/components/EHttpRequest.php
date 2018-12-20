<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 15.11.12
 * Time: 15:41
 * To change this template use File | Settings | File Templates.
 *
 * @package application.components
 */
class EHttpRequest extends CHttpRequest
{
    private $_bkAuth = 'http://capitalcity.oldbk.com/blog_auth.php';
    private $_bk_keys = ['29vm8gyuq789agerui67jaer'];
    public function validateCsrfToken($event)
    {
        if($this->getIsPostRequest())
        {
            $advert = $this->getPost('advert');
            $bk_key = $this->getPost('oldbk_key', null);
            if($this->getUrlReferrer() == $this->_bkAuth || $advert == 'advert' || in_array($bk_key, $this->_bk_keys)) {
                $valid = true;
                return true;
            }

            $cookies=$this->getCookies();
            if($cookies->contains($this->csrfTokenName) && isset($_POST[$this->csrfTokenName]) || isset($_GET[$this->csrfTokenName] ))
            {
                $tokenFromCookie=$cookies->itemAt($this->csrfTokenName)->value;
                $tokenFrom=!empty($_POST[$this->csrfTokenName]) ? $_POST[$this->csrfTokenName] : $_GET[$this->csrfTokenName];
                $valid=$tokenFromCookie===$tokenFrom;
            }
            else
                $valid=false;
            if(!$valid)
                throw new CHttpException(400,Yii::t('yii','Lite: The CSRF token could not be verified.'));
        }
    }

    public function getUrlReferrer()
    {
        return isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:Yii::app()->createUrl('/');
    }

    public function getUserHostAddress()
    {
        return isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : '127.0.0.1';
    }
}
