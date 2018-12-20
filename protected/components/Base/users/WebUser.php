<?php
/**
 * Class WebUser контейнер пользователя, который зашел на портал
 *
 * @property integer $level
 * @property integer $id
 * @property integer $game_id
 * @property integer $align
 * @property string $clan
 * @property string $login
 *
 * @package application.components
 */
class WebUser extends CWebUser
{

    const UPDATE_INTERVAL = 5;

    // Store model to not repeat query.
    private $_model;
    private $_profile;
    private $_countNewFriend = null;

    public $clan = '';

    private $params = array(
        'level' => 0,
        'clan' => ''
    );

    private function getSessionId()
    {
        /** @var User $model */
        $model = $this->loadUser($this->id);
        if(!$this->isGuest && $model !== null)
            return $model->game_id;

        if(preg_match('/blog_oldbk=(.*?)(?:;|$)/ui', $_SERVER['HTTP_COOKIE'], $out))
            return $out[1];
        else
            return null;
    }

    private function clearFromOnline()
    {
        $Online = Yii::app()->cache->get('online');
        if($Online === false) $Online = [];
        if(($sessionId = $this->getSessionId()) !== null && isset($Online[$sessionId])) {
            $Online[$sessionId]['update_at'] = 0;
            Yii::app()->cache->set('online', $Online);
        }
    }

    public function init()
    {
        parent::init();

        /** @var User $model */
        $model = $this->loadUser($this->id);

        $Online = Yii::app()->cache->get('online');
        if($Online === false) $Online = [];

        if(($sessionId = $this->getSessionId()) !== null) {
            if($this->isGuest)
                $Online[$sessionId] = [
                    'last_activity' => time(),
                    'update_at' => time(),
                    'login' => 'guest',
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                ];
            else
                $Online[$sessionId] = [
                    'last_activity' => time(),
                    'update_at' => time(),
                    'login' => $model->getFullLogin(),
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                ];
            Yii::app()->cache->set('online', $Online);
        }

        if($this->isGuest || null === $model)
            return;

        if($model->is_blocked) {
            $this->logout();
            $this->setFlash('error', 'Персонаж заблокирован!');
            return;
        }

        foreach($model->attributes as $name => $value)
            $this->params[$name] = $value;

        if($model->is_silenced && time() >= strtotime($model->silence_end)) {
            $model->is_silenced = 0;
            $model->save();
        } elseif($model->is_silenced) {
            $dateEnd = date(Yii::app()->params['siteTimeFormatShort'], strtotime($model->silence_end));
            $this->setFlash('warning', 'На вас наложено заклятие молчания до '.$dateEnd.'.');
        }

        if(time() - 5 * 60 > strtotime($model->last_update)) {
            $gameId = ApiUser::checkUser(null, null, $model->game_id);
            if(false !== $gameId) {
                $model->last_update = date(Yii::app()->params['dbTimeFormat'], time());
                $model->save();
            }
        }
    }

    public function beforeLogin($id, $states, $fromCookie)
    {
        $r = parent::beforeLogin($id, $states, $fromCookie);

        $this->clearFromOnline();

        return $r;
    }

    public function beforeLogout()
    {
        $r = parent::beforeLogout();

        $this->clearFromOnline();

        return $r;
    }

    public function isNewFriend()
    {
        if(null !== $this->_countNewFriend)
            return $this->_countNewFriend;

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.friend_id = :f_id');
        $criteria->with = array('user'); //user = кто кинул мне запрос, я friend
        $criteria->params = array(':f_id' => \Yii::app()->user->id);
        $criteria->scopes = array('pending');

        $dependency = new \CDbCacheDependency('select max(update_datetime) from {{friend_request}} where friend_id = :friend_id');
        $dependency->params = array(':friend_id' => \Yii::app()->user->id);
        $dependency->reuseDependentData = true;

        return $this->_countNewFriend = FriendRequest::model()->cache(1000, $dependency)->count($criteria);
    }

    // Return first name.
    // access it by Yii::app()->user->first_name
    public function getName()
    {
        $user = $this->loadUser($this->id);
        if(null !== $user)
            return $user->login;
        else
            return false;
    }

    public function getAvatar($local = false)
    {
        $user = $this->loadUser($this->id);
        if(null !== $user)
            return $user->getAvatar($local);
        else
            return Yii::app()->theme->baseUrl.'/images/'.Yii::app()->params['no_avatar'];
    }

    public function getFullLogin()
    {
        $user = $this->loadUser($this->id);
        if(null === $user)
            return '';

        $string = CHtml::image('https://i.oldbk.com/i/align_'.$user->align.'.gif');
        if($user->clan !== null)
            $string .= CHtml::image('https://i.oldbk.com/i/klan/'.$user->clan.'.gif');
        $string .= ' <span style="vertical-align:middle;">'.$user->login.' ['.$user->level.']</span>';
        $string .= CHtml::link(CHtml::image('https://i.oldbk.com/i/inf.gif'), 'https://oldbk.com/inf.php?'.$user->game_id, array('target' => '_blank'));
        return $string;
    }

    public function getUserAlign()
    {
        $user = $this->loadUser($this->id);
        if(null === $user)
            return Access::VIEW_ROLE_GREY;
        if($user->align == 3)
            return Access::VIEW_ROLE_DARK;
        elseif($user->align == 6)
            return Access::VIEW_ROLE_LIGHT;
        else
            return Access::VIEW_ROLE_GREY;
    }

    public function getGameId()
    {
        $user = $this->loadUser($this->id);
        if(null === $user)
            return 0;
        else
            return $user->game_id;
    }

    public function isModer()
    {
        /** @var User $user */
        $user = $this->loadUser($this->id);
        if(null === $user || !$user->isModer())
            return false;
        else
            return true;
    }

    public function isAdmin()
    {
        /** @var User $user */
        $user = $this->loadUser($this->id);
        if(null === $user || !$user->isAdmin())
            return false;
        else
            return true;
    }

    public function isSilence()
    {
        /** @var User $user */
        $user = $this->loadUser($this->id);
        if(null === $user || $user->is_silenced)
            return true;
        else
            return false;
    }

    public function hideDeleted()
    {
        return true;
        $user = $this->loadUser($this->id);
        if(null !== $user && !$user->userProfile->hide_deleted)
            return false;
        else
            return true;
    }

    /**
     * @param null $id
     * @return User|null
     */
    protected function loadUser($id=null)
    {
        if($this->_model===null)
        {
            if($id!==null)
                $this->_model = User::model()->with('userProfile')->findByPk($id);
        }
        return $this->_model;
    }

    public function __get($name)
    {
        if(isset($this->params[$name]))
            return $this->params[$name];
        else
            return parent::__get($name);
    }

    public function getCode()
    {
        /** @var User $user */
        $user = $this->loadUser($this->id);
        if($user !== null)
            return $user->code;
        else
            return null;
    }

    public function access($type)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('`t`.user_id = :user_id');
        $criteria->addCondition('`rights_type`.item_name = :name');
        $criteria->with = array('rights_type');
        $criteria->params = array(':user_id' => $this->id, ':name' => $type);
        $model = \Rights::model()->find($criteria);
        return $model === null ? false : true;
    }
}