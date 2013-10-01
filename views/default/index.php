
<?php

$this->widget('EmoticonToolbar', array('controller' => $this->id, 'action' => $this->action->id));

Yii::app()->clientScript->registerScript('deleteUrl', "
    EmoticonsModule.deleteSetUrl = '".Yii::app()->createUrl('emoticons/emoticonSet/delete')."';");
?>

<h1 class="main-header">Your Emoticon Sets</h1>

<?php
$this->widget('zii.widgets.CListView', array(
    'id' => 'emoticon-set-list',
    'dataProvider'=> $emoticonSets,
    'emptyText' => 'No emoticon sets found',
    'itemView'=>'_set',
    'ajaxUrl' => Yii::app()->request->requestUri,
    'sortableAttributes'=>array(
        'id'=>'Date',
        'name',
    ),
));
?>

<?php if(!Yii::app()->request->isAjaxRequest): ?>
<?php echo CHtml::link('View all emoticons', array('emoticon/index'), array('class' => 'view-more'));?>
<?php endif; ?>