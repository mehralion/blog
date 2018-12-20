<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 21.11.13
 * Time: 21:45
 *
 * @var Poll $model
 * @var PollAnswer[] $results
 */ ?>
<?php
$options = "['Task', '{$model->question}'],";
foreach($results as $answer)
    $options .= "['{$answer->title}', {$answer->value}],";
?>
<div id="piechart" style="width: 660px; height: 400px;"></div>
<script>
    $(function(){
        drawChart([<?php echo trim($options, ","); ?>], '<?php echo $model->question; ?>', 'piechart');
    });
</script>