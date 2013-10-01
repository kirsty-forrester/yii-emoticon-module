<div class="row">
    <?php echo $form->labelEx($model,'images'); ?>
    <?php
      $attribute = 'images';
      $this->widget('CMultiFileUpload', array(
         'model'=> $model,
         'name'=> CHtml::resolveName($model, $attribute),
         'accept'=> 'jpeg|jpg|gif|png',
         'duplicate' => 'Duplicate file!',
         'denied' => 'Invalid file type',
      ));
    ?>
    <?php echo $form->error($model,'images'); ?>
</div>