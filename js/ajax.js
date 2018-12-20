/**
 * Created with JetBrains PhpStorm.
 * User: nnick
 * Date: 17.07.13
 * Time: 17:07
 * To change this template use File | Settings | File Templates.
 */
var message, validate;
$(function(){
    message = new Message();
    validate = new Validate();

    $( document ).ajaxError(function(error, response) {
        $.fancybox.hideLoading();
        message.error(response.responseText);
    });
});

function Message()
{
    /**
     *
     * @param text_message
     * @param header
     */
    this.error = function(text_message)
    {
        $.jGrowl(text_message, {
            life:2000,
            header: 'Ошибка!',
            theme: 'error'
        });
    };

    /**
     *
     * @param text_message
     * @param url
     */
    this.noError = function(text_message, url)
    {
        if(text_message != '') {
            $.jGrowl(text_message, {
                theme: 'Left',
                life:2000,
                close: function(e,m,o) {
                    if(url !== null && url !== undefined)
                        location.href = url;
                },
                speed:0
            });    
        } else if(url !== null && url !== undefined)
            location.href = url;
    }
}

function Validate()
{
    /**
     *
     * @type {*}
     * @private
     */
    var _that = this;

    /**
     *
     * @param formId
     * @returns {boolean}
     */
    this.beforeValidate = function(formId)
    {
        $.fancybox.showLoading();
        if(formId !== undefined && formId !== null) {
            $('#'+formId).find('input, select, textarea').attr('readonly', true).removeClass('error');
            $('#'+formId).find('span.error').hide();
        }
        return true;
    };

    /**
     *
     * @param data
     * @param hasError
     * @param formId
     */
    this.afterValidate = function(data, hasError, formId)
    {
        $.fancybox.hideLoading();
        var string = '';
        $('#'+formId).find('input, select, textarea').attr('readonly', false).removeClass('error');

        if(data.error !== undefined || hasError) {
            var errors = null;
            if(data.error !== undefined)
                errors = data.error;
            else
                errors = data;
            $.each(errors, function(field, error) {
                if($('#' + formId + ' #' + field).exists())
                    $('#' + formId + ' #' + field).addClass('error');
                string += error + '<br>';
            });
            message.error(string);
            return false;
        } else {
            if(data.close === undefined || data.close === null || data.close !== false)
                $.fancybox.close();
            var url = null;
            if(data.url !== undefined)
                url = data.url;
            if(data.text !== undefined)
                message.noError(data.text, url);
            return true;
        }
    };

    /**
     *
     * @param data
     * @param hasError
     * @param formId
     */
    this.afterValidate2 = function(data, hasError, formId)
    {
        $.fancybox.hideLoading();
        var string = '';
        $('#'+formId).find('input, select, textarea').attr('readonly', false);

        if(data.error !== undefined) {
            $.each(data.error, function(type, errorsList){
                $.each(errorsList, function(textField, errors){
                    var stringError = '';
                    if(!is_array(errors)) {
                        if(stringError != '')
                            stringError += '<br>';
                        stringError += errors;
                    } else {
                        $.each(errors, function(i, error){
                            if(stringError != '')
                                stringError += '<br>';
                            stringError += error;
                        });
                    }
                    string += stringError + '<br>';
                    if($('#' + textField).exists())
                        $('#' + textField).addClass('error_box');
                    if($('#' + textField + '_em_').exists())
                        $('#' + textField + '_em_').addClass('required_field_text').html(stringError).show();
                });
            });
            if(string != '')
                message.error(string);
            $('html').scrollTo($('body'), 1000);
            return false;
        } else {
            if(data.close === undefined || data.close === null || data.close !== false)
                $.fancybox.close();
            var url = null;
            if(data.url !== undefined)
                url = data.url;

            if(data.text !== undefined) {
                $.each(data.text, function(type, textArray){
                    $.each(textArray, function(textField, textList){
                        if(!is_array(textList))
                            string += textList+'<br>';
                        else {
                            $.each(textList, function(i, text){
                                if(string != '')
                                    string += '<br>';
                                string += text;
                            });
                        }
                    });
                });
            }
            message.noError(string, url);
            return true;
        }
    }
}