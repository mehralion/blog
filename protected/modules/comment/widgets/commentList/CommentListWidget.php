<?php
/**
 * Class CommentListWidget
 *
 * @package application.comment.widgets.commentlist
 */
class CommentListWidget extends CWidget
{
    /** @var  GalleryVideo | GalleryImage | GalleryAlbumAudio | Post | Community */
    public $model;
    public $url;
    public $item_type;

	public function run()
	{
        $commentId = Yii::app()->request->getParam('comment_id');
        $criteria = new CDbCriteria();
        $criteria->addCondition('`t`.item_id = :item_id and `t`.item_type = :item_type');
        $criteria->scopes = array(
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus',
            'truncatedStatus',
        );

        $criteria->params = array(
            ':item_id' => $this->model->id,
            ':item_type' => $this->item_type,
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );
        $criteria->with = array(
            'user',
            'report',
            'canRate',
            'info'
        );
        $criteria->order = '`t`.create_datetime asc';

        $dependency = new CDbCacheDependency('SELECT MAX(update_datetime) FROM {{cache_event_item}} where item_id = :item_id and  item_type = :item_type');
        $dependency->params = array(':item_id' => $this->model->id, ':item_type' => $this->item_type);
        $dependency->reuseDependentData = true;

        $pages = new CPagination(CommentItem::model()->cache(Yii::app()->paramsWrap->cache->comment, $dependency)->count($criteria));
        if($commentId) {
            $criteriaOffset = clone $criteria;
            $criteriaOffset->addCondition('`t`.id < :id');
            $criteriaOffset->params = CMap::mergeArray($criteriaOffset->params, array(':id' => $commentId));
            $count = CommentItem::model()->cache(Yii::app()->paramsWrap->cache->comment, $dependency)->count($criteriaOffset);
            $page = floor($count / Yii::app()->paramsWrap->pageSize->comment);
            if($page)
                $pages->currentPage = $page;
        }

        $pages->pageSize = Yii::app()->paramsWrap->pageSize->comment;
        $pages->applyLimit($criteria);

        $Comments = CommentItem::model()->cache(Yii::app()->paramsWrap->cache->comment, $dependency, 10)->findAll($criteria);

		$this->render('index', array(
            'pages' => $pages,
            'models' => $Comments,
            'url' => $this->url,
            'newModel' => new CommentItem(),
            'quote' => !$this->model->is_deleted && !$this->model->is_moder_deleted
        ));
	}
}