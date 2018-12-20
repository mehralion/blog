<?php
/**
 * Class GenerateController
 *
 * @package application.controllers
 */
set_time_limit(0);
class GenerateController extends Controller
{
    private $rights = array(
        0 => 0,
        1 => 1,
        2 => 5,
    );
    public function actionPost()
    {
        $User = User::model()->findAll();
        for($i = 0; $i < 200; $i++) {
            $user = rand(0, count($User)-1);
            Yii::app()->user->id = $User[$user]->id;

            $Post = new Post();
            $Post->onSave = array(new \EventItemPost(), 'create');
            $Post->onAfter = array(new \CacheEventItemPost(), 'updateRecord');
            $Post->title = 'Заголовок '.($i+1);
            $Post->description = $this->getIpsumText();
            $Post->user_id = Yii::app()->user->id;
            $Post->is_activated = 1;
            $Post->post_type = 1;
            $Post->is_like = rand(0, 1);
            $Post->is_comment = rand(0, 1);
            $Post->view_role = $this->rights[rand(0, 2)];
            $Post->save();
        }
    }

    public function actionFixcomment()
    {
        /** @var CommentItem[] $models */
        $models = CommentItem::model()->findAll();
        foreach($models as $model) {
            $model->description = Yii::app()->stringHelper->parseEditor($model->description);
            $model->save();
        }
    }

    public function actionFixpost()
    {
        /** @var Post[] $models */
        $models = Post::model()->findAll();
        foreach($models as $model) {
            $model->description = Yii::app()->stringHelper->parseEditor($model->description);
            $model->save();
        }
    }

    public function actionPostcomment()
    {
        /** @var User[] $User */
        $User = User::model()->findAll();
        /** @var Post[] $Posts */
        $Posts = Post::model()->findAll();
        foreach($Posts as $model) {
            $commentCount = rand(0, 20);
            for($i = 0; $i < $commentCount; $i++) {
                $user = rand(0, count($User)-1);
                if(!$this->addComment($model->id, $User[$user]->id)) {
                    die('Error');
                }
            }
        }
    }

    private function addComment($id, $user_id)
    {
        Yii::app()->user->id = $user_id;
        $criteria = new \CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->scopes = array(
            'canComment',
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus'
        );
        $criteria->params = array(
            ':id' => $id,
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0
        );
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess());

        /** @var \Post $Post */
        $Post = \Post::model()->find($criteria);
        if($Post) {
            $Post->comment_count += 1;
            $Post->onSave = array(new \EventCommentInfoPost(), 'updateRecord');
            $Post->onAfter = array(new \CacheEventItemPost(), 'updateRecord');

            $Comment = new \CommentItemPost();
            $Comment->item_id = $Post->id;
            $Comment->description = $this->getIpsumText();
            $Comment->user_owner_id = $Post->user_id;
            $Comment->user_id = $user_id;
            $Comment->onCreate = array(new \EventCommentPost(), 'create');
            $Comment->onAfter = array($Post, 'updateRecord');
            if($Comment->create())
                return true;
            else
                return false;
        } else
            return true;
    }

    private function getIpsumText()
    {
        $curl = Yii::app()->curl;
        return $curl->run('http://loripsum.net/api/10/short');
    }
}
