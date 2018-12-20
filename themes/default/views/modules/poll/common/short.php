<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 30.01.14
 * Time: 18:26
 *
 * @var Poll $model
 */ ?>
<article class="short_block">
    <h3 class="title">
        <?php $title = Yii::app()->stringHelper->subStringNew($model->post->title, 60, '...'); ?>
        <?php echo CHtml::link($title, Yii::app()->createUrl('/post/index/show', array('id' => $model->post_id, 'gameId' => $model->post->user->game_id))); ?>
    </h3>
    <div class="content">
        <?php
        $options = "['Task', '{$model->question}'],";
        foreach($model->pollAnswers as $answer)
            $options .= "['{$answer->title}', {$answer->value}],";
        ?>
        <div id="piechart_<?php echo $model->post_id; ?>" style="width: 250px; height: 100px;"></div>
        <script>
            $(function(){
                drawChart([<?php echo trim($options, ","); ?>], '<?php echo $model->question; ?>', 'piechart_<?php echo $model->post_id; ?>');
            });
        </script>
    </div>
    <div class="info">
        <span class="icon" id="like"></span><span class="ratingCount">Проголосовало: <?php echo $model->pollUserAnswer; ?></span>
    </div>
</article>