<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 12.10.13
 * Time: 3:12
 * To change this template use File | Settings | File Templates.
 * @property string $name Theme name.
 * @property string $baseUrl The relative URL to the theme folder (without ending slash).
 * @property string $basePath The file path to the theme folder.
 * @property string $viewPath The path for controller views. Defaults to 'ThemeRoot/views'.
 * @property string $systemViewPath The path for system views. Defaults to 'ThemeRoot/views/system'.
 * @property string $skinPath The path for widget skins. Defaults to 'ThemeRoot/views/skins'.
 * @property string $uploadGallery
 * @property string $uploadGalleryLink
 * @property string $uploadAlbum
 * @property string $uploadAlbumLink
 * @property string $uploadAvatar
 * @property string $uploadAvatarLink
 */

class Theme extends CApplicationComponent
{
    public $name;
    public $domain;
    private $_name;
    private $_basePath;
    private $_baseUrl;
    private $_uploadGallery;
    private $_uploadAlbum;
    private $_uploadAvatar;

    public function init()
    {
        $this->_name = $this->name;
        if(!YII_DEBUG)
            $this->_baseUrl = $this->domain.'/themes/'.$this->name;
        else
            $this->_baseUrl = '/themes/'.$this->name;
        $this->_basePath = Yii::app()->basePath.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$this->name;
        $this->_uploadGallery = '/uploads/images';
        $this->_uploadAlbum = '/uploads/albums';
        $this->_uploadAvatar = '/uploads/avatars';
    }

    /**
     * @return string theme name
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return string the relative URL to the theme folder (without ending slash)
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     * @return string the relative URL to the theme folder (without ending slash)
     */
    public function getUploadGallery()
    {
        return $this->_uploadGallery;
    }
    /**
     * @return string the relative URL to the theme folder (without ending slash)
     */
    public function getUploadGalleryLink()
    {
        return $this->domain.$this->_uploadGallery;
    }

    /**
     * @return string the relative URL to the theme folder (without ending slash)
     */
    public function getUploadAlbum()
    {
        return $this->_uploadAlbum;
    }
    /**
     * @return string the relative URL to the theme folder (without ending slash)
     */
    public function getUploadAlbumLink()
    {
        return $this->domain.$this->_uploadAlbum;
    }

    /**
     * @return string the relative URL to the theme folder (without ending slash)
     */
    public function getUploadAvatar()
    {
        return $this->_uploadAvatar;
    }
    /**
     * @return string the relative URL to the theme folder (without ending slash)
     */
    public function getUploadAvatarLink()
    {
        return $this->domain.$this->_uploadAvatar;
    }

    /**
     * @return string the file path to the theme folder
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }

    /**
     * @return string the path for controller views. Defaults to 'ThemeRoot/views'.
     */
    public function getViewPath()
    {
        return $this->_basePath.DIRECTORY_SEPARATOR.'views';
    }

    /**
     * @return string the path for system views. Defaults to 'ThemeRoot/views/system'.
     */
    public function getSystemViewPath()
    {
        return $this->getViewPath().DIRECTORY_SEPARATOR.'system';
    }

    /**
     * @return string the path for widget skins. Defaults to 'ThemeRoot/views/skins'.
     * @since 1.1
     */
    public function getSkinPath()
    {
        return $this->getViewPath().DIRECTORY_SEPARATOR.'skins';
    }

    /**
     * Finds the view file for the specified controller's view.
     * @param CController $controller the controller
     * @param string $viewName the view name
     * @return string the view file path. False if the file does not exist.
     */
    public function getViewFile($controller,$viewName)
    {
        $moduleViewPath=$this->getViewPath();
        if(($module=$controller->getModule())!==null)
            $moduleViewPath.='/'.$module->getId();
        return $controller->resolveViewFile($viewName,$this->getViewPath().'/'.$controller->getUniqueId(),$this->getViewPath(),$moduleViewPath);
    }

    /**
     * Finds the layout file for the specified controller's layout.
     * @param CController $controller the controller
     * @param string $layoutName the layout name
     * @return string the layout file path. False if the file does not exist.
     */
    public function getLayoutFile($controller,$layoutName)
    {
        $moduleViewPath=$basePath=$this->getViewPath();
        $module=$controller->getModule();
        if(empty($layoutName))
        {
            while($module!==null)
            {
                if($module->layout===false)
                    return false;
                if(!empty($module->layout))
                    break;
                $module=$module->getParentModule();
            }
            if($module===null)
                $layoutName=Yii::app()->layout;
            else
            {
                $layoutName=$module->layout;
                $moduleViewPath.='/'.$module->getId();
            }
        }
        elseif($module!==null)
            $moduleViewPath.='/'.$module->getId();

        return $controller->resolveViewFile($layoutName,$moduleViewPath.'/layouts',$basePath,$moduleViewPath);
    }

}