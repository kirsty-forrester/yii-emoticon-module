<?php 

$this->breadcrumbs=array(
    'Emoticons Home'=>array('default/index'),
    'All Emoticons',
);

$this->widget('EmoticonToolbar', array('controller' => $this->id, 'action' => $this->action->id));

?>
<h1>All Emoticons</h1>

<?php

$this->renderPartial('_grid', array('model' => $model, 'order' => false));

?>
