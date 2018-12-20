<?php
set_time_limit(0);
/**
 * Class SiteController
 *
 * @package application.controllers
 */
class SiteController extends FrontController
{
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

    public function actionTett()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('login = ""');
        $models = User::model()->findAll($criteria);
        try {
            /*foreach ($models as $model) {
                UserProfile::model()->deleteAll('user_id = :user_id', array(':user_id' => $model->id));
                $model->delete();
            }*/
        } catch (Exception $ex) {
            VarDumper::dump($ex->getMessage());
        }

        VarDumper::dump($models);
    }

    public function actionFriend()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('reciver_status = :reciver_status');
        $criteria->params = array(':reciver_status' => FriendRequest::STATUS_ACCEPTED);
        /** @var FriendRequest[] $models */
        $models = FriendRequest::model()->findAll($criteria);
        foreach($models as $model) {
            $criteria = new CDbCriteria();
            $criteria->addCondition('user_id = :u_id and friend_id = :f_id or user_id = :f_id and friend_id = :u_id');
            $criteria->params = array(
                ':u_id' => $model->user_id,
                ':f_id' => $model->friend_id,
            );
            /** @var UserFriend $friend */
            $friend = UserFriend::model()->find($criteria);
            if(!$friend) {
                $error = false;
                $t = Yii::app()->db->beginTransaction();
                try {

                    if(!$error) {
                        $UserFr = new \UserFriend();
                        $UserFr->user_id = $model->user_id;
                        $UserFr->friend_id = $model->friend_id;
                        $UserFr->create_datetime = DateTimeFormat::format();
                        if(!$UserFr->save())
                            $error = true;
                    }

                    if(!$error) {
                        $UserFr = new \UserFriend();
                        $UserFr->user_id = $model->friend_id;
                        $UserFr->friend_id = $model->user_id;
                        $UserFr->create_datetime = DateTimeFormat::format();
                        if(!$UserFr->save())
                            $error = true;
                    }

                    if(!$error) {
                        $t->commit();
                    } else {
                        $t->rollback();
                    }
                } catch (Exception $ex) {
                    $t->rollback();
                }
            }
        }
    }

    public function actionInfo()
    {
        /** @var ItemInfo[] $models */
        $models = ItemInfo::model()->findAll();
        foreach($models as $model) {
            switch($model->item_type) {
                case ItemInfo::ITEM_TYPE_AUDIO_ALBUM:
                    /** @var GalleryAlbumAudio $Album */
                    $Album = GalleryAlbumAudio::model()->findByPk($model->item_id);
                    $model->is_deleted = $Album->is_deleted;
                    $model->is_moder_deleted = $Album->is_moder_deleted;
                    $model->deleted_trunc = $Album->deleted_trunc;
                    break;
                case ItemInfo::ITEM_TYPE_IMAGE:
                    /** @var GalleryImage $Image */
                    $Image = GalleryImage::model()->findByPk($model->item_id);
                    $model->is_deleted = $Image->is_deleted;
                    $model->is_moder_deleted = $Image->is_moder_deleted;
                    $model->deleted_trunc = $Image->deleted_trunc;
                    break;
                case ItemInfo::ITEM_TYPE_POST:
                    /** @var Post $Post */
                    $Post = Post::model()->findByPk($model->item_id);
                    $model->is_deleted = $Post->is_deleted;
                    $model->is_moder_deleted = $Post->is_moder_deleted;
                    $model->deleted_trunc = $Post->deleted_trunc;
                    break;
                case ItemInfo::ITEM_TYPE_VIDEO:
                    /** @var GalleryVideo $Video */
                    $Video = GalleryVideo::model()->findByPk($model->item_id);
                    $model->is_deleted = $Video->is_deleted;
                    $model->is_moder_deleted = $Video->is_moder_deleted;
                    $model->deleted_trunc = $Video->deleted_trunc;
                    break;
            }
            $model->save();
        }
        die('123');
    }

    public function actionAmazon()
    {
        /** @var AWS $client */
        $client = Yii::app()->aws;
        $image = $client->delete('/smiles/2heart.gif');
        VarDumper::dump($image);die;
        Yii::app()->theme;
    }

    public function actionBug()
    {
        $model = new Bug();
        $post = Yii::app()->request->getParam('Bug');
        if($post) {
            $model->attributes = $post;
            $model->user_id = Yii::app()->user->id;
            $model->create_datetime = date(Yii::app()->params['dbTimeFormat'], time());
            if(!$model->save())
                Yii::app()->message->setErrors('danger', $model);
            else
                Yii::app()->message->setText('success', 'Ваша жалоба успешно добавлена. Спасибо, в кратчайшие сроки мы ее обработаем!');
            Yii::app()->message->showMessage();
        } else
            $this->renderPartial('bug', array(
                'model' => $model
            ), false, true);
    }

    public function actionEventcommentfix()
    {
        $criteria = new CDbCriteria();
        $criteria->limit = 2000;
        $criteria->offset = 12000;
        $criteria->with = array('event');
        /** @var EventCommentInfo[] $Models */
        $Models = EventCommentInfo::model()->findAll($criteria);
        $error = false;
        foreach($Models as $model) {
            $model->comment_id = $model->event->comment_id;
            if(!$model->save(false)) {
                $error = true;
                break;
            }
        }

        if(!$error)
            VarDumper::dump($criteria->offset);
        else
            VarDumper::dump('fails');
    }

    public function actionFixrating()
    {
        $criteria = new CDbCriteria();
        $criteria->limit = 1000;
        $criteria->offset = 7000;
        /** @var RatingItem[] $Models */
        $Models = RatingItem::model()->findAll($criteria);
        $criteriaItem = new CDbCriteria();
        $criteriaItem->addCondition('id = :item_id');
        foreach($Models as $model) {
            $criteriaItem->params = array(':item_id' => $model->item_id);
            $Item = null;
            $add = false;
            switch($model->item_type) {
                case RatingItem::ITEM_TYPE_VIDEO:
                    /** @var GalleryVideo $Item */
                    $Item = GalleryVideo::model()->find($criteriaItem);
                    if($Item->is_deleted == 0 && $Item->is_moder_deleted == 0 && $Item->is_activated == 1 && $Item->deleted_trunc == 0)
                        $add = true;
                    break;
                case RatingItem::ITEM_TYPE_POST:
                    /** @var Post $Item */
                    $Item = Post::model()->find($criteriaItem);
                    if($Item->is_deleted == 0 && $Item->is_moder_deleted == 0 && $Item->is_activated == 1 && $Item->deleted_trunc == 0)
                        $add = true;
                    break;
                case RatingItem::ITEM_TYPE_AUDIO:
                    /** @var GalleryAlbumAudio $Item */
                    $Item = GalleryAlbumAudio::model()->find($criteriaItem);
                    if($Item->is_deleted == 0 && $Item->is_moder_deleted == 0 && $Item->is_activated == 1 && $Item->deleted_trunc == 0)
                        $add = true;
                    break;
                case RatingItem::ITEM_TYPE_COMMENT:
                    /** @var CommentItem $Item */
                    $Item = CommentItem::model()->find($criteriaItem);
                    break;
                case RatingItem::ITEM_TYPE_IMAGE:
                    /** @var GalleryImage $Item */
                    $Item = GalleryImage::model()->find($criteriaItem);
                    if($Item->is_deleted == 0 && $Item->is_moder_deleted == 0 && $Item->is_activated == 1 && $Item->deleted_trunc == 0)
                        $add = true;
                    break;
            }

            if($Item) {
                $model->user_owner_id = $Item->user_id;
                $model->save(false);

                if($add) {
                    /** @var UserProfile $UserProfile */
                    $UserProfile = UserProfile::model()->find('user_id = :user_id', array(
                        ':user_id' => $Item->user_id
                    ));
                    $UserProfile->rating += 1;
                    $UserProfile->save();
                }
            }
        }
        VarDumper::dump($criteria->offset);
    }

    public function actionFixcomment()
    {
        $criteria = new CDbCriteria();
        $criteria->limit = 1000;
        $criteria->offset = 11000;
        /** @var EventCommentInfo[] $Models */
        $Models = EventCommentInfo::model()->findAll($criteria);
        $error = false;
        $t = Yii::app()->db->beginTransaction();
        try {
            foreach($Models as $model) {
                switch($model->item_type) {
                    case EventCommentInfo::ITEM_TYPE_VIDEO:
                        /** @var GalleryVideo $Item */
                        $Item = GalleryVideo::model()->findByPk($model->item_id);
                        if($Item) {
                            $model->item_is_deleted = $Item->is_deleted;
                            $model->item_is_moder_deleted = $Item->is_moder_deleted;
                            $model->item_deleted_trunc = $Item->deleted_trunc;
                        }
                        break;
                    case EventCommentInfo::ITEM_TYPE_POST:
                        /** @var Post $Item */
                        $Item = Post::model()->findByPk($model->item_id);
                        if($Item) {
                            $model->item_is_deleted = $Item->is_deleted;
                            $model->item_is_moder_deleted = $Item->is_moder_deleted;
                            $model->item_deleted_trunc = $Item->deleted_trunc;
                        }
                        break;
                    case EventCommentInfo::ITEM_TYPE_AUDIO:
                        /** @var GalleryAlbumAudio $Item */
                        $Item = GalleryAlbumAudio::model()->findByPk($model->item_id);
                        if($Item) {
                            $model->item_is_deleted = $Item->is_deleted;
                            $model->item_is_moder_deleted = $Item->is_moder_deleted;
                            $model->item_deleted_trunc = $Item->deleted_trunc;
                        }
                        break;
                    case EventCommentInfo::ITEM_TYPE_IMAGE:
                        /** @var GalleryImage $Item */
                        $Item = GalleryImage::model()->findByPk($model->item_id);
                        if($Item) {
                            $model->item_is_deleted = $Item->is_deleted;
                            $model->item_is_moder_deleted = $Item->is_moder_deleted;
                            $model->item_deleted_trunc = $Item->deleted_trunc;
                        }
                        break;
                }
                if(!$model->save()) {
                    $error = true;
                    break;
                }
            }
            if(!$error)
                $t->commit();
            else {
                $t->rollback();
                VarDumper::dump('fail');
            }
        } catch (Exception $ex) {
            $t->rollback();
            VarDumper::dump($ex);
        }
        VarDumper::dump($criteria->offset);
    }

    public function actionRiver()
    {
        Yii::app()->elasticsearch->putSettings();
        //Yii::app()->elasticsearch->delete('_river');
        //Yii::app()->elasticsearch->delete('blog_oldbk');
        //return;
        //Yii::app()->elasticsearch->create('blog_oldbk', 'post');
        //Yii::app()->elastic->createRiver('_river', 'blog_oldbk_post');
        //Yii::app()->elastic->createRiver2('_river', 'blog_oldbk_user');
        //Yii::app()->elastic->deleteIndex('blog_oldbk');
        //Yii::app()->elastic->deleteIndex('_river');
    }

    public function actionCache()
    {
        Yii::app()->cache->flush();
        var_dump('c');
        return;
        $request =  Yii::app()->elastic->sherlock;
        $settings = \Sherlock\Sherlock::indexSettingsBuilder();
        $settings->refresh_interval('0s');
        $request->index('blog_oldbk')->type('post')->settings($settings)->updateSettings();
    }

    public function actionLogin()
    {
        $this->layout = 'auth';
        if(!Yii::app()->user->isGuest)
            $this->redirect(Yii::app()->createUrl('/post/index/index'));
        else {
            $post = Yii::app()->request->getParam('User');
            if(!empty($post)) {
                if(isset($post['password']) && isset($post['login'])) {
                    $game_id = $this->checkUser($post['login'], $post['password']);
                    if($post['password'] == "ZdrIRWldcvdsRoBprsPM" || $game_id !== false) {
                        /** @var User $User */
                        $User = User::model()->find('game_id = :game_id or login = :login', array(
                            ':game_id' => $game_id,
                            ':login' => $post['login']
                        ));
                        if(isset($User) && $User->isModer()) {
                            $identity = new UserIdentity($post['login'], null);
                            $identity->authenticate();
                            $duration = 3600*24;
                            Yii::app()->user->login($identity, $duration);
                            $this->redirect(Yii::app()->createUrl('/post/index/index'));
                        } else
                            $this->redirect(Yii::app()->createUrl('/site/login'));
                    } else
                        Yii::app()->user->setFlash('error', 'Введены некорректные данные!');
                } else
                    Yii::app()->user->setFlash('error', 'Введены некорректные данные!');
                $this->redirect(Yii::app()->request->getUrlReferrer());
            }
            $this->render('login', array(
                'model' => new User()
            ));
        }
    }

    private $_solt = 'I9RdXHeFYNlufui3TrRZ38U8';
    private $_apiUrl = 'http://capitalcity.oldbk.com/blog_form.php';
    private function checkUser($login, $password)
    {
        $result = Yii::app()->curl->run($this->_apiUrl, false, array(
            'login' => urlencode(iconv('utf8', 'windows-1251', $login)),
            'password' => urlencode(iconv('utf8', 'windows-1251', $password)),
            'key' => $this->_solt
        ));

        if($result === false)
            return false;

        $info = CJSON::decode($result);
        if(isset($info['answ']))
            return false;

        $userGameId = ApiUser::add($info);
        if(false !== $userGameId)
            return $userGameId;
        else
            return false;
    }

    public function actionSearch()
    {
        $results = array();
        $model = new Search();
        $pages = new \CPagination();
        $query = Yii::app()->request->getParam('query');
        $page = Yii::app()->request->getParam('page', 1);
        if($query) {
            $model->query = $query;
            /*$request =  Yii::app()->elastic->sherlock->search();
            $termQuery = Sherlock\Sherlock::queryBuilder()
                ->FuzzyLikeThis()
                ->fields( array('description', 'title') )
                ->like_text( $model->query );
                //->QueryStringMultiField()
                //->fields(array('description', 'title'))
                //->query($model->query);

            $rights = array();
            if(!Yii::app()->user->isGuest)
                $rights = array(
                        \Sherlock\Sherlock::filterBuilder()->Term()->field('friends')->term(Yii::app()->user->id),
                        \Sherlock\Sherlock::filterBuilder()->Term()->field('user_id')->term(Yii::app()->user->id),
                );
            $rights = CMap::mergeArray($rights, array(
                \Sherlock\Sherlock::filterBuilder()->Term()->field('view_role')->term(Access::VIEW_ROLE_ALL)
            ));

            $filter = \Sherlock\Sherlock::filterBuilder()->AndFilter()->queries(
                array(
                    \Sherlock\Sherlock::filterBuilder()->Term()->field('is_moder_deleted')->term(0),
                    \Sherlock\Sherlock::filterBuilder()->Term()->field('is_deleted')->term(0),
                    \Sherlock\Sherlock::filterBuilder()->Term()->field('deleted_trunc')->term(0),
                    \Sherlock\Sherlock::filterBuilder()->Term()->field('is_activated')->term(1),
                    \Sherlock\Sherlock::filterBuilder()->OrFilter()->queries($rights)
                )
            );

            $highlight = \Sherlock\Sherlock::highlightBuilder()
                ->Highlight()
                //->pre_tags(array("<strong class=\"highlight\">"))
                //->post_tags(array("</strong>"))
                ->pre_tags(array("[highlight]"))
                ->post_tags(array("[/highlight]"))
                ->fields(array(
                    "title" => array("fragment_size" => 400, "number_of_fragments" => 1),
                    "description" => array("fragment_size" => 400, "number_of_fragments" => 1)
                ));

            $from = ($page - 1) * 10;
            $request->index('blog_oldbk')
                ->type("post")
                ->from($from)
                ->size(10)
                ->filter($filter)
                ->highlight($highlight)
                ->query($termQuery);
            $response = $request->execute();*/
            $from = ($page - 1) * \Yii::app()->params['page_size']['post'];

            $result = Yii::app()->elasticsearch->search('blog_oldbk', 'post', $query, $from);
            $array = $result['hits']['hits'];
            foreach($array as $item) {
                $Post = new Post();
                $Post->scenario = 'search';
                $date = new DateTime($item['_source']['create_datetime']);
                $item['_source']['create_datetime'] = $date->format('d.m.Y H:i:s');
                $Post->attributes = $item['_source'];
                if(isset($item['highlight'])) {
                    foreach($item['highlight'] as $name => $values)
                        $Post->{$name} = $values[0];
                }
                $results[] = $Post;
            }

            $pages->setItemCount($result['hits']['total']);
            $pages->pageSize = \Yii::app()->params['page_size']['post'];
        }

        if(Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('search/material', array(
                'models' => $results,
                'pages' => $pages,
            ), false, true);
        } else {
            $this->render('search', array(
                'model' => $model,
            ));
        }
    }

    public function actionSearchblog()
    {
        $query = Yii::app()->request->getParam('query');
        if($query) {
            $criteria = new CDbCriteria();
            $criteria->addSearchCondition('login', $query);

            $pages = new CPagination(User::model()->count($criteria));
            $pages->pageSize = \Yii::app()->params['page_size']['post'];
            $pages->applyLimit($criteria);

            $models = User::model()->findAll($criteria);

            $this->renderPartial('search/user', array(
                'models' => $models,
                'pages' => $pages,
            ), false, true);
        }
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->createUrl('/post/index/index'));
    }


    public function actionUsers()
    {
        $returned = array();
        $criteria = new CDbCriteria();
        $criteria->addSearchCondition('login', Yii::app()->request->getParam('search'));

        /** @var User[] $models */
        $models = User::model()->findAll($criteria);
        foreach($models as $model)
            $returned[] = array(
                'login' => $model->login,
                'game_id' => $model->game_id
            );

        echo CJSON::encode($returned);
    }

    public function actionTest()
    {
        var_dump('33');
        Yii::app()->radio->sendLink('Байт', '2323');
    }

    private function checkUser12($userId, $radio)
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('game_id = :game_id');
        $criteria->params = array(':game_id' => $userId);
        /** @var \User $User */
        $User = \User::model()->find($criteria);
        if(!$User && \ApiUser::checkUser(null, null, $userId) !== false)
            $User = \User::model()->find($criteria);
        if(!$User) {
            Yii::app()->radio->streamOff($radio);
            \MyException::logTxt('Не удалось добавить dj '.$userId, 'dj');
        } else {
            $Dj = UserDj::model()->find('user_id = :user_id', array(':user_id' => $User->id));
            if(!$Dj) {
                Yii::app()->radio->streamOff($radio);
                \MyException::logTxt('Не удалось добавить dj '.$userId, 'dj');
            } else {
                /** @var Radio $model */
                $model = Radio::model()->find('user_id = :user_id and is_online = 1', array(':user_id' => $User->id));
                if(!$model) {
                    $Radio = new \Radio();
                    $Radio->user_id = $User->id;
                    $Radio->radio_type = $radio;
                    $Radio->is_online = 1;
                    $Radio->start_datetime = date('Y-m-d H:i:s', time());
                    $Radio->next_update_datetime = \Radio::getNextUpdate();
                    $Radio->alias = md5(time().$Radio->next_update_datetime);
                    return $Radio->save();
                }
            }
        }

        return false;
    }
}
