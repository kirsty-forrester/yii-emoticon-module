<?php

Yii::app()->clientScript->registerScript('deleteUrl', "
    EmoticonsModule.deleteSelectedUrl = '".Yii::app()->createUrl('emoticons/emoticon/deleteSelected')."';
    EmoticonsModule.updateSetIdUrl = '".Yii::app()->createUrl('emoticons/emoticon/updateSelected')."';

    // If you try to change the filter to a different set, redirect to that set's page
    $(function(){
        $('select[name=\"Emoticon[set_id]\"]').change(function(){
            window.location.href = '".Yii::app()->baseUrl."/emoticons/emoticonSet/update/id/' + $(this).val();
        });
    });
", 
    CClientScript::POS_END);

$columns = array(
        array('class'=>'CCheckBoxColumn', 'value' => '$data->id', 'header'=>'', 'selectableRows' => 2),
        array(
            'class' => 'AjaxSelectColumn',
            'name' => 'set_id',
            'selectOptions' => EmoticonSet::model()->listData,
            'url' => array('emoticon/updateColumn'),
            'filter' => EmoticonSet::model()->listData,
        ),
        'position',

        array(
            'name' => 'file_name',
            'header' => 'Image',
            'type' => 'html',
            'value' => 'CHtml::image($data->imageUrl)',
        ),

        array(
            'class' => 'AjaxInputColumn',
            'name' => 'alt_text',
            'url' => array('emoticon/updateColumn'),
        ),
        array(
            'class' => 'AjaxInputColumn',
            'name' => 'code',
            'url' => array('emoticon/updateColumn'),
        ),
        'width',
        'height',
        );
    /**
     * Don't show order column on pages where all emoticons are shown, because ordering goes by set
     */
    $scope = $order ? 'ordered' : 'newest';
    if($order) array_push($columns,  
        array(
            'class' => 'CButtonColumn',
            'header' => 'Reorder',
            'template' => '{moveUp}{moveDown}',
            'buttons' => array(
                'moveUp' => array(
                    'label' => 'Up',
                    'imageUrl' => Yii::app()->controller->module->registerImage('up.png', 'Move up'),
                    'url' => 'Yii::app()->controller->createUrl("emoticon/order", array("id" => $data->id, "direction" => "up"))',
                    'options' => array(
                        'ajax' => array(
                            'type' => 'post',
                            'url'=>'js:$(this).attr("href")',
                            'success' => 'js:function(data){$.fn.yiiGridView.update("emoticon-grid")}'
                        ),
                    ),
                ),
                'moveDown' => array(
                    'label' => 'Up',
                    'imageUrl' => Yii::app()->controller->module->registerImage('down.png', 'Move down'),
                    'url' => 'Yii::app()->controller->createUrl("emoticon/order", array("id" => $data->id, "direction" => "down"))',
                    'options' => array(
                        'ajax' => array(
                            'type' => 'post',
                            'url'=>'js:$(this).attr("href")',
                            'success' => 'js:function(data){$.fn.yiiGridView.update("emoticon-grid")}'
                        ),
                    ),
                ),
            ),
    ));
  
    array_push($columns,
        array(
            'class'=>'CButtonColumn',
            'viewButtonImageUrl' => $this->getModule()->registerImage('zoom.png'),
            'viewButtonUrl' => 'Yii::app()->createUrl("emoticons/emoticon/view", array("id" => $data->id))',
            'updateButtonImageUrl' => $this->getModule()->registerImage('pencil.png'),
            'updateButtonUrl' => 'Yii::app()->createUrl("emoticons/emoticon/update", array("id" => $data->id))',
            'deleteButtonImageUrl' => $this->getModule()->registerImage('delete.png'),
            'deleteButtonUrl' => 'Yii::app()->createUrl("emoticons/emoticon/delete", array("id" => $data->id))',
        ));


?>
<?php echo CHtml::htmlButton('Delete selected', array('class' => 'delete-selected btn btn-danger'));?>

Move selected emoticons to: <?php echo CHtml::dropDownList('move_set_id', '', array(0 => 'Select one') + EmoticonSet::model()->listData); ?>

<?php 
    $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'emoticon-grid',
        'dataProvider'=>$model->{$scope}()->search(),
        'template' => "{pager}{summary}\n{items}\n{pager}",
        'emptyText' => 'No emoticons found',
        'filter'=>$model,
        'columns'=>
            $columns,
    ));
?>