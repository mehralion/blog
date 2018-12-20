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
class Access extends CApplicationComponent
{
    const VIEW_ROLE_ME           = 0;
    const VIEW_ROLE_ALL          = 1;
    const VIEW_ROLE_DARK         = 2;
    const VIEW_ROLE_LIGHT        = 3;
    const VIEW_ROLE_GREY         = 4;
    const VIEW_ROLE_FRIEND       = 5;
    const VIEW_ROLE_COMMUNITY    = 6;

    const CAN_COMMENT            = 5;

    private $_viewRoleArray = array(
        0 => 'Только я',
        1 => 'Все',
        //2 => 'Только темные',
        //3 => 'Только светлые',
        //4 => 'Только серые',
        5 => 'Только друзья'
    );

    private $_viewRoleArrayCommunity = array(
        1 => 'Все',
        6 => 'Сообщество',
    );

    /**
     * @param bool $community
     * @return array
     */
    public function getRoleViewList($community = false)
    {
        return $community === false ? $this->_viewRoleArray : $this->_viewRoleArrayCommunity;
    }


    private static $viewRole = array(
        0 => 'Только я',
        1 => 'Все',
        //2 => 'Только темные',
        //3 => 'Только светлые',
        //4 => 'Только серые',
        5 => 'Только друзья'
    );
    private static $viewRoleCommunity = array(
        1 => 'Все',
        6 => 'Сообщество',
    );
    public static function getRoleName($roleId, $community = false)
    {
        return $community === false ? self::$viewRole[$roleId] : self::$viewRoleCommunity[$roleId];
    }

    /**
     * @param string $alias
     * @return array
     */
    public function GetStringAccess($alias = 't')
    {
        if(Yii::app()->user->isAdmin())
            return array(
                'params' => array(),
                'condition' => ''
            );

        if(Yii::app()->user->isModer())
            return array(
                'params' => array(':access_own_role' => self::VIEW_ROLE_ME),
                'condition' => $alias.'.view_role != :access_own_role'
            );

        $idsFriends = array();
        /** @var UserFriend $friend */
        foreach(UserFriend::getFriends(Yii::app()->user->id) as $friend)
            $idsFriends[] = $friend->friend_id;

        $idsCommunities = array();
        /** @var CommunityUser $community */
        foreach(CommunityUser::getCommunities(Yii::app()->user->id) as $community)
            $idsCommunities[] = $community->community_id;

        return array(
            'condition' => '(
                    ('.$alias.'.view_role = :access_all) or
                    ('.$alias.'.view_role = :access_v_role_friend and '.$alias.'.user_id IN (\''.implode('\',\'', $idsFriends).'\')) or
                    ('.$alias.'.view_role = :access_v_role_community and '.$alias.'.community_id IN (\''.implode('\',\'', $idsCommunities).'\')) or
                    ('.$alias.'.is_community = 0 and '.$alias.'.user_id = :access_owner_id)
                )',
            'params' => array(
                ':access_all' => self::VIEW_ROLE_ALL,
                ':access_owner_id' => Yii::app()->user->id,
                ':access_v_role_friend' => self::VIEW_ROLE_FRIEND,
                ':access_v_role_community' => self::VIEW_ROLE_COMMUNITY,
            )
        );
    }

    /**
     * @param string $alias
     * @param bool $event
     * @return CDbCriteria
     */
    public function GetCriteriaAccess($alias = 't', $event = false)
    {
        $criteriaAccess = new CDbCriteria();
        if(Yii::app()->user->isAdmin())
            return $criteriaAccess;

        if(Yii::app()->user->isModer()) {
            $criteriaAccess->addCondition($alias.'.view_role != :access_own_role');
            $criteriaAccess->addCondition($alias.'.view_role != :access_community');
            $criteriaAccess->params = array(':access_own_role' => self::VIEW_ROLE_ME, ':access_community' => self::VIEW_ROLE_COMMUNITY);
            return $criteriaAccess;
        }

        $criteriaAccess->addCondition($alias.'.view_role = :access_all', 'OR');
        $ids = array();
        /** @var UserFriend $friend */
        foreach(UserFriend::getFriends(Yii::app()->user->id) as $friend)
            $ids[] = $friend->friend_id;
        $criteria = new CDbCriteria();
        $criteria->addCondition($alias.'.view_role = :access_friend');
        if($event)
            $criteria->addInCondition($alias.'.user_owner_id', $ids);
        else
            $criteria->addInCondition($alias.'.user_id', $ids);
        $criteriaAccess->mergeWith($criteria, false);

        $ids = array();
        /** @var CommunityUser $community */
        foreach(CommunityUser::getCommunities(Yii::app()->user->id) as $community)
            $ids[] = $community->community_id;
        $criteria = new CDbCriteria();
        $criteria->addCondition($alias.'.view_role = :access_v_role_community');
        $criteria->addInCondition($alias.'.community_id', $ids);
        $criteriaAccess->mergeWith($criteria, false);

        if($event)
            $criteriaAccess->addCondition($alias.'.user_owner_id = :access_owner_id and '.$alias.'.is_community = 0', 'OR');
        else
            $criteriaAccess->addCondition($alias.'.user_id = :access_owner_id and '.$alias.'.is_community = 0', 'OR');

        $criteriaAccess->params = CMap::mergeArray($criteriaAccess->params, array(
            ':access_all' => self::VIEW_ROLE_ALL,
            ':access_owner_id' => Yii::app()->user->id,
            ':access_friend' => self::VIEW_ROLE_FRIEND,
            ':access_v_role_community' => self::VIEW_ROLE_COMMUNITY,
        ));

        return $criteriaAccess;
    }

    public static function canEdit($userId = null)
    {
        if(null === $userId)
            $userId = Yii::app()->userOwn->id;

        return $userId == Yii::app()->user->id;
    }

}