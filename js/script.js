/**
 * Created with JetBrains PhpStorm.
 * User: Николай
 * Date: 18.05.13
 * Time: 18:38
 * To change this template use File | Settings | File Templates.
 */
function updateGrid(id) {
    $.fn.yiiGridView.update(id);
}

$.fn.exists = function(){return this.length>0;}
Array.prototype.in_array = function(p_val) {
    for(var key in this)  {
        if(key == p_val)
            return true;
    }
    return false;
}

function is_array( mixed_var ) {	// Finds whether a variable is an array
    //
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Legaev Andrey
    // +   bugfixed by: Cord

    return ( mixed_var instanceof Array );
}

$(function(){
    $(document.body).on('click', 'a.preview_img', function(event){
        var $self = $(this);
        $.fancybox({
            openEffect	: 'none',
            closeEffect	: 'none',
            type        : 'image',
            href        : $self.attr('href'),
            width       : 'auto',
            height      : 'auto'
        });
        event.preventDefault();
    });

    $(document.body).on('click', '.ratingCount.ajax a', function(event){
        var $self = $(this);
        $.ajax({
            url:$self.attr('href'),
            dataType:'json',
            beforeSend: function(){
                return validate.beforeValidate();
            },
            success:function(response){
                response['close'] = false;
                validate.afterValidate2(response);
                if(response.selector !== undefined && response.rateVal !== undefined) {
                    $(response.selector).html(response.rateVal);
                    var $parent = $(response.selector).parent();
                    $self.remove();
                    $parent.prepend('<span class="icon" id="like_tuskl"></span>');
                }
            }
        });
        event.preventDefault();
    });

    $(document.body).on('click', '.addUserToFriend', function(){
        var $self = $(this);
        if(confirm('Вы уверены, что хотите отправить запрос в друзья ' + $(this).attr('login') + '?')) {
            $.ajax({
                url:$self.attr('link'),
                dataType:'json',
                beforeSend: function(){
                    return validate.beforeValidate();
                },
                success:function(response){
                    validate.afterValidate2(response);
                    if(response.ok !== undefined)
                        $self.remove();
                }
            });
        }
    });

    $(document.body).on('click', '.ajaxRequestFriend', function(event){
        event.preventDefault();
        var $self = $(this);
        if(confirm('Вы уверены, что хотите сделать это?')) {
            $.ajax({
                url:$self.attr('href'),
                dataType:'json',
                beforeSend: function(){
                    return validate.beforeValidate();
                },
                success:function(response){
                    validate.afterValidate2(response);
                    if(response.ok !== undefined)
                        $self.closest('.userBlockFriend').remove();
                }
            });
        }
    });

    $(document.body).on('click', '#addToFriend', function(event){
        event.preventDefault();
        var $self = $(this);
        if(confirm('Вы уверены, что хотите отправить запрос в друзья?')) {
            $.ajax({
                url:$self.attr('href'),
                dataType:'json',
                beforeSend: function(){
                    return validate.beforeValidate();
                },
                success:function(response){
                    validate.afterValidate2(response);
                    if(response.ok !== undefined)
                        $self.closest('.deleteThis').remove();
                }
            });
        }
    });

    $(document.body).on('click', '.subscribe.ajax a', function(event){
        var $self = $(this);
        $.ajax({
            url:$self.attr('href'),
            dataType:'json',
            beforeSend: function(){
                return validate.beforeValidate();
            },
            success:function(response){
                validate.afterValidate2(response);
                if(response.ok !== undefined)
                    $self.html('<i class="icon" title="Вы уже подписаны" id="subscribeDebate"></i>');
            }
        });
        event.preventDefault();
    });

    /*$(document.body).on('click', '.showRate.ajax', function(event){
     var $self = $(this);
     $.fancybox({
     openEffect	: 'none',
     closeEffect	: 'none',
     type        : 'ajax',
     href        : $self.attr('data-link'),
     width       : 'auto',
     height      : 'auto'
     });
     event.preventDefault();
     });*/

    History.Adapter.bind(window, 'statechange', function() {
        var state = History.getState();
        var currentIndex = History.getCurrentIndex();
        var internal = (state.data._index == (currentIndex - 1));
        if (!internal && state.data['callback'] !== undefined) {
            eval(state.data['callback']);
        }
    });
});

function previewFocus (id){
    $('.album-slider').scrollTo($('#focused_'+id));
    //$('#focused_'+id).scrollTo('focus');
}

function elementFocus (selector, scrollto){
    $(selector).scrollTo($(scrollto));
    //$('#focused_'+id).scrollTo('focus');
}
function drawChart(values, title, id) {
    var data = google.visualization.arrayToDataTable(values);
    var options = {
        title:title,
        is3D: true,
        'backgroundColor': 'transparent',
        sliceVisibilityThreshold:0
    };
    var chart = new google.visualization.PieChart(document.getElementById(id));
    chart.draw(data, options);
}

function showLoader() {
    $.fancybox.showLoading();
}

function hideLoader() {
    $.fancybox.hideLoading();
}