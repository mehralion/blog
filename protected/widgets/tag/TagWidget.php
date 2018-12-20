<?php
/**
 * Class TagWidget
 *
 * @package application.widgets.tag
 */
class TagWidget extends CWidget {

    /**
     * Html ID
     * @var string
     */
    public $id = 'tagWidget';

    /**
     * Initial tags
     * @var array
     */
    public $tags = array();

    /**
     * The url to get json data
     * @var string
     */
    public $url;
    
    public function init()
    {
        // this method is called by CController::beginWidget()
    }

    public function run()
    {
        //$this->tags = json_encode($this->tags);
        // this method is called by CController::endWidget()
        $this->render('TagView', array(
            'id' => $this->id,
            'tags' => $this->tags,
            'url' => $this->url,
            'tag_it' => dirname(__FILE__),
        ));
    }
}