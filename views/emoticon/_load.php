<span class="expanded">
<?php
    echo EmoticonHelper::displayAll($emoticons, $publicPath);
?>
</span>

<?php
   Yii::app()->clientScript->registerScript('showLess', "
    $(function(){
        $(document).on('click', '#show-less', function(){
            $('#show-more').removeClass('hidden');
            $('.expanded').addClass('hidden');   
            return false;
        });
    });", CClientScript::POS_END);
   echo CHtml::link('Show less', '#', array('id' => 'show-less'));
?>



