<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 19.11.13
 * Time: 21:20
 */  ?>
<fieldset>
    <legend>Удалить блог пользователя</legend>
    <div id="userClearBlock">
        <?php $this->widget('CAutoComplete',
            array(
                'name'=>'user_clear',
                'url'=>array('/admin/user/list'),
                'minChars'=>2,
                'methodChain'=>".result(function(event,item){\$(\"#user_clear_id\").val(item[1]);})",
            )
        ); ?>
        <?php echo CHtml::hiddenField('user_clear_id', '0'); ?>
        <div class="m_button" style="vertical-align: top;padding-top: 3px;">
            <?php echo CHtml::submitButton('Удалить', array('class' => 'btn1', 'id' => 'submit_clear')); ?>
        </div>
    </div>
</fieldset>

<script>
    $(function(){
        $(document.body).on('click', '#submit_clear', function(event){
            event.preventDefault();
            var value = $('#user_clear_id').val();
            if(value == '0') {
                alert('Вы не выбрали блогера!');
            } else {
                if(confirm('Вы точно хотите очистить блог? Процесс может занять несколько минут!')) {
                    $.ajax({
                        url:'<?php echo Yii::app()->createUrl('/admin/user/clear'); ?>',
                        data:{'user_id':$('#user_clear_id').val()},
                        dataType:'json',
                        beforeSend:function(){
                            return validate.beforeValidate();
                        },
                        success:function(response){
                            validate.afterValidate2(response);
                        }
                    });
                }
            }
        });
    });
</script>