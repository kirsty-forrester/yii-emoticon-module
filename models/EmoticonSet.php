<?php

/**
 * This is the model class for table "emoticon_set".
 *
 * The followings are the available columns in table 'emoticon_set':
 * @property integer $id
 * @property string $name
 * @property integer $order
 * @property integer $visible
 *
 * The followings are the available model relations:
 * @property Emoticon[] $emoticons
 */
class EmoticonSet extends CActiveRecord
{
	
	const VISIBLE = 1;

	private $_oldAttributes;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return EmoticonSet the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{emoticon_set}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required', 'on' => 'create'),
			array('position, visible', 'numerical', 'integerOnly'=>true),
			array('position', 'default', 'value' => 0),
			array('name, slug', 'length', 'min'=>1),
			array('name, slug', 'length', 'max'=>50),
			array('name', 'unique'),
			array('name, slug','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('name, slug, position, visible', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'emoticons' => array(self::HAS_MANY, 'Emoticon', 'set_id'),
			'previewSet' => array(self::HAS_MANY, 'Emoticon', 'set_id', 'scopes' => 'preview'),
			'numEmoticons' => array(self::STAT, 'Emoticon', 'set_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Set Name',
			'slug' => 'Slug',
			'position' => 'Order',
			'visible' => 'Visible',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.name',$this->name,true);
		$criteria->compare('slug',$this->slug,true);
		$criteria->compare('t.position',$this->position);
		$criteria->compare('visible',$this->visible);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination' => array('pageSize' => 18),
		));
	}

	public function scopes()
	{
		return array(
			'visible' => array(
				'condition' => 't.visible = :visible',
				'params' => array(':visible' => self::VISIBLE),
			),
			'default' => array(
				'select' => 'id, name, slug, t.position, visible',
				'limit' => 1,
			),
			'preview' => array(
				'with' => array('numEmoticons', 'emoticons'),
				'together' => true,
			),
			'ordered' => array(
				'order' => 't.position ASC, t.name ASC',
			),
			'folder' => array(
				'select' => 'slug',
			),
			'list' => array(
				'select' => 'id, name',
			),
		);

	}

	public static function findBySlug($slug)
	{
		return self::model()->findByAttributes(array('slug' => $slug));
	}

	public static function getIdFromSlug($slug)
	{
		return self::findBySlug($slug)->id;
	}

	public function getPrevious()
	{
		return self::model()->find('id < :id', array(':id' => $this->id));
	}

	public function getNext()
	{
		return self::model()->find('id > :id', array(':id' => $this->id));
	}

	public function getVisibleValues()
	{
		return array(1 => 'Visible', 0 => 'Hidden');
	}

	public static function getOrderArray()
	{
		$count = self::model()->count();
		$array = array();
		for($n = 1; $n <= $count; $n++){
			$array[$n] = $n;
		}
		return $array;
	}

  /**
   * Check to see if any emoticons have been ordered yet
   */
  public function countOrdered()
  {
    $criteria = new CDbCriteria(array(
      'condition' => 't.position > :position AND t.set_id = :set_id',
      'params' => array(':position' => 0, ':set_id' => $this->id),
    ));
    
    return Emoticon::model()->count($criteria);
  }
    
	public static function getListData()
	{
		return CHtml::listData(self::model()->list()->findAll(), 'id', 'name');
	}

	public static function getSlug($set_id)
	{
		$model = self::model()->findByPk($set_id);
		return !empty($model) ? $model->slug : '';
	}

	public function toSlug($string, $space="-") {
	    if (function_exists('iconv')) {
	        $string = @iconv('UTF-8', 'ASCII//TRANSLIT', $string);
	    }
	    $string = preg_replace("/[^a-zA-Z0-9 -]/", "", $string);
	    $string = strtolower($string);
	    $string = str_replace(" ", $space, $string);
	    return $string;
	}

	public function afterFind()
	{
		$this->_oldAttributes = $this->attributes;

		return parent::afterFind();
	}

	public function beforeSave()
	{
		if(!isset($this->slug) || (empty($this->slug)))
			$this->slug = $this->toSlug($this->name);

		if((!$this->isNewRecord) && $this->slug != $this->_oldAttributes['slug']){
			$oldPath = Yii::app()->controller->module->uploadPath . $this->_oldAttributes['slug'] . '/';
			$newPath = Yii::app()->controller->module->uploadPath . $this->slug . '/';
			rename($oldPath, $newPath);
		}

		if($this->isNewRecord){
			$path = Yii::app()->controller->module->uploadPath . $this->slug . '/';

            if(!file_exists($path))
                mkdir($path);
		}

		return parent::beforeSave();
	}

	public function beforeDelete()
	{
		// Delete all emoticons
		$emoticons = Emoticon::model()->findAllByAttributes(array('set_id' => $this->id));
		$numEmoticons = count($emoticons);

		foreach($emoticons as $emoticon){
			$emoticon->delete();
		}

		// Delete folder
		$path = Yii::app()->controller->module->uploadPath . $this->slug . '/';

		if(file_exists($path . '.DS_Store'))
			unlink($path . '.DS_Store'); // Fix for my local mac only

		rmdir($path);

		return parent::beforeDelete();
	}

}


