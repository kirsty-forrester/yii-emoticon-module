<?php

$this->breadcrumbs=array(
	'Emoticons Home'=>array('default/index'),
	$model->set->name . ' Set' => array('emoticonSet/update', 'id' => $model->set_id),
	$model->id,
);

?>

<h1>View <?php echo $model->alt_text; ?> Emoticon</h1>

<?php echo CHtml::image($model->imageUrl, $model->code); ?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'set_id',
		'file_name',
		'alt_text',
		'code',
		'width',
		'height',
	),
)); ?>
