<?php

$this->breadcrumbs=array(
	'Emoticons Home'=>array('default/index'),
	'Emoticon Sets',
);

Yii::app()->clientScript->registerScript('deleteUrl', "
    EmoticonsModule.deleteSelectedUrl = '".Yii::app()->createUrl('emoticons/emoticonSet/deleteSelected')."';
", 
    CClientScript::POS_END);
?>

<?php $this->widget('EmoticonToolbar', array('controller' => $this->id, 'action' => $this->action->id)); ?>


<h1>Emoticon Sets</h1>

<?php echo CHtml::htmlButton('Delete selected', array('class' => 'delete-selected btn btn-danger'));?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'emoticon-set-grid',
	'dataProvider'=>$model->ordered()->search(),
	'emptyText' => 'No emoticon sets found',
	'filter'=>$model,
	'columns'=>array(
		array('class'=>'CCheckBoxColumn', 'value' => '$data->id', 'header'=>'', 'selectableRows' => 2),
		array(
			'class' => 'AjaxInputColumn',
			'name' => 'name',
			'url' => array('emoticonSet/updateColumn'),
		),
		array(
			'class' => 'AjaxInputColumn',
			'name' => 'slug',
			'url' => array('emoticonSet/updateColumn'),
		),
		array('header' => 'Preview', 'type' => 'raw', 'value' => 'EmoticonHelper::displayAll($data->emoticons);'),
		array(
			'class' => 'AjaxSelectColumn',
			'name' => 'visible',
			'selectOptions' => $model->visibleValues,
			'url' => array('emoticonSet/updateColumn'),
		),
		array(
			'class' => 'CButtonColumn',
			'header' => 'Reorder',
			'template' => '{moveUp}{moveDown}',
			'buttons' => array(
				'moveUp' => array(
					'label' => 'Up',
					'imageUrl' => Yii::app()->controller->module->registerImage('up.png', 'Move up'),
					'url' => 'Yii::app()->controller->createUrl("emoticonSet/order", array("id" => $data->id, "direction" => "up"))',
					'options' => array(
						'ajax' => array(
							'type' => 'post',
							'url'=>'js:$(this).attr("href")',
							'success' => 'js:function(data){$.fn.yiiGridView.update("emoticon-set-grid")}'
						),
					),
				),
				'moveDown' => array(
					'label' => 'Up',
					'imageUrl' => Yii::app()->controller->module->registerImage('down.png', 'Move down'),
					'url' => 'Yii::app()->controller->createUrl("emoticonSet/order", array("id" => $data->id, "direction" => "down"))',
					'options' => array(
						'ajax' => array(
							'type' => 'post',
							'url'=>'js:$(this).attr("href")',
							'success' => 'js:function(data){$.fn.yiiGridView.update("emoticon-set-grid")}'
						),
					),
				),
			),
		),
		array(
			'class'=>'CButtonColumn',
			'template' => '{view}{update}{delete}',
            'viewButtonImageUrl' => $this->getModule()->registerImage('zoom.png'),
            'updateButtonImageUrl' => $this->getModule()->registerImage('pencil.png'),
            'deleteButtonImageUrl' => $this->getModule()->registerImage('delete.png'),
		),
	),
)); ?>
