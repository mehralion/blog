<?php

/**
 * This is the model base class for the table "user".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "User".
 *
 * Columns in table "user" available as properties of the model,
 * followed by relations of table "user" available as properties of the model.
 *
 * @property integer $id
 * @property string $login
 * @property integer $level
 * @property string $align
 * @property integer $game_id
 * @property string $clan
 *
 * @property CommentItem[] $commentItems
 * @property EventComment[] $eventComments
 * @property EventComment[] $eventComments1
 * @property EventFriend[] $eventFriends
 * @property EventFriend[] $eventFriends1
 * @property EventItem[] $eventItems
 * @property FriendRequest[] $friendRequests
 * @property FriendRequest[] $friendRequests1
 * @property GalleryAlbum[] $galleryAlbums
 * @property GalleryImage[] $galleryImages
 * @property GalleryVideo[] $galleryVideos
 * @property Opros[] $oproses
 * @property OprosAnswer[] $oprosAnswers
 * @property Post[] $posts
 * @property RatingItem[] $ratingItems
 * @property RatingUser[] $ratingUsers
 * @property RatingUser[] $ratingUsers1
 * @property UserFriend[] $userFriends
 * @property UserFriend[] $userFriends1
 * @property UserProfile $userProfile
 *
 * @package application.user.models
 */
abstract class BaseUser extends MyAR
{
    /**
     * @param string $className
     * @return BaseUser
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    /**
     * @return string
     */
    public function tableName() {
		return 'user';
	}

    /**
     * @param int $n
     * @return string
     */
    public static function label($n = 1) {
		return Yii::t('app', 'User|Users', $n);
	}

    /**
     * @return array|string
     */
    public static function representingColumn() {
		return 'login';
	}

    /**
     * @return array
     */
    public function rules() {
		return array(
			array('login, game_id', 'required'),
			array('level, game_id', 'numerical', 'integerOnly'=>true),
			array('login, align, clan', 'length', 'max'=>255),
			array('level, align, clan', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, login, level, align, game_id, clan', 'safe', 'on'=>'search'),
		);
	}

    /**
     * @return array
     */
    public function relations() {
		return array(
			'commentItems' => array(self::HAS_MANY, 'CommentItem', 'user_id'),
			'eventComments' => array(self::HAS_MANY, 'EventComment', 'user_id'),
			'eventComments1' => array(self::HAS_MANY, 'EventComment', 'user_owner_id'),
			'eventFriends' => array(self::HAS_MANY, 'EventFriend', 'reciver_id'),
			'eventFriends1' => array(self::HAS_MANY, 'EventFriend', 'sender_id'),
			'eventItems' => array(self::HAS_MANY, 'EventItem', 'user_id'),
			'friendRequests' => array(self::HAS_MANY, 'FriendRequest', 'friend_id'),
			'friendRequests1' => array(self::HAS_MANY, 'FriendRequest', 'user_id'),
			'galleryAlbums' => array(self::HAS_MANY, 'GalleryAlbum', 'user_id'),
			'galleryImages' => array(self::HAS_MANY, 'GalleryImage', 'user_id'),
			'galleryVideos' => array(self::HAS_MANY, 'GalleryVideo', 'user_id'),
			'oproses' => array(self::HAS_MANY, 'Opros', 'user_id'),
			'oprosAnswers' => array(self::HAS_MANY, 'OprosAnswer', 'user_id'),
			'posts' => array(self::HAS_MANY, 'Post', 'user_id'),
			'ratingItems' => array(self::HAS_MANY, 'RatingItem', 'user_id'),
			'ratingUsers' => array(self::HAS_MANY, 'RatingUser', 'send_user_id'),
			'ratingUsers1' => array(self::HAS_MANY, 'RatingUser', 'user_id'),
			'userFriends' => array(self::HAS_MANY, 'UserFriend', 'friend_id'),
			'userFriends1' => array(self::HAS_MANY, 'UserFriend', 'user_id'),
			'userProfile' => array(self::HAS_ONE, 'UserProfile', 'user_id'),
		);
	}

    /**
     * @return array
     */
    public function pivotModels() {
		return array(
		);
	}

    /**
     * @return array
     */
    public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'login' => Yii::t('app', 'Login'),
			'level' => Yii::t('app', 'Level'),
			'align' => Yii::t('app', 'Align'),
			'game_id' => Yii::t('app', 'Game'),
			'clan' => Yii::t('app', 'Clan'),
			'commentItems' => null,
			'eventComments' => null,
			'eventComments1' => null,
			'eventFriends' => null,
			'eventFriends1' => null,
			'eventItems' => null,
			'friendRequests' => null,
			'friendRequests1' => null,
			'galleryAlbums' => null,
			'galleryImages' => null,
			'galleryVideos' => null,
			'oproses' => null,
			'oprosAnswers' => null,
			'posts' => null,
			'ratingItems' => null,
			'ratingUsers' => null,
			'ratingUsers1' => null,
			'userFriends' => null,
			'userFriends1' => null,
			'userProfile' => null,
		);
	}

    /**
     * @return CActiveDataProvider
     */
    public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('login', $this->login, true);
		$criteria->compare('level', $this->level);
		$criteria->compare('align', $this->align, true);
		$criteria->compare('game_id', $this->game_id);
		$criteria->compare('clan', $this->clan, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}