<?php

/**
 * Class User
 *
 * @property UserProfile $userProfile
 * @property string $password
 * @property string silence_end
 * @property int is_silenced
 * @property int is_blocked
 * @property string last_update
 * @property integer is_dj
 * @property string skype
 * @property string icq
 * @property integer radio_type
 * @property UserDj $userDj
 * @property string code
 *
 * @package application.user.models
 *
 */
class User extends BaseUser
{

    public $password = '';

    /**
     * @param string $className
     * @return User
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    /**
     * @return array
     */
    public function relations() {
        return array(
            'userProfile' => array(
                self::HAS_ONE,
                'UserProfile',
                'user_id',
                'joinType' => 'inner join'
            ),
        );
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
     * @param bool $loginLink
     * @param null $login
     * @return string
     */
    public function getFullLogin($loginLink = false, $login = null)
    {
        $login = null === $login ? $this->login : Yii::app()->stringHelper->parseTag($login);
        $string = CHtml::image('https://i.oldbk.com/i/align_'.$this->align.'.gif').' ';
        if($this->clan !== null && $this->clan != '')
            $string .= CHtml::image('https://i.oldbk.com/i/klan/'.$this->clan.'.gif', $this->clan, array('title' => $this->clan)).' ';
        $string .= '<span style="vertical-align:middle;">'.CHtml::link($login.' ['.$this->level.']', Yii::app()->createUrl(
                '/user/profile/show', array('gameId' => $this->game_id)
            )
        ).'</span>';
        $string .= CHtml::link(
            CHtml::image('https://i.oldbk.com/i/inf.gif', '', array('style' => 'margin-bottom:2px')), 'https://oldbk.com/inf.php?'.$this->game_id, array(
                'target' => '_blank')
        );
        return $string;
    }

    public static function getLogin($align, $clan, $login, $level, $gameId)
    {
        $string = CHtml::image('https://i.oldbk.com/i/align_'.$align.'.gif').' ';
        if($clan !== null && $clan != '')
            $string .= CHtml::image('https://i.oldbk.com/i/klan/'.$clan.'.gif', $clan, array('title' => $clan)).' ';
        $string .= '<span style="vertical-align:middle;">'.CHtml::link($login.' ['.$level.']', Yii::app()->createUrl(
                    '/user/profile/show', array('gameId' => $gameId)
                )
            ).'</span>';
        $string .= CHtml::link(
            CHtml::image('https://i.oldbk.com/i/inf.gif', '', array('style' => 'margin-bottom:2px')), 'https://oldbk.com/inf.php?'.$gameId, array(
                'target' => '_blank')
        );
        return $string;
    }

    /**
     * @param bool $local
     * @return string
     */
    public function getAvatar($local = false)
    {
        if(null === $this->userProfile || null === $this->userProfile->avatar_path || $this->userProfile->avatar_path == "")
            return Yii::app()->theme->baseUrl.'/images/'.Yii::app()->params['no_avatar'];

        $file = Yii::app()->basePath.'/../uploads/avatars/'.$this->userProfile->avatar_path;
        if(file_exists($file) && !$this->userProfile->is_croped && $local)
            return $this->userProfile->getBaseUrl().'/'.$this->userProfile->avatar_path;
        elseif($this->userProfile->is_croped)
            return Yii::app()->theme->uploadAvatarLink.'/'.$this->userProfile->avatar_path;
        else
            return Yii::app()->theme->baseUrl.'/images/'.Yii::app()->params['no_avatar'];
    }

    /** @var array  */
    private $moderAlign = array('1.7', '1.9', '1.91', '1.99');
    private $adminClan = array('adminion', 'rаdminion', 'radminion');
    private $logins = array();
    /**
     * @return bool
     */
    public function isModer()
    {
        return in_array($this->align, $this->moderAlign) || in_array(strtolower($this->clan), $this->adminClan) || in_array($this->login, $this->logins);
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return in_array(strtolower($this->clan), $this->adminClan) || in_array($this->login, $this->logins);
    }

    /**
     * @return array
     */
    public function getModerList()
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('align', $this->moderAlign);
        return GxHtml::listDataEx(User::model()->findAll($criteria), 'id', 'login');
    }

    public static function buildIcq($icq = '')
    {
        $icq = trim($icq);
        if($icq !== null && $icq != '' && $icq > 0)
            return '<img src="https://web.icq.com/whitepages/online?icq='.$icq.'&img=5" alt="Статус ICQ пользователя '.$icq.'" /> '.$icq;
        else
            return '';
    }

    public static function buildSkype($skype = '')
    {
        $skype = trim($skype);
        if($skype !== null && $skype != '')
            return '<img width="16" height="16" src="'.Yii::app()->theme->baseUrl.'/images/icons/skype.ico'.'"><a href="skype:'.$skype.'?chat">'.$skype.'</a>';
        else
            return '';
    }
}