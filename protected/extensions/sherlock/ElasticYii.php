<?php
use Sherlock\Sherlock;
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 10.08.13
 * Time: 15:25
 * To change this template use File | Settings | File Templates.
 */
class ElasticYii extends \CApplicationComponent
{
    public $server = 'localhost';
    public $port = 9200;

    public $dbServer = 'localhost';
    public $dbPort = 3306;
    public $db = 'test';
    public $dbUser = 'root';
    public $dbPassword = 'root';
    public $riverInterval = '60s';

    /** @var Sherlock  */
    public $sherlock = null;

    public function init()
    {
        parent::init();
        $this->sherlock = new Sherlock();
        $this->sherlock->addNode($this->server, $this->port);
    }

    public function createRiver($index = '_river', $type = 'test')
    {
        $doc_id = '_meta';

        $json_doc = array(
            'type' => 'jdbc',
            'jdbc' => array(
                'driver'             => 'com.mysql.jdbc.Driver',
                'url'                => 'jdbc:mysql://'.$this->dbServer.':'.$this->dbPort.'/'.$this->db,
                'user'               => $this->dbUser,
                'password'           => $this->dbPassword,
                'poll'               => $this->riverInterval,
                'strategy'           => 'simple',
                'sql'                => 'select `t`.id as _id, `t`.*, `user_friend`.friend_id as friends from post `t` left join user_friend on `user_friend`.user_id = `t`.user_id',
                'autocommit'         => 'true',
            ),
            'index' => array(
                'index'              => 'blog_oldbk',
                'type'               => 'post',
                //'bulk_size'          => '100',
                //'max_bulk_requests'  => '30',
                //'bulk_timeout'       => '60s',
            )
        );
        $json_doc = json_encode($json_doc);

        $baseUri = 'http://'.$this->server.':'.$this->port.'/'.$index.'/'.$type.'/'.$doc_id;

        $curl = Yii::app()->curl;
        $curl->options = array(
            'setOptions' => array(
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_FORBID_REUSE => 0,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $json_doc
            )
        );
        $result = $curl->run($baseUri);
        VarDumper::dump($result);
    }

    public function deleteIndex($index = '_river')
    {
        $json_doc = array();
        $json_doc = json_encode($json_doc);

        $baseUri = 'http://'.$this->server.':'.$this->port.'/'.$index.'/';

        $curl = Yii::app()->curl;
        $curl->options = array(
            'setOptions' => array(
                CURLOPT_CUSTOMREQUEST => 'XDELETE',
                CURLOPT_FORBID_REUSE => 0,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $json_doc,
                CURLOPT_TIMEOUT => 30000
            )
        );
        $result = $curl->run($baseUri);
        VarDumper::dump($baseUri);
        VarDumper::dump($curl->error_string);
        VarDumper::dump($result);
    }
}