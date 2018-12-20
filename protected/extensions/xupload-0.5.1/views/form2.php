<!-- The file upload form used as target for the file upload widget -->
<?php if ($this->showForm) echo CHtml::beginForm($this->url, 'post', $this->htmlOptions);?>
<div class="m_button">
    <span class="fileinput-button <?php echo $this->btnClass; ?>" style="float: none;">
            <span><?php echo $this->title; ?></span>
        <?php
        if ($this->hasModel()) :
            echo CHtml::activeFileField($this -> model, $this -> attribute, $htmlOptions) . "\n";
        else :
            echo CHtml::fileField($this->name, $this -> value, $htmlOptions) . "\n";
        endif;
        ?>
		</span>
</div>
<?php if ($this->showForm) echo CHtml::endForm();?>
