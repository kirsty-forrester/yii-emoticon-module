<span class="emoticon-container">
    <?php
        echo EmoticonHelper::displayAll($emoticons, $publicPath); 
    ?>
    <span class="expanded"></span>
</span>

<?php
    Yii::app()->clientScript->registerScript('showMore', "
    $(function(){
        $(document).on('click', '#show-more', function(){
            $('#show-more').addClass('hidden');

            // If emoticons have already been loaded, don't load them again
            if($('.expanded').html().length > 0){
                $('.expanded').removeClass('hidden');
            }else{
                $('.expanded').load(".json_encode($ajaxUrl).");
            }
            return false;
        });

    });", CClientScript::POS_END);

    echo CHtml::link('Show more', '#', array('id' => 'show-more'));
?>