/**
 * Created with JetBrains PhpStorm.
 * User: Николай
 * Date: 05.06.13
 * Time: 15:20
 * To change this template use File | Settings | File Templates.
 */
var fileCount = 1;
$(function(){
    $(document.body).on('click', '.add_row', function(event){
        event.preventDefault();
        addImageBlock();
        fileCount++;
    });
    $(document.body).on('click', '.remove_row', function(event){
        event.preventDefault();
        $(this).parent().parent().remove();
    });

    $(document.body).on('click', '#add_audio', function(){
        $('.hidden.block').toggle();
    });
    $(document.body).on('click', '.edit', function(event){
        var $self = $(this);
        $.fancybox({
            type        :'ajax',
            href        : $self.attr("href"),
            openEffect  :'none',
            closeEffect :'none',
            autoWidth   : true,
            autoHeight  : true,
            afterClose  : function(){
                if($('.sp-container').exists())
                    $('.sp-container').remove();
            },
            afterShow   : function() {
                setTimeout(function(){
                    $.fancybox.update();
                }, 1500);
            }
        });
        event.preventDefault();
    });
    $(document.body).on('click', '.btn1.del', function(event){
        if(confirm("Вы уверены, что хотите отправить этот аудиоальбом в корзину? \n Восстановить аудиоальбом можно из корзины в любой момент."))
            return true;
        else
            return false;
    });
    $(document.body).on('click', '#edit_album_btn', function(event){
        var $self = $(this);
        $.fancybox({
            type        :'ajax',
            openEffect  :'none',
            closeEffect :'none',
            href        : $self.attr("href")
        });
        event.preventDefault();
    });
    $(document.body).on('click', '#add_album', function(event){
        var $self = $(this);
        $.fancybox({
            type        : 'ajax',
            openEffect  : 'none',
            closeEffect : 'none',
            href        : $self.attr('href')
        });
        event.preventDefault();
    });
});
function addImageBlock()
{
    var div = $('.files_block');
    var ul = $('<ul>').appendTo(div);
    var liLink = $('<li>').appendTo(ul);
    $('<input>', {'name':'Files['+fileCount+'][link]', 'type':'text','maxlength':255,'placeholder':'Ссылка на файл (только mp3)'}).appendTo(liLink);
    var liTitle = $('<li>').appendTo(ul);
    $('<input>', {'name':'Files['+fileCount+'][title]', 'type':'text', 'style':'margin-left:4px;','maxlength':255, 'placeholder':'Название'}).appendTo(liTitle);
    var liAdd = $('<li>', {'class':'m_button'}).appendTo(ul);
    $('<button>', {'style':'margin-left:5px;','class':'add_row btn1', 'text':'+'}).appendTo(liAdd);
    $('<button>', {'style':'margin-left:5px;','class':'remove_row btn1', 'text':'-'}).appendTo(liAdd);
}