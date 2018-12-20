/**
 * Created with JetBrains PhpStorm.
 * User: Николай
 * Date: 04.10.13
 * Time: 18:22
 * To change this template use File | Settings | File Templates.
 */
$(function(){
    $(document.body).on('click', '#add_video', function(event){
        var $self = $(this);
        $.fancybox({
            type        : 'ajax',
            openEffect  : 'none',
            closeEffect : 'none',
            width       : '600px',
            height      : 'auto',
            autoSize    : false,
            href        : $self.attr("href"),
            afterClose  : function(){
                if($('.sp-container').exists())
                    $('.sp-container').remove();
            },
            afterShow   : function(){
                $('#GalleryVideo_video_type').change(function(){
                    var $this = $(this);
                    var videoType = $this.val();
                    if(videoType == 2) {
                        $this.parent().append('<div style="margin-left: 10px;" id="help" class="m_button"><a href="/themes/default/images/help/vk.png" target="_blank" class="btn1">Помощь</a></div>');
                    } else
                        $this.parent().find('#help').remove();
                });
            }
        });
        event.preventDefault();
    });
    $(document.body).on('click', '.edit', function(event){
        var $self = $(this);
        $.fancybox({
            type        : 'ajax',
            openEffect  : 'none',
            closeEffect : 'none',
            width       : '600px',
            height      : 'auto',
            autoSize    : false,
            href        : $self.attr("href"),
            afterClose  : function(){
                if($('.sp-container').exists())
                    $('.sp-container').remove();
            }
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
    $(document.body).on('click', '.del', function(event){
        if(confirm("Вы уверены, что хотите отправить этот видеоальбом в корзину? \n Восстановить видеоальбом можно из корзины в любой момент."))
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

    $(document.body).on('click', '.preview_video.event.fancybox-media', function(event){
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