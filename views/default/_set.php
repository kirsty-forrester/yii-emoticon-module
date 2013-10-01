<?php $index++; ?>
<?php if( ($index == 1) || ($index % 4 == 0)): ?>
    <div class="row-fluid">
<?php endif; ?>

<div class="emoticon-set<?php if($index % 4 == 0) echo ' pull-right'; ?>">
    <h4><?php echo $data->name;?></h4>
    <small><?php echo number_format(count($data->emoticons));?> emoticons</small>
    <div class="preview-emoticons">
        <?php echo EmoticonHelper::displayAll($data->emoticons); ?>
    </div>
    <div class="set-buttons clearfix">
    <?php
        echo CHtml::link(
            '<img src="'.$this->getModule()->registerImage('zoom.png').'">View',
            array('emoticonSet/view', 'id' => $data->id));

        echo CHtml::link(
            '<img src="'.$this->getModule()->registerImage('pencil.png').'">Edit',
            array('emoticonSet/update', 'id' => $data->id));

        echo CHtml::link(
            '<img src="'.$this->getModule()->registerImage('delete.png').'">Delete',
                Yii::app()->createUrl('emoticons/emoticonSet/delete', array('id' => $data->id)), array(
                'class' => 'delete-set',
        ));

        echo CHtml::activeDropDownList($data, 'visible', EmoticonSet::model()->visibleValues, array(
            'class' => 'pull-right input-small update-list-value',
            'data-id' => $data->id,
            'data-name' => 'visible',
            'data-class' => 'EmoticonSet',
            'data-url' => Yii::app()->createUrl('emoticons/emoticonSet/updateColumn'),
        ));
    ?>
    </div>
</div>

<?php if($index % 4 == 0 || (isset($numSets) && ($index == $numSets))) : ?>
    </div>
<?php endif; ?>



