<?php

Yii::import('application.models._base.BasePoll');
/**
 * Class Poll
 *
 * Params
 * @property integer $is_deleted
 *
 * Relations
 * @property User $owner
 * @property PollAnswer[] pollAnswers
 * @property Post post
 * @property integer pollUserAnswer
 *
 * Scopes
 * @@method Poll enabled()
 */
class Poll extends BasePoll
{
    public $answer;

    /**
     * @param string $className
     * @return Poll
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function rules() {
        return array(
            array('post_id, user_owner_id, question, create_datetime', 'required'),
            array('post_id, user_owner_id', 'numerical', 'integerOnly'=>true),
            array('question', 'length', 'max'=>255),
            array('date_start, date_end', 'safe'),
            array('date_start, date_end', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, post_id, user_owner_id, question, date_start, date_end, update_datetime, create_datetime', 'safe', 'on'=>'search'),
        );
    }

    public function relations()
    {
        return array(
            'owner' => array(
                self::BELONGS_TO,
                'User',
                'user_owner_id',
                'joinType' => 'inner join'
            ),
            'post' => array(
                self::BELONGS_TO,
                'Post',
                'post_id',
                'joinType' => 'inner join'
            ),
            'hasAnswer' => array(
                self::HAS_ONE,
                'PollAnswer',
                'poll_id',
                'joinType' => 'inner join',
                'on' => 'hasAnswer.value > 0'
            ),
            'pollAnswers' => array(
                self::HAS_MANY,
                'PollAnswer',
                'poll_id',
                'joinType' => 'inner join'
            ),
            'pollUserAnswer' => array(
                self::STAT,
                'PollUserAnswer',
                'poll_id',
            ),
        );
    }

    public function scopes()
    {
        $t = $this->getTableAlias(false, false);
        return array(
            'enabled' => array(
                'condition' => $t.'.is_deleted = 0',
            ),
        );
    }

    public function attributeLabels() {
        return array(
            'id' => Yii::t('app', 'ID'),
            'post_id' => Yii::t('app', 'Post'),
            'user_owner_id' => Yii::t('app', 'User Owner'),
            'question' => Yii::t('app', 'Вопрос'),
            'date_start' => Yii::t('app', 'Date Start'),
            'date_end' => Yii::t('app', 'Date End'),
            'update_datetime' => Yii::t('app', 'Update Datetime'),
            'create_datetime' => Yii::t('app', 'Create Datetime'),
        );
    }
}