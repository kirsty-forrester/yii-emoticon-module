<?php

class EmoticonParser extends CWidget
{

    public static function parse($text)
    {
        $module = Yii::app()->getModule('emoticons');
        $emoticons = array();
        $emoticonTable = Emoticon::model()->tableName();
        $emoticonSetTable = EmoticonSet::model()->tableName();
        $dependency = new CDbCacheDependency('SELECT COUNT(id) FROM ' . $emoticonTable);

        $sql = 'SELECT set_id, file_name, alt_text, code, width, height, slug, visible FROM '.$emoticonTable.' e INNER JOIN '.$emoticonSetTable.' s on e.set_id = s.id AND visible = 1';
        $command = Yii::app()->db->cache(40000, $dependency)->createCommand($sql);
        $rows = $command->queryAll();

        foreach($rows as $row){
            $imageUrl = $module->publicPath . $row['slug'] . '/' . $row['file_name'];
            $emoticons[$row['code']] = '<img src="'.$imageUrl.'">';
        }

        return str_replace(array_keys($emoticons), array_values($emoticons), $text);
    }

}