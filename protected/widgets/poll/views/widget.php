<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 21.11.13
 * Time: 21:45
 *
 * @var Poll $model
 * @var PollAnswer[] $results
 */
?>
<?php
$options = "['Task', '{$model->question}'],";
foreach ($results as $answer)
    $options .= "['{$answer->title}', {$answer->value}],";
?>
<h2 class="title">
    Случайный опрос
</h2>
<div class="" style="text-align: center;">
    <?php
        $title = Yii::app()->stringHelper->subStringNew($model->question, 100);
        echo CHtml::link($title, Yii::app()->createUrl('/post/index/show', array('id' => $model->post_id)));
    ?>
</div>
<div class="block" id="piechartWidget" style="width: 230px; height: 163px;">
</div>
<script>
    $(function () {
        drawChart([<?php echo trim($options, ","); ?>], '', 'piechartWidget');
    });
</script>