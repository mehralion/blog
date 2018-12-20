<?php
namespace application\modules\post\actions\profile;
use application\modules\post\components\PostAction;

/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.post.actions.profile
 */
class Add extends PostAction
{
    public $addLinkRoute = null;
    public $addLinkParams = array();

    public $viewName = 'form';

    public function run()
    {
        \Yii::app()->clientScript->registerScriptFile(\Yii::app()->baseUrl.'/js/poolPost.js');
        $Post = new \Post('create');

        if($this->isCommunity) {
            if(!\Yii::app()->community->inCommunity()) {
                \Yii::app()->message->setErrors('danger', 'Вы не состоите в этом сообществе');
                \Yii::app()->message->showMessage();
            }

            $Post->community_id = $this->communityId;
            $Post->is_community = 1;
            $Post->community_alias = \Yii::app()->community->alias;

            $this->successLinkRoute = '/community/post/show';
            $this->successLinkParams = array('community_alias' => \Yii::app()->community->alias);

            $this->addLinkRoute = '/community/post/add';
            $this->addLinkParams = array('community_alias' => \Yii::app()->community->alias);
        } else {
            $this->successLinkRoute = '/post/index/show';
            $this->successLinkParams = array('gameId' => \Yii::app()->user->getGameId());

            $this->addLinkRoute = '/post/profile/add';
            $this->addLinkParams = array('gameId' => \Yii::app()->user->getGameId());
        }

        $post = \Yii::app()->request->getParam('Post');
        $tags = \Yii::app()->request->getParam('Tags', array());
        if(!empty($post)) {
            $t = \Yii::app()->db->beginTransaction();
            $error = false;
            try {
                $Post->attributes = $post;
                $Post->user_id = \Yii::app()->user->id;
                if(!$Post->tags->setTags($tags)->create()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Post);
                }

                if(!$error && $Post->is_poll) {
                    $poolPost = \Yii::app()->request->getParam('Poll');
                    if($poolPost) {
                        $Pool = new \Poll();
                        $Pool->attributes = $poolPost;
                        if(isset($poolPost['date_end']) && $poolPost['date_end'] != '') {
                            if(time() > strtotime($poolPost['date_end'])) {
                                $Pool->addError('date_end', 'Не может быть меньше текущей');
                                \Yii::app()->message->setErrors('danger', $Pool);
                                \Yii::app()->message->showMessage();
                            } else
                                $Pool->date_end = \DateTimeFormat::format(null, strtotime($poolPost['date_end'].' 23:59:59'));
                        }

                        if(isset($poolPost['answer']))
                            $Pool->answer = $poolPost['answer'];
                        $Pool->post_id = $Post->id;
                        $Pool->user_owner_id = \Yii::app()->user->id;
                        $Pool->create_datetime = \DateTimeFormat::format();
                        if(!$Pool->save()) {
                            $error = true;
                            \Yii::app()->message->setErrors('danger', $Pool);
                        }

                        if(!$error) {
                            if(is_array($Pool->answer) && count($Pool->answer) > 0) {
                                $i = 0;
                                foreach($Pool->answer as $key => $answer) {
                                    if($i > 5)
                                        break;
                                    $model = new \PollAnswer();
                                    $model->poll_id = $Pool->id;
                                    $model->title = $answer;
                                    if(!$model->save()) {
                                        if(!$error)
                                            $Pool->addError('answer', 'Вариант ответа не может быть пустым');
                                        $error = true;
                                        \Yii::app()->message->setErrors('danger', $Pool, $key);
                                    }
                                    $i++;
                                }
                            } else {
                                $error = true;
                                $Pool->addError('answer', 'Должен быть как минимум один вариант ответа!');
                                \Yii::app()->message->setErrors('danger', $Pool);
                            }
                        }
                    } else {
                        $Post->addError('is_poll', 'Проверьте поля опроса');
                        \Yii::app()->message->setErrors('danger', $Post);
                        $error = true;
                    }
                }

                if(!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Заметка была добавлена!');
                    \Yii::app()->message->url = \Yii::app()->createUrl($this->successLinkRoute, \CMap::mergeArray($this->successLinkParams, array('id' => $Post->id)));
                } else
                    $t->rollback();

            } catch (\Exception $ex) {
                $t->rollback();
                \MyException::log($ex);
            }

            \Yii::app()->message->showMessage();
        } else
            $this->controller->renderPartial($this->viewName, array(
                'model' => $Post,
                'url' => \Yii::app()->createUrl($this->addLinkRoute, $this->addLinkParams),
                'poll' => new \Poll()
            ), false, true);
    }
}