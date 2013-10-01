<div class="row">
    <?php echo $form->labelEx($model,'multiple'); ?>
    <?php echo $form->fileField($model, 'multiple[]', array('multiple' => 'multiple')); ?>
    <?php echo $form->error($model,'multiple'); ?>
</div>