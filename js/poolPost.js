/**
 * Created by Николай on 21.11.13.
 */
$(function(){
    if(!$('#Post_is_poll').is(':checked'))
        $('.poll.hidden').hide();
    else
        $('.poll.hidden').show();

    $(document.body).on('click', '.poll .add', function(event){
        event.preventDefault();
        if($('#pollAnswer').find('.blockPoll').length < 5)
            addField();
        else
            alert('Максимальное число вариантов 6');
    });

    $(document.body).off('click', '#Post_is_poll');
    $(document.body).on('click', '#Post_is_poll', function(event){
        if(!$(this).is(':checked'))
            $('.poll.hidden').hide();
        else
            $('.poll.hidden').show();
    });

    $(document.body).off('click', '.poll .take');
    $(document.body).on('click', '.poll .take', function(event){
        event.preventDefault();
        $(this).closest('.blockPoll').remove();

        $('.poll .add').remove();

        var add = $('<a>', {'class':'btn1 add','text':'+', 'style':'margin-left:4px;margin-right:4px;'});
        if($('#pollAnswer .blockPoll').length > 0)
            $('#pollAnswer .blockPoll:last').find('.m_button').prepend(add);
        else
            $('.poll .blockPoll').find('.m_button').append(add);

        setLabel();
    });
});

function addField() {
    var count = $('#pollAnswer .blockPoll').length + 1;
    var $block = $('#pollAnswer');
    var divRow = $('<div>', {'class':'control-group blockPoll'}).appendTo($block);
    $('<label>', {'class':'short control-label'}).appendTo(divRow);

    var inputRow = $('<div>', {'class':'controls', 'style':'display: inline-block;margin-left: 15px;'}).appendTo(divRow);
    $('<input>', {'type':'text','name':'Poll[answer]['+count+']', 'id':'Poll_'+count+'_answer'}).appendTo(inputRow);
    $('<span>', {'id':'Poll_'+count+'_answer_em_', 'style':'display:none;', 'class':'help-block error'}).appendTo(inputRow);

    $('.poll .add').remove();

    var buttonRow = $('<div>', {'class':'m_button', 'style':'vertical-align:top;'}).appendTo(divRow);
    $('<a>', {'class':'btn1 add','text':'+', 'style':'margin-left:4px;'}).appendTo(buttonRow);
    $('<a>', {'class':'btn1 take','text':'-', 'style':'margin-left:4px;'}).appendTo(buttonRow);

    setLabel();
}

function setLabel() {
    $.each($('#pollAnswer').find('.blockPoll label'), function(i, el){
        $(el).html('Вариант '+(i+2));
    });
}