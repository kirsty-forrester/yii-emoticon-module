<?php
$this->breadcrumbs=array(
    'Emoticon Sets'=>array('index'),
    $model->set->name => array('emoticonSet/update', 'id' => $model->set_id),
    $model->alt_text
);

?>
<?php $this->widget('EmoticonToolbar', array('controller' => $this->id, 'action' => $this->action->id)); ?>

<h1>Update <?php echo $model->alt_text; ?> emoticon</h1>

<img src="<?php echo $model->imageUrl;?>" width="<?php echo $model->width;?>" height="<?php echo $model->height;?>">

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'emoticon-update-form',
    'enableAjaxValidation'=>false,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'set_id'); ?>
        <?php echo $form->dropDownList($model, 'set_id', EmoticonSet::model()->listData);?>
        <?php echo $form->error($model,'set_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'alt_text'); ?>
        <?php echo $form->textField($model,'alt_text'); ?>
        <?php echo $form->error($model,'alt_text'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'code'); ?>
        <?php echo $form->textField($model,'code'); ?>
        <?php echo $form->error($model,'code'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'width'); ?>
        <?php echo $form->textField($model,'width'); ?>
        <?php echo $form->error($model,'width'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'height'); ?>
        <?php echo $form->textField($model,'height'); ?>
        <?php echo $form->error($model,'height'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'image'); ?>
        <?php echo $form->fileField($model,'image'); ?>
        <?php echo $form->error($model,'image'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Submit', array('class' => 'btn btn-primary')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->