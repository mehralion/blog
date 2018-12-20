/**
 * Created by Николай on 03.01.14.
 */
$(function(){
    $(document.body).on('click', '.community .addModer', function(event){
        event.preventDefault();

        var $li = $('<li>', {'class':'item','style':'margin-top:10px;'});
        $('<input>', {'type':'text', 'placeholder':'Введите ник', 'class':'nickname moder', 'data-id':'0'}).css({'margin-bottom':'3px','margin-right':'10px'}).appendTo($li);
        $('<div>', {'class':'m_button'}).append($('<a>', {'class':'btn1 delModer', 'text':'-'}).css({'width':'9px','text-align':'center'})).appendTo($li);
        $('#moderLsit').append($li);
    });

    $(document.body).on('click', '.community .addInvite', function(event){
        event.preventDefault();

        var $li = $('<li>', {'class':'item','style':'margin-top:10px;'});
        $('<input>', {'type':'text', 'placeholder':'Введите ник', 'class':'nickname invite', 'data-id':'0'}).css({'margin-bottom':'3px','margin-right':'10px'}).appendTo($li);
        $('<div>', {'class':'m_button'}).append($('<a>', {'class':'btn1 delInvite', 'text':'-'}).css({'width':'9px','text-align':'center'})).appendTo($li);
        $('#inviteList').append($li);
    });

    $(document.body).on('click', '.community .delModer, .community .delInvite', function(event){
        event.preventDefault();

        $(this).closest('li').remove();
    });

    $(document.body).on('focus', '.community .nickname:not(.ui-autocomplete-input)', function (e) {
        $(this).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url: getUserListLink,
                    dataType: "json",
                    data: {
                        search: request.term
                    },
                    success: function( data ) {
                        response( $.map( data, function( user ) {
                            return {
                                label   : user.login,
                                value   : user.login,
                                gameId  : user.game_id
                            }
                        }));
                    }
                });
            },
            minLength: 3,
            select: function( event, ui ) {
                $(this).attr('data-id', ui.item.gameId);
            },
            open: function() {
                $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
            },
            close: function() {
                $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
            }
        });
    });

    $(document.body).on('click', '.community a.back', function(event){
        event.preventDefault();

        var $self = $(this);

        $.ajax({
            url         : $self.attr('href'),
            dataType    : 'json',
            beforeSend  : function() {
                $.fancybox.showLoading();
                return true;
            },
            success     : function(response){
                if(response.content !== undefined) {
                    $('.form.community').replaceWith(response.content);
                    closeSmiles();
                    $.fancybox.hideLoading();

                    History.pushState(
                        {"_index" : History.getCurrentIndex()},
                        'Блоги ОлдБК Сообщество',
                        $self.attr('href')
                );
                } else
                    validate.afterValidate2(response);
            }
        });
    });

    $(document.body).on('click', '.community.users a.delete', function(event){
        event.preventDefault();

        var $self = $(this);
        if(confirm('Вы уверены, что хотите удалить участника сообщества?')) {
            $.ajax({
                url         : $self.attr('href'),
                dataType    : 'json',
                beforeSend  : function() {
                    $.fancybox.showLoading();
                    return true;
                },
                success     : function(response){
                    validate.afterValidate2(response);
                    if(response.ok !== undefined)
                        getContent($('#page').val());
                }
            });
        }
    });

    $(document.body).on('click', '.community #save', function(event){
        event.preventDefault();

        var data = {};
        var $self = $(this);

        $.each($('.community .nickname.moder'), function(i, el){
            if($(el).val() != '' && $(el).attr('data-id') != '0')
                data['Moder['+i+']'] = {
                    'login' : $(el).val(),
                    'id'    : $(el).attr('data-id')
                };
        });
        $.each($('.community .nickname.invite'), function(i, el){
            if($(el).val() != '' && $(el).attr('data-id') != '0')
                data['Invite['+i+']'] = {
                    'login' : $(el).val(),
                    'id'    : $(el).attr('data-id')
                };
        });

        $.ajax({
            url         : $self.attr('href'),
            dataType    : 'json',
            data        : data,
            beforeSend  : function() {
                $.fancybox.showLoading();
                return true;
            },
            success     : function(response){
                validate.afterValidate2(response);
            }
        });
    });

    $(document.body).on('click', '.community.users .pagination a', function(event){
        event.preventDefault();

        getContent($(this).attr('data-page'));
    });

    $(document.body).on('click', '.communityMenu #connect_community', function(event){
        event.preventDefault();

        var $self = $(this);

        $.ajax({
            url         : $self.attr('href'),
            dataType    : 'json',
            beforeSend  : function(){
                showLoader();
            },
            success     : function(response){
                if(response.ok !== undefined)
                    validate.afterValidate2(response);
                else if(response.content !== undefined) {
                    $.fancybox({
                        type    : 'html',
                        content : response.content
                    });
                }

                hideLoader();
            }
        });
    });
});

function getContent(currPage)
{
    $.ajax({
        data        : {
            'page'  : currPage
        },
        dataType    : 'json',
        beforeSend  : function(){
            $.fancybox.showLoading();
            return true;
        },
        success     : function(response){
            if(response.content !== undefined)
                $('#userList').html(response.content);

            $.fancybox.hideLoading();
        }
    });
}