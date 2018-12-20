<?php
Yii::import('application.models._base.BasePostTag');
/**
 * Class PostTag
 *
 * @property Post $post
 * @property Tag $tag
 *
 * @package application.post.models
 */
class PostTag extends BasePostTag
{
    /**
     * @param string $className
     * @return PostTag
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    /**
     * @return array
     */
    public function relations() {
        return array(
            'post' => array(
                self::BELONGS_TO,
                'Post',
                'post_id',
                'joinType' => 'inner join'
            ),
            'tag' => array(
                self::BELONGS_TO,
                'Tag',
                'tag_id',
                'joinType' => 'inner join'
            ),
        );
    }
}