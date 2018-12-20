/**
 * Created with JetBrains PhpStorm.
 * User: nnick
 * Date: 17.07.13
 * Time: 16:16
 * To change this template use File | Settings | File Templates.
 */
$(function(){
    $(document.body).on('click', '.moder_delete', function(event){
        event.preventDefault();
        var $self = $(this);

        $.fancybox({
            type:'ajax',
            openEffect:'none',
            closeEffect:'none',
            href:$self.attr('href')
        });
    });
});