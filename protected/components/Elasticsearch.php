<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 10.08.13
 * Time: 15:25
 * To change this template use File | Settings | File Templates.
 */
class Elasticsearch extends \CApplicationComponent
{
    public $server = 'localhost';
    public $port = 9200;

    public $dbServer = 'localhost';
    public $dbPort = 3306;
    public $db = 'test';
    public $dbUser = 'root';
    public $dbPassword = 'root';
    public $riverInterval = '60s';

    /** @var Elasticsearch\Client  */
    private $_client = null;

    public function init()
    {
        parent::init();
        $params = array();
        $params['hosts'] = array ($this->server.':'.$this->port);
        $this->_client = new Elasticsearch\Client($params);
    }

    public function search($index, $type, $query, $from)
    {
        $userId = Yii::app()->user->id !== null ? Yii::app()->user->id : 0;
        $params['index'] = $index;
        $params['type'] = $type;
        $params['from'] = $from;
        $params['size'] = \Yii::app()->params['page_size']['post'];
        $params['body'] = array(
            'query' => array(
                'fuzzy_like_this' => array(
                    'like_text' => $query,
                    'fields' => array('login', 'description', 'title'),
                    'analyzer' => 'min',
                    'prefix_length' => 3
                    //"use_dis_max" => true
                )
            ),
            'filter' => array(
                'and' => array(
                    'filters' => array(
                        array(
                            'or' => array(
                                'filters' => array(
                                    array('term' => array('friends' => $userId)),
                                    array('term' => array('user_id' => $userId)),
                                    array('term' => array('view_role' => Access::VIEW_ROLE_ALL)),
                                )
                            )
                        ),
                        array('term' => array('is_moder_deleted' => 0)),
                        array('term' => array('is_deleted' => 0)),
                        array('term' => array('deleted_trunc' => 0)),
                        array('term' => array('is_activated' => 1)),
                    )
                )
            ),
            'highlight' => array(
                'pre_tags' => array('[highlight]'),
                'post_tags' => array('[/highlight]'),
                'fields' => array(
                    'title' => array("fragment_size" => 400, "number_of_fragments" => 1),
                    'login' => array("fragment_size" => 400, "number_of_fragments" => 1),
                    'description' => array("fragment_size" => 400, "number_of_fragments" => 1)
                )
            ),
            'sort' => array(
                array('create_datetime' => 'desc')
            )
        );

        return $this->_client->search($params);
    }

    public function delete($index = 'blog_oldbk')
    {
        $result = $this->_client->indices()->delete(array('index' => $index));
        VarDumper::dump($result);
    }

    public function close()
    {
        $result = $this->_client->indices()->close(array('index' => 'blog_oldbk'));
        VarDumper::dump($result);
    }

    public function open()
    {
        $result = $this->_client->indices()->open(array('index' => 'blog_oldbk'));
        VarDumper::dump($result);
    }

    public function status($index = 'blog_oldbk')
    {
        $indexParams['index']  = 'blog_oldbk';
        $result = $this->_client->indices()->getSettings($indexParams);
        VarDumper::dump($result);
    }

    public function mapping()
    {
        $indexParams['index']  = 'blog_oldbk';
        $indexParams['type'] = 'post';
        $indexParams['ignore_conflicts'] = true;
        $indexParams['body'] = array(
           'post' => array(
               'properties' => array(
                   'create_datetime' => array(
                       'store' => 'yes',
                       'type' => 'string'
                   )
               )
           )
        );


        $result = $this->_client->indices()->putMapping($indexParams);
        VarDumper::dump($result);
    }

    public function putSettings()
    {
        $this->close();
        $indexParams['index'] = 'blog_oldbk';
        $settings = array(
            'analysis' => array(
                'analyzer' => array(
                    'min' => array(
                        'type' => 'custom',
                        'tokenizer' => 'standard',
                        'filter' => array(
                            'lowercase',
                            'mynGram'
                        )
                    )
                ),
            )
        );
        $indexParams['body']['settings'] = $settings;
        $this->_client->indices()->putSettings($indexParams);
        $this->open();
        $result = $this->_client->indices()->getSettings(array('index' => 'blog_oldbk'));
        VarDumper::dump($result);
    }

    public function create($index, $type)
    {
        $indexParams['index']  = $index;

        $settings = array(
            'analysis' => array(
                'analyzer' => array(
                    'ru' => array(
                        'type' => 'custom',
                        'tokenizer' => 'standard',
                        'filter' => array(
                            'lowercase',
                            //'russian_morphology',
                            //'english_morphology',
                            'ru_stemming',
                            'mynGram'
                        )
                    ),
                    'min' => array(
                        'type' => 'custom',
                        'tokenizer' => 'standard',
                        'filter' => array(
                            'lowercase',
                            'mynGram'
                        )
                    )
                ),
                'filter' => array(
                    'ru_stemming' => array(
                        'type' => 'snowball',
                        'language' => 'Russian',
                    ),
                    'mynGram' => array(
                        'type' => 'nGram',
                        'min_gram' => '3',
                        'max_gram' => '10'
                    )
                )
            )
        );

        // Example Index Mapping
        $TypeMapping = array(
            'properties' => array(
                'title' => array(
                    'type' => 'string',
                    'store' => 'yes'
                ),
                'description' => array(
                    'type' => 'string',
                    'store' => 'yes'
                )
            )
        );
        $indexParams['body']['settings'] = $settings;
        $indexParams['body']['mappings']['post'] = $TypeMapping;
        $result = $this->_client->indices()->create($indexParams);
        VarDumper::dump($result);
        //$result = $this->_client->indices()->status(array('index' => $indexParams['index']));
        //VarDumper::dump($result);die;
    }
}