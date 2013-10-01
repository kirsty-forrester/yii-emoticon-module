<?php

class InstallForm extends CFormModel
{
    public $emoticonTable;
    public $emoticonSetTable;
    public $exampleSet;
    public $tablePrefix;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('emoticonTable, emoticonSetTable', 'required'),
            array('exampleSet', 'boolean'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'emoticonTable' => 'Table for emoticons',
            'emoticonSetTable' => 'Table for emoticon sets',
            'exampleSet' => 'Install example emoticon set (recommended)',
            'tablePrefix' => 'Database table prefix, e.g. tbl_',
        );
    }
}