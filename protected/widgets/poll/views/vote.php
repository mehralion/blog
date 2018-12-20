<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 21.11.13
 * Time: 21:05
 *
 * @var Poll $model
 * @var PollAnswer[] $results
 */ ?>
    <div id="poll" class="border">
        <div id="pollTitle"><?php echo $model->question; ?></div>
        <ul>
            <?php foreach($results as $answer): ?>
                <li>
                    <?php echo CHtml::radioButton('Vote', false, array('id' => 'Vote_'.$answer->id, 'value' => $answer->id)); ?>
                    <label for="<?php echo 'Vote_'.$answer->id; ?>"><?php echo $answer->title; ?></label>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="clear"></div>
        <div class="buttons" style="text-align: center;">
            <div class="m_button">
                <?php echo CHtml::link('Проголосовать', Yii::app()->createUrl('/poll/request/add', array('id' => $model->id)), array('class' => 'btn2', 'id' => 'addVote')) ?>
            </div>
        </div>
    </div>
<script>
    $(function(){
        $(document.body).on('click', '#addVote', function(event){
            event.preventDefault();
            var $self = $(this);
            var vote = $('input[name=Vote]:checked').val();
            if(vote === undefined) {
                alert('Вы не выбрали вариант ответа!');
                return;
            }
            if(confirm('Вы уверены, что хотите проголосовать за этот вариант ответа?')) {
                $.ajax({
                    url:$self.attr('href'),
                    dataType:'json',
                    data:{'vote_id':vote},
                    beforeSend:function(){
                        return validate.beforeValidate();
                    },
                    success:function(response){
                        validate.afterValidate2(response, null, null);
                        if(response.ok !== undefined) {
                            $('#poll').html('').removeClass('border').css({'width':'660px','height':'400px'});
                            var answer = [];
                            $.each(response.values, function(i, item){
                                var voteValue;
                                if(!isNaN(parseInt(item.value)))
                                    voteValue = parseInt(item.value);
                                else
                                    voteValue = item.value;
                                answer.push([item.title, voteValue]);
                            });
                            drawChart(answer, response.title, 'poll');
                        }
                    }
                });
            }
        });
    });
</script>