<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 25.09.12
 * Time: 18:17
 * To change this template use File | Settings | File Templates.
 *
 * @package application.components.base
 */
class ImageUploader
{
    public static $dirs = array(
        'origin' => 'origin',
        'small' => 'thumbs_small',
        'big' => 'thumbs_big',
        'thumbs' => 'thumbs'
    );

    /**
     * @param $base
     * @param $filename
     * @param CUploadedFile $instance
     * @return bool
     * @throws CHttpException
     */
    public function uploadCloud($base, $filename, $instance)
    {
        $baseLocal = Yii::app()->basePath.'/..'.$base;
        if(!is_dir($baseLocal.'/thumbs/'))
            mkdir($baseLocal . '/thumbs/', 0777, true);
        if(!is_dir($baseLocal . '/thumbs_small/'))
            mkdir($baseLocal . '/thumbs_small/', 0777, true);
        if(!is_dir($baseLocal . '/thumbs_big/'))
            mkdir($baseLocal . '/thumbs_big/', 0777, true);
        if(!is_dir($baseLocal . '/origin/'))
            mkdir($baseLocal . '/origin/', 0777, true);

        if(!$instance->saveAs($baseLocal.'/'.$filename))
            throw new \CHttpException(500, "Could not upload file image");

        //Используем функции расширения CImageHandler;
        /*Yii::app()->ih
            ->load($baseLocal . '/' . $filename)
            ->thumb(false, '209')
            ->save($baseLocal . '/thumbs/' . $filename)
            ->reload()
            ->thumb(false, '98')
            ->save($baseLocal . '/thumbs_small/' . $filename)
            ->reload()
            ->thumb('800', '800')
            ->save($baseLocal . '/thumbs_big/' . $filename)
            ->reload()
            ->save($baseLocal . '/origin/' . $filename);

        chmod( $baseLocal . '/' . $filename, 0777 );
        chmod( $baseLocal . '/thumbs/' . $filename, 0777 );
        chmod( $baseLocal . '/thumbs_small/' . $filename, 0777 );
        chmod( $baseLocal . '/thumbs_big/' . $filename, 0777 );
        chmod( $baseLocal . '/origin/' . $filename, 0777 );*/

        try {
            /** @var CImageHandler $imageOriginal */
            $imageOriginal = Yii::app()->ih->load($baseLocal . '/' . $filename);
            if($imageOriginal->getHeight() > 209)
                Yii::app()->ih->load($baseLocal . '/' . $filename)->resize(false, 209)->save($baseLocal . '/thumbs/' . $filename);
            //Yii::app()->image->load($baseLocal . '/' . $filename)->resize(false, 209, Image::HEIGHT)->save($baseLocal . '/thumbs/' . $filename, 0777);
            else
                Yii::app()->ih->load($baseLocal . '/' . $filename)->save($baseLocal . '/thumbs/' . $filename);
            //Yii::app()->image->load($baseLocal . '/' . $filename)->save($baseLocal . '/thumbs/' . $filename, 0777);
            chmod( $baseLocal . '/thumbs/' . $filename, 0777 );

            if($imageOriginal->getHeight() > 98)
                Yii::app()->ih->load($baseLocal . '/' . $filename)->resize(false, 98)->save($baseLocal . '/thumbs_small/' . $filename);
            //Yii::app()->image->load($baseLocal . '/' . $filename)->resize(false, 98, Image::HEIGHT)->save($baseLocal . '/thumbs_small/' . $filename, 0777);
            else
                Yii::app()->ih->load($baseLocal . '/' . $filename)->save($baseLocal . '/thumbs_small/' . $filename);
            //Yii::app()->image->load($baseLocal . '/' . $filename)->save($baseLocal . '/thumbs_small/' . $filename, 0777);
            chmod( $baseLocal . '/thumbs_small/' . $filename, 0777 );

            if($imageOriginal->getHeight() > 800 || $imageOriginal->getWidth() > 800) //can load animation
                Yii::app()->image->load($baseLocal . '/' . $filename)->resize(800, 800, Image::AUTO)->save($baseLocal . '/thumbs_big/' . $filename, 0777);
            else
                Yii::app()->image->load($baseLocal . '/' . $filename)->save($baseLocal . '/thumbs_big/' . $filename, 0777);

            Yii::app()->image->load($baseLocal . '/' . $filename)->save($baseLocal . '/origin/' . $filename, 0777);
            //Yii::app()->ih->load($baseLocal . '/' . $filename)->save($baseLocal . '/origin/' . $filename);
            //chmod( $baseLocal . '/origin/' . $filename, 0777 );

            $error = false;
            if(!Yii::app()->aws->upload($baseLocal . '/thumbs/' . $filename, $base.'/thumbs/' . $filename))
                $error = true;
            if(!Yii::app()->aws->upload($baseLocal . '/thumbs_small/' . $filename, $base.'/thumbs_small/' . $filename))
                $error = true;
            if(!Yii::app()->aws->upload($baseLocal . '/thumbs_big/' . $filename, $base.'/thumbs_big/' . $filename))
                $error = true;
            if(!Yii::app()->aws->upload($baseLocal . '/origin/' . $filename, $base.'/origin/' . $filename))
                $error = true;

            if($error) {
                Yii::app()->aws->delete($base . '/thumbs/' . $filename);
                Yii::app()->aws->delete($base . '/thumbs_small/' . $filename);
                Yii::app()->aws->delete($base . '/thumbs_big/' . $filename);
                Yii::app()->aws->delete($base . '/origin/' . $filename);
                return false;
            } else {
                unlink($baseLocal . '/thumbs/' . $filename);
                unlink($baseLocal . '/thumbs_small/' . $filename);
                unlink($baseLocal . '/thumbs_big/' . $filename);
                unlink($baseLocal . '/origin/' . $filename);
                unlink($baseLocal . '/' .$filename);
                return true;
            }
        } catch (Exception $ex) {
            MyException::log($ex);
            return false;
        }
    }

    /**
     * @param $path
     * @param $file
     * @param CUploadedFile $instance
     * @return bool
     */
    public function uploadFile($path, $file, $instance)
    {
        $path = Yii::app()->basePath.'/..'.$path;
        if(!is_dir($path))
            mkdir($path, 0777, true);

        if(!$instance->saveAs($path.'/'.$file))
            return false;
        else {
            chmod( $path .'/'. $file, 0777 );
            return true;
        }
    }

    /**
     * @return int
     * @throws Exception
     */
    private function getSize()
    {
        if (isset($_SERVER["CONTENT_LENGTH"])) {
            return (int)$_SERVER["CONTENT_LENGTH"];
        } else {
            throw new Exception('Getting content length is not supported.');
        }
    }

    /**
     * @return mixed
     */
    private function getName()
    {
        return Yii::app()->request->getParam('qqfile');
    }

    /**
     * @param $fileName
     * @param $post
     * @param null $newName
     * @return bool|\Guzzle\Service\Resource\Model
     */
    public function crop($fileName, $post, $newName = null)
    {
        $baselocal = Yii::app()->basePath.'/..'.$fileName;

        $image = \Yii::app()->ih
            ->load($baselocal)
            ->crop($post['w'], $post['h'], $post['x'], $post['y']) // $width, $height, $startX = false, $startY = false
            ->resize(200, 200)
            ->save($baselocal);

        /*$image = Yii::app()->image
            ->load($baselocal)
            ->crop($post['w'], $post['h'], $post['y'], $post['x'])
            ->resize(200, 200, Image::NONE)
            ->save($baselocal, 0777);*/
        
        if($image) {
            if(Yii::app()->aws->upload($baselocal, $newName)) {
                unlink($baselocal);
                return true;
            } else
                return false;
        } else
            return false;
    }
}
