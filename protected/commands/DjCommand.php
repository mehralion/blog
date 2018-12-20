<?php
class DjCommand extends CConsoleCommand
{
	public function run($args)
	{
        $page = Yii::app()->curl->run('http://capitalcity.oldbk.com/blog_dj.php?key=I9RdXHeFYNlufui3TrRZ38U8');
        if($page == false)
            return;

        $idsRus = array();
        $idsOld = array();

        $criteria = new CDbCriteria();
        $criteria->addCondition('game_id = :game_id');
        foreach(CJSON::decode($page) as $user) {
            $criteria->params = array(':game_id' => $user['id']);
            /** @var User $User */
            $User = User::model()->find($criteria);
            if(!$User && ApiUser::checkUser(null, null, $user['id']) !== false)
                $User = User::model()->find($criteria);

            if(!$User)
                continue;

            $User->login = iconv('windows-1251', 'utf8', urldecode($user['login']));
            $User->save();

            $user['r1_access'] = 1;
            if($user['r1_access'] == '1') {
                $Dj = UserDj::model()->find('user_id = :user_id and radio_type = :radio_type', array(
                    ':user_id' => $User->id, ':radio_type' => Radio::RADIO_TYPE_RUSFM
                ));
                if(!$Dj)
                    $Dj = new UserDj();

                $Dj->user_id = $User->id;
                $Dj->login = $User->login;
                $Dj->skype = $user['skype'];
                $Dj->icq = $user['icq'];
                $Dj->radio_type = Radio::RADIO_TYPE_RUSFM;
                $Dj->save();

                $idsRus[] = $User->id;
            }

            if($user['r2_access'] == '1') {
                $Dj = UserDj::model()->find('user_id = :user_id and radio_type = :radio_type', array(
                    ':user_id' => $User->id, ':radio_type' => Radio::RADIO_TYPE_OLDFM
                ));
                if(!$Dj)
                    $Dj = new UserDj();

                $Dj->user_id = $User->id;
                $Dj->login = $User->login;
                $Dj->skype = $user['skype'];
                $Dj->icq = $user['icq'];
                $Dj->radio_type = Radio::RADIO_TYPE_OLDFM;
                $Dj->save();

                $idsOld[] = $User->id;
            }
        }

        if(!empty($idsRus)) {
            $criteria = new CDbCriteria();
            $criteria->addNotInCondition('user_id', $idsRus);
            $criteria->addCondition('radio_type = :radio_type');
            $criteria->params = CMap::mergeArray($criteria->params, array(':radio_type' => Radio::RADIO_TYPE_RUSFM));
            UserDj::model()->deleteAll($criteria);
        }

        if(!empty($idsOld)) {
            $criteria = new CDbCriteria();
            $criteria->addNotInCondition('user_id', $idsOld);
            $criteria->addCondition('radio_type = :radio_type');
            $criteria->params = CMap::mergeArray($criteria->params, array(':radio_type' => Radio::RADIO_TYPE_OLDFM));
            UserDj::model()->deleteAll($criteria);
        }
	}
}
