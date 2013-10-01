<?php

$this->breadcrumbs=array(
    'Emoticons Home'=>array('default/index'),
    'Emoticon Sets' => array('emoticonSet/index'),
    'Create a new set' => array('emoticonSet/create'),
);
?>

<?php $this->widget('EmoticonToolbar', array('controller' => $this->id, 'action' => $this->action->id)); ?>
<h1>Create an Emoticon Set</h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'emoticon-set-form',
)); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'name'); ?>
        <?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>100)); ?>
        <?php echo $form->error($model,'name'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'visible'); ?>
        <?php echo $form->dropdownList($model,'visible', $model->visibleValues); ?>
        <?php echo $form->error($model,'visible'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn btn-primary')); ?>
    </div>


<?php $this->endWidget(); ?>

</div><!-- form -->