<?php

$this->breadcrumbs=array(
    'Emoticons Home'=>array('default/index'),
    'Emoticon Sets'=>array('index'),
    $set->name . ' Set',
);

?>

<?php $this->widget('EmoticonToolbar', array('controller' => $this->id, 'action' => $this->action->id)); ?>

<h1><span id="set-name"><?php echo $set->name; ?></span> Emoticon Set</h1>

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'emoticon-set-form',
    'htmlOptions' => array('class' => 'form-inline'),
)); ?>

    <?php echo $form->labelEx($set,'name'); ?>
    <?php echo $form->textField($set,'name',array('size'=>30,'maxlength'=>100)); ?>
    <?php echo $form->error($set,'name'); ?>


    <?php echo $form->labelEx($set,'visible'); ?>
    <?php
    echo $form->dropDownList($set, 'visible', EmoticonSet::model()->visibleValues, array(
            'class' => 'update-list-value',
            'data-id' => $set->id,
            'data-name' => 'visible',
            'data-class' => 'EmoticonSet',
            'data-url' => Yii::app()->createUrl('emoticons/emoticonSet/updateColumn'),
        ));
        ?>
    <?php echo $form->error($set,'visible'); ?>

    <?php echo CHtml::ajaxSubmitButton(
        'Save',
        '',
        array('update' => '#set-name'),
        array('class' => 'btn btn-primary')
    ); ?>

<?php $this->endWidget(); ?>


<?php 

    echo CHtml::link(
        'Upload emoticons to this set',
        array('emoticon/create', 'set_id' => $set->id),
        array('class' => 'btn btn-success'
    )); 
?>

<?php echo $this->renderPartial('/emoticon/_grid', array('model' => $model, 'order' => true));