<h1>Install Emoticons Module</h1>
Before submitting the form below, please make sure that the module is install mode. To do that, change your module config as shown:
<pre>
'modules'=>array(
    ...
    'emoticons' => array(
        'uploadFolder' => 'images/emoticons',
        'admins' => array('admin'),
        'install' => true, // Add this line, then remove after installation is complete
    ),
    ...  
),
</pre>



<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'install-form',
    'enableAjaxValidation'=>false,
)); ?>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'emoticonTable'); ?>
        <?php echo $form->textField($model,'emoticonTable'); ?>
        <?php echo $form->error($model,'emoticonTable'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'emoticonSetTable'); ?>
        <?php echo $form->textField($model,'emoticonSetTable'); ?>
        <?php echo $form->error($model,'emoticonSetTable'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'exampleSet'); ?>
        <?php echo $form->checkBox($model, 'exampleSet'); ?>
        <?php echo $form->error($model,'exampleSet'); ?>
    </div>


    <div class="row buttons">
        <?php echo CHtml::submitButton('Submit', array('class' => 'btn btn-primary')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->