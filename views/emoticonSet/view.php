<?php $this->widget('EmoticonToolbar', array('controller' => $this->id, 'action' => $this->action->id)); ?>


<?php

$this->breadcrumbs=array(
	'Emoticons Home' => array('default/index'),
	'Emoticon Sets'=>array('index'),
	$model->name,
);

?>
<h1>View <?php echo $model->name; ?> Emoticon Set</h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'position',
		'visible',
	),
)); ?>


<?php if($model->numEmoticons > 0): ?>

<div class="emoticon-container">
	<?php 
		$index = 1;
		foreach($model->emoticons as $emoticon): ?>
			<?php if( ($index == 1) || ($index % 5 == 0)): ?>
			    <div class="row-fluid">
			<?php endif; ?>

			<div class="one-emoticon<?php if($index % 5 == 0) echo ' pull-right'; ?>">
				<h4><?php echo $emoticon->alt_text;?></h4>
				<?php echo CHtml::image($emoticon->imageUrl, $emoticon->code, array(
					'width' => $emoticon->width, 'height' => $emoticon->height
				)); ?>
				<p>
					Code: <?php echo $emoticon->code;?><br>
					Dimensions: <?php echo $emoticon->width;?> x <?php echo $emoticon->height;?>
				</p>
			</div>

			<?php if( $index % 5 == 0 || ($index == $model->numEmoticons) ): ?>
			    </div>
			<?php endif; ?>
			<?php $index++; ?>
	<?php endforeach; ?>
</div>

<?php else: ?>

<?php 

	echo CHtml::link('Upload emoticons to this set', array('emoticon/create', 'set_id' => $model->id), array('class' => 'btn btn-success'));

endif;