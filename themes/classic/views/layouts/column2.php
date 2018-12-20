<?php $this->beginContent('//layouts/main'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/style.css"/>
<div class="container">
	<div class="span-18">
		<div id="content">
			<?php echo $content; ?>
		</div><!-- content -->
	</div>
	<div class="span-6 last">
		<div id="sidebar">
            <div id="adminmenu">
                <?php $this->widget('zii.widgets.CMenu', array(
                'items' => array(
                    array('label' => 'Главная', 'url' => array('/admin/default/index')),
                    array('label' => 'Объекты', 'url' => array('/admin/item/index')),
                    array('label' => 'Разделы', 'url' => array('/admin/category/index')),
                    array('label' => 'Материалы', 'url' => array('/admin/material/index')),
                ),
            )); ?>
            </div>
		</div><!-- sidebar -->
	</div>
</div>
<?php $this->endContent(); ?>