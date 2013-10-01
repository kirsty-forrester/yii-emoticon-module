<?php

class EmoticonHelper extends CComponent
{

    public static function displayAll($emoticons, $path = false)
    {
        $images = array();

        foreach($emoticons as $e){
            $images[] = CHtml::image($e->getImageUrl($path), $e->alt_text, array(
                'class' => 'add-emoticon',
                'width' => $e->width,
                'height' => $e->height,
                'data-code' => $e->code,
            ));
        }

        return implode('', $images);

    }

}