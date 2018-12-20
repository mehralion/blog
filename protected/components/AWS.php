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
class AWS extends CApplicationComponent
{
    public $key;
    public $secret;
    public $bucket;
    public $distribution = 'EX8XASQAC27AD';

    /** @var  \Aws\S3\S3Client */
    private $_s3;
    private $_client;

    /**
     * @param $filePath //"Yii::app()->theme->basePath.'/images/album-bg2.jpg'"
     * @param $fileUrl //"/images/album-bg1.jpg"
     * @return bool|\Guzzle\Service\Resource\Model
     */
    public function upload($filePath, $fileUrl)
    {
        if(file_exists($filePath)) {
            $fileUrl = ltrim($fileUrl, '/');
			// Upload data.
			try {
				$r = $this->_s3->putObject(array(
					'Bucket' => $this->bucket,
					'Key'    => $fileUrl,
					//'Body'   => 'Hello, world!',
					'ACL'    => 'public-read',
					'SourceFile' => realpath($filePath),
				));
			} catch (Exception $ex) {
				var_dump($ex->getMessage());die;
			}

            //$r = $this->_s3->upload($this->bucket, $fileUrl, fopen($filePath, 'r'), 'public-read');
            $obj = $this->getObject($fileUrl);
            return $obj;
        } else
            return false;
    }

    /**
     * @param $fileUrl
     * @return Model
     */
    public function delete($fileUrl)
    {
        if($this->getObject($fileUrl))
            $this->_s3->deleteObject(array(
                'Bucket' => $this->bucket,
                'Key' => $fileUrl
            ));
    }

    /**
     * @param $fileUrl
     * @return string
     */
    public function getFileUrl($fileUrl)
    {
        return $this->_s3->getObjectUrl($this->bucket, $fileUrl);
    }

    /**
     * @param $fileUrl
     * @return bool
     */
    public function getObject($fileUrl)
    {
        try {
            $this->_s3->getObject(array(
                'Bucket' => $this->bucket,
                'Key' => $fileUrl
            ));
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function init()
    {
        $this->_s3 = new Aws\S3\S3Client([
            'version' => 'latest',
            'region'  => 'eu-west-1',
            'credentials' => [
                'key'       => $this->key,
                'secret'    => $this->secret,
            ]
        ]);
    }
}