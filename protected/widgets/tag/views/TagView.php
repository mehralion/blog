<?php
$tag_it=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.widgets.tag').'/tag-it.min.js');
$tag_it_css=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.widgets.tag').'/jquery.tagit.css');
$cs=Yii::app()->clientScript;
$cs->registerScriptFile($tag_it);
$cs->registerCssFile($tag_it_css);

$cs->registerScript($id,'
    $("#'.$id.'").tagit({
        fieldName: "Tags[]",
        allowSpaces: true,
        autocomplete: {
            source: function( request, response ) {
                $.ajax({
                    url: "'.$url.'",
                    data: { tag:request.term },
                    dataType: "json",
                    success: function( data ) {
                        response( $.map( data, function( item ) {
                            return {
                                label: item.label,
                                value: item.label
                            }
                        }));
                    }
                });
            },
            minLength: 2,
            delay: 0
        }
    });
', CClientScript::POS_READY);

?>

<label for="<?php echo CHtml::encode($id);?>">Теги</label>
<ul id="<?php echo CHtml::encode($id);?>">
    <?php foreach($tags as $tag): ?>
        <li><?php echo $tag; ?></li>
    <?php endforeach; ?>
</ul>