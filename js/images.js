/**
 * Created with JetBrains PhpStorm.
 * User: Николай
 * Date: 05.06.13
 * Time: 15:20
 * To change this template use File | Settings | File Templates.
 */
$(function(){
    $(document.body).on('click', '#add_album', function(event){
        var $self = $(this);
        $.fancybox({
            openEffect  : 'none',
            closeEffect : 'none',
            type        : 'ajax',
            href        : $self.attr('href')
        });
        event.preventDefault();
    });

    $(document.body).on('click', '#add_image', function(){
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
    $(document.body).on('click', '.del', function(event){
        if(confirm("Вы уверены, что хотите отправить этот фотоальбом в корзину? \n Восстановить фотоальбом можно из корзины в любой момент."))
            return true;
        else
            return false;
    });
    $(document.body).on('click', '#edit_album_btn', function(event){
        event.preventDefault();
        var $self = $(this);
        $.fancybox({
            openEffect  :'none',
            closeEffect :'none',
            type        :'ajax',
            href        : $self.attr("href")
        });
    });

    $(document.body).on('click', '.preview_image.event.fancybox-media', function(event){
        var $self = $(this);
        $.fancybox({
            type        : 'ajax',
            openEffect  : 'none',
            closeEffect : 'none',
            minWidth    : '800px',
            href        : $self.attr("href"),
            afterShow   : function() {
                setTimeout(function(){
                    $.fancybox.update();
                }, 1000);
            }
        });
        event.preventDefault();
    });
});
function addItems(data)
{
    var count = $(document.body).find('ul.image li').length;
    if(count < 10) {
        var $ul = $(document.body).find('ul.image');
        $ul.append('<li style="margin-left: 4px;">'
            +'<figure class="img_border">'
            +'<a href="'+data.link+'"><img src="'+data.url+'" alt=""></a></figure><div class="buttons">'
            +'<a id="update_'+count+'" class="edit fancybox.ajax" title="Редактировать" href="'+data.updateLink+'"><i class="icon" id="edit" title="Редактировать"></i></a>'
            +'<span style="width:10px;display: inline-block"></span>'
            +'<a title="Удалить" href="'+data.deleteLink+'" id="yt0"><i class="icon" id="del" title="Удалить"></i></a></div>'
            +'</li>');
    }
}