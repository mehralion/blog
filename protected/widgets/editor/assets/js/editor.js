/**
 * Created with JetBrains PhpStorm.
 * User: Николай
 * Date: 22.06.13
 * Time: 9:06
 * To change this template use File | Settings | File Templates.
 */

function MyEditor()
{
    var _that = this;

    this.setTextArea = function() {

    }

    this.B = function(obj, id){
        $(id).selection('insert', {text: '[b]', mode: 'before'});
        $(id).selection('insert', {text: '[/b]', mode: 'after'});
    }
    this.I = function(obj, id){
        $(id).selection('insert', {text: '[i]', mode: 'before'});
        $(id).selection('insert', {text: '[/i]', mode: 'after'});
    }
    this.U = function(obj, id){
        $(id).selection('insert', {text: '[u]', mode: 'before'});
        $(id).selection('insert', {text: '[/u]', mode: 'after'});
    }
    this.Hide = function(obj, id){
        $(id).selection('insert', {text: '[hide="Спойлер"]', mode: 'before'});
        $(id).selection('insert', {text: '[/hide]', mode: 'after'});
    }
    this.Info = function(obj, id){
        $(id).selection('insert', {text: '[info]', mode: 'before'});
        $(id).selection('insert', {text: '[/info]', mode: 'after'});
    }
    this.Link = function(obj, id){
        $(id).selection('insert', {text: '[link="http://url"]', mode: 'before'});
        $(id).selection('insert', {text: '[/link]', mode: 'after'});
    }
    this.Image = function(obj, id){
        $(id).selection('insert', {text: '[image]', mode: 'before'});
        $(id).selection('insert', {text: '[/image]', mode: 'after'});
    }
    this.Youtube = function(obj, id){
        $(id).selection('insert', {text: '[youtube=""]', mode: 'before'});
    }
    this.Quote = function(obj, id){
        $(id).selection('insert', {text: '[quote=""]', mode: 'before'});
        $(id).selection('insert', {text: '[/quote]', mode: 'after'});
    }
    this.Audio = function(obj, id){
        $(id).selection('insert', {text: '[mp3]', mode: 'before'});
        $(id).selection('insert', {text: '[/mp3]', mode: 'after'});
    }
    /*this.Color = function(obj){
        $(id).selection('insert', {text: '[color=""]', mode: 'before'});
        $(id).selection('insert', {text: '[/color]', mode: 'after'});
    }*/
    /*this.Smile = function(obj, id){
        $(id).selection('insert', {text: '[smile=""]', mode: 'before'});
    }*/
}

var smileBuild = false;
function buildSmiles()
{
    $('.popover_btn').popover('hide').popover('destroy');
    var content = '<div class="smiles_block">';

    $.each(smiles, function(i, el){
        content += '<img id="'+el+'" class="smile_item" src="http://i.oldbk.com/i/smiles/'+el+'.gif">';
    });
    content += '</div>';
    content += '<div class="popover_close">Закрыть</div>';

    $('#'+editorBlockId+' #smile').popover({
        html      : true,
        placement : 'bottom',
        content   : content
    });
    $('#'+editorBlockId+' #smile').popover('toggle');
    setTimeout(function(){
        $.fancybox.update();
    }, 1000);

    if(smileBuild === false) {
        $('#'+editorBlockId+' #smile').popover('hide');
        $('#'+editorBlockId+' #smile').popover('destroy');
        smileBuild = true;
    }
}

function buildTable()
{
    $('.popover_btn').popover('hide').popover('destroy');

    var $content = $('<div>', {'id':'choose_table'});
    var $ul = $('<ul>', {'id':'table_settings'}).appendTo($content);

    var $row = $('<li>').appendTo($ul);
    var $column = $('<li>').appendTo($ul);

    $row.html('<label>Кол-во строк:</label><input type="text" class="num" id="table_row">');
    $column.html('<label>Кол-во колонок:</label><input type="text" class="num" id="table_column">');

    var $buttons = $('<div>', {'style':'margin-top:10px;text-align:center;'}).appendTo($content);

    $buttons.append('<div style="display: inline-block;margin-right: 5px;" id="add_table" class="popover_ok btn">Добавить</div>');
    $buttons.append('<div style="display: inline-block;" class="popover_close btn">Закрыть</div>');

    $('#'+editorBlockId+' #table').popover({
        html      : true,
        placement : 'bottom',
        content   : $content[0].outerHTML
    }).popover('show');
}

$(function(){
    $(document.body).off('click', '.popover_close');
    $(document.body).on('click', '.popover_close', function(event){
        event.preventDefault();
        $('.popover_btn').popover('hide').popover('destroy');
        $.fancybox.update();
    });

    $(document.body).off('click', '.smile_item');
    $(document.body).on('click', '.smile_item', function(event){
        event.preventDefault();
        $('#'+editorID).selection('insert', {text: '[smile="'+$(this).attr('id')+'"]', mode: 'before'});
    });

    $(document.body).off('click', '#add_table');
    $(document.body).on('click', '#add_table', function(event){
        event.preventDefault();

        var r = parseInt($('#table_settings #table_row').val());
        var c = parseInt($('#table_settings #table_column').val());

        var text = '[table]';
        text += '[tr]';
        var i, row, column;
        for(i = 1; i <= c; i++)
            text += '[th]Заголовок '+i+'[/th]';
        text += '[/tr]';

        for(row = 1; row <= r; row++) {
            text += '[tr]';
            for(column = 1; column <= c; column++) {
                text += '[td]Ячейка '+row+' - '+column+'[/td]';
            }
            text += '[/tr]';
        }
        text += '[/table]';

        $('#'+editorID).selection('insert', {text: text, mode: 'before'});
        $('.popover_btn').popover('hide').popover('destroy');
    });

    $(document.body).on('click', '.set_quote', function(){
        var val = $('#'+editorID).val();
        $('#'+editorID).val(val + $(this).attr('data-content'));
        return false;
    });

    $('.num').keyup(function () {
        this.value = this.value.replace(/[^0-9\.]/g,'');
    });
});