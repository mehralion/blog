/**
 * Created by Николай on 03.12.13.
 *
 * @var string cropWidth SIZE_THUMBS_SMALL_WIDTH
 * @var string cropHeight SIZE_THUMBS_SMALL_HEIGHT
 * @var string cropUrl
 * @var string YiiToken Yii::app()->request->csrfToken;
 */

function Crop()
{
    var cropWidth, cropHeight, cropUrl, YiiToken;
    var x, y, x2, w, y2, h;
    var _that = this;

    this.setCropParams = function(cropWidthIn, cropHeightIn, cropUrlIn, YiiTokenIn) {
        cropWidth = cropWidthIn;
        cropHeight = cropHeightIn;
        cropUrl = cropUrlIn;
        YiiToken = YiiTokenIn;
    };

    this.showCrop = function(img){
        $('body').find('#TB_overlay').show();
        $('body').find('#image_crop').show().html(
            '<img src="'+img+'" id="jcrop_target">' +
                '<div class="hr"></div>' +
                '<a id="saveCrop" class="btn btn-primary">Обрезать</a>' +
                '<a id="cancelCrop" class="btn" style="margin-left: 10px;">Отмена</a>'
        );
        /*$('body').append('<div id="TB_overlay"></div>' +
            '<div id="image_crop">' +
            '<img src="'+img+'" id="jcrop_target">' +
            '<div class="hr"></div>' +
            '<a id="saveCrop" class="btn btn-primary">Обрезать</a>' +
            '<a id="cancelCrop" class="btn" style="margin-left: 10px;">Отмена</a>' +
            '</div>');*/

        /** events */
        var im = new Image();
        im.src = img;
        im.onload = function(){
            $('#image_crop').css({
                'margin-left':'-'+(im.width/2)+'px',
                'margin-top':'-'+((im.height+50)/2)+'px',
                'width':im.width,
                'height':(im.height+50)
            });
            $('#jcrop_target').Jcrop({
                minSize:[cropWidth, cropHeight],
                bgFade:     true,
                bgOpacity: .2,
                allowResize: true,
                aspectRatio: cropWidth/cropHeight,
                setSelect: [ 0, 0, cropWidth, cropHeight ],
                allowSelect: false,
                allowMove : true,
                onChange: _that.showCoords
            });
        };
        $('#saveCrop').click(function(event){
            _that.sendCrop();
            event.preventDefault();
        });
        $('#TB_overlay, #cancelCrop').click(function(event){
            $('body').find('#image_crop, #TB_overlay').hide();
            event.preventDefault();
        });
        /** end events */
    };

    this.showCoords = function(c) {
        x = c.x;
        y = c.y;
        x2 = c.x2;
        y2 = c.y2;
        w = c.w;
        h = c.h;
    };

    this.sendCrop = function() {
        $.ajax({
            url:cropUrl,
            data:{
                'YII_CSRF_TOKEN':YiiToken,
                'name':$('#Image_crop').val(),
                'Coords[x]':x,
                'Coords[y]':y,
                'Coords[h]':h,
                'Coords[w]':w
            },
            type:'post',
            dataType:'json',
            beforeSend:function(){
                return validate.beforeValidate();
            },
            success:function(response){
                if(response.debug !== undefined)
                    console.log(response);
                if(response.ok !== undefined) {
                    $('body').find('#image_crop, #TB_overlay').remove();
                    response.close = false;
                }

                if(response.selectorId !== undefined)
                    $(response.selectorId).attr('src', response.selectorValue);
                validate.afterValidate(response);
            }
        });
    }
}