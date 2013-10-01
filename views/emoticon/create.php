<?php $this->widget('EmoticonToolbar', array('controller' => $this->id, 'action' => $this->action->id)); ?>

<h1>Upload Emoticons</h1>

You can upload both individual files and zip files together. When the "enter new set name" field is filled in, it will take priority over your drop down selection.

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'emoticon-form',
    'enableAjaxValidation'=>false,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

<div class="form">
    <div class="row">
        <?php echo $form->labelEx($model,'set_id'); ?>
        <?php echo $form->dropDownList($model, 'set_id', EmoticonSet::model()->listData);?>
        <?php echo $form->error($model,'set_id'); ?>

        or enter new set name: <?php echo $form->textField($model, 'set_name');?>
    </div>

    <?php
    
    $this->widget('CTabView', array(
    'tabs'=>array(
        'tab1'=>array(
            'title'=>'Upload files',
            'view'=>'_files',
            'data'=>array('model'=>$model, 'form' => $form),
        ),
        'tab2'=>array(
            'title'=>'Upload files (HTML5)',
            'view'=>'_html5',
            'data'=>array('model'=>$model, 'form' => $form),
        ),
        'tab3'=>array(
            'title'=>'Upload a zip file',
            'view'=>'_zip',
            'data'=>array('model'=>$model, 'form' => $form),
        ),
       
        
    ),
  )); 

  ?>


    <div class="row buttons">
        <?php echo CHtml::submitButton('Upload', array('class' => 'btn btn-success')); ?>
    </div>

<?php $this->endWidget(); ?>

</div>