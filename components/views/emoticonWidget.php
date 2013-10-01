<?php echo CHtml::openTag('div', $this->htmlOptions); ?>
    <?php
    $this->render($this->viewFile, array(
            'emoticons' => $emoticons,
            'textareaId' => $this->textareaId,
            'publicPath' => $this->module->publicPath,
            'tabs' => $this->tabs,
            'display' => $this->display,
            'ajaxUrl' => $this->ajaxUrl,
        ));
    ?>
</div>