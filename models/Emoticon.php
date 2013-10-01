<?php

/**
 * This is the model class for table "emoticon".
 *
 * The followings are the available columns in table 'emoticon':
 * @property integer $id
 * @property integer $set_id
 * @property string $file_name
 * @property string $alt_text
 * @property integer $width
 * @property integer $height
 *
 * The followings are the available model relations:
 * @property EmoticonSet $set
 */
class Emoticon extends CActiveRecord
{
	public $images;
	public $multiple;
	public $zip;
	public $image;
	private $_oldAttributes;
	public $set_name;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Emoticon the static model class
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
		return '{{emoticon}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('set_id', 'required'),
			array('set_id, position, width, height', 'numerical', 'integerOnly'=>true),
			array('position', 'default', 'value' => 0),
			array('alt_text', 'length', 'max'=>100, 'allowEmpty' => true),
			array('code', 'length', 'max'=>25),
			array('code', 'unique'),
			array('zip', 'file', 'types' => 'zip', 'allowEmpty' => true),
			array('image', 'file', 'types'=>'jpg, gif, png', 'allowEmpty' => true),
			array('alt_text, code, file_name','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('set_id, file_name, alt_text, code, width, height, position, image,', 'safe', 'on'=>'search'),
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
			'set' => array(self::BELONGS_TO, 'EmoticonSet', 'set_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'set_id' => 'Set',
			'file_name' => 'File Name',
			'alt_text' => 'Alt Text',
			'width' => 'Width',
			'height' => 'Height',
			'code' => 'Code',
			'images' => 'Images (Select one at a time)',
			'multiple' => 'Images (Select multiple at a time)',
			'zip' => 'Upload a zip file',
			'position' => 'Order',
			'image' => 'Replace image',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->with = 'set';

		$criteria->compare('t.id',$this->id);
		$criteria->compare('set_id',$this->set_id);
		$criteria->compare('t.position',$this->position);
		$criteria->compare('file_name',$this->file_name,true);
		$criteria->compare('alt_text',$this->alt_text,true);
		$criteria->compare('width',$this->width);
		$criteria->compare('height',$this->height);
		$criteria->compare('code',$this->code, true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array('pageSize' => 25),
		));
	}

	public function scopes()
	{
		return array(
			'ordered' => array(
				'order' => 't.position ASC, t.id DESC',
			),
			'relationOrdered' => array(
				'order' => 'emoticons.position ASC, emoticons.id DESC',
			),
			'newest' => array(
				'order' => 't.id DESC',
			),
			'visibleSets' => array(
				'with' => 'set',
				'condition' => 'set.visible = 1',
			),
			'sets' => array(
				'with' => 'set',
				'order' => 'set_id DESC, t.position ASC, t.id DESC',
			),
		);
	}

	public function defaultScope()
	{
		return array(
			'with' => 'set',
		);
	}
  

	public function afterFind()
	{
		$this->_oldAttributes = $this->attributes;

		return parent::afterFind();
	}

	public function beforeSave()
	{
		// Remove the random string prefixed to the file name
		$baseName = explode('_', $this->file_name, 2);

		if(empty($this->alt_text)){
            $name = preg_replace("/\\.[^.\\s]{3,4}$/", "", $baseName[1]); // remove extension
            $this->alt_text = preg_replace("/[^a-zA-Z0-9]/", " ", $name);
        }
        if(empty($this->code)){
            $name = preg_replace("/\\.[^.\\s]{3,4}$/", "", $baseName[1]); // remove extension
            $code = strtolower(preg_replace("/[^a-zA-Z0-9]+/", "", $name));
            $this->code = ':' . $this->createUniqueCode($code) . ':';
        }

        if(!$this->isNewRecord){
	        // If set has changed, need to move image
	        if($this->set_id != $this->_oldAttributes['set_id']){

	        	$oldFolder = EmoticonSet::model()->getSlug($this->_oldAttributes['set_id']);
	        	$newFolder = EmoticonSet::model()->getSlug($this->set_id);
	        	$oldPath = Yii::app()->controller->module->uploadPath . $oldFolder . '/';
	        	$newPath = Yii::app()->controller->module->uploadPath . $newFolder . '/';

	        	if(file_exists($oldPath . $this->file_name))
	        		rename($oldPath . $this->file_name, $newPath . $this->file_name);
	        }

	        // If image has been replaced
	        if($this->file_name != $this->_oldAttributes['file_name']){
	        	$this->setDimensions();
	        }
    	}
		// Calculate width and height of emoticon
		else{
			$this->setDimensions();
		}

		return parent::beforeSave();
	}

	public function setDimensions()
	{
		$folder = EmoticonSet::model()->getSlug($this->set_id);
		$dimensions = $this->getDimensions($folder);

		if(!$dimensions)
			return false;

		$this->width = $dimensions['width'];
		$this->height = $dimensions['height'];	
	}
	
	/**
	 * Check to see if the insertion code for the emoticon is unique
	 * @param string $code the desired code
	 */
	public function createUniqueCode($code)
	{
		$counter = 0;
		$results = self::model()->findAllByAttributes(array('code' => ':' . $code . ':'));

    foreach($results as $result){
        $checkCode = sprintf('%s%d', $code, ++$counter);
    }

    return $counter > 0 ? $checkCode : $code;
	}
	
	public function beforeDelete()
	{
      $folder = $this->set->slug;
      $path = Yii::app()->controller->module->uploadPath . $folder . '/';
	    unlink($path . $this->file_name);

	    return parent::beforeDelete();
	}
	
	public function afterSave()
	{
		if($this->isNewRecord){
			$dimensions = $this->getDimensions();
			$this->width = $dimensions['width'];
			$this->height = $dimensions['height'];
		}
		return parent::afterSave();
	}
	
	/**
	 * Gets all emoticons that belong to a particular set
	 * @param string $set the URL slug for the set
	 */
	public function bySet($set)
	{
		if(!empty($set)){
			$this->getDbCriteria()->mergeWith(array(
		    	'condition' => 'set.slug = :slug',
		    	'with' => 'set',
		    	'params' => array(
		    		':slug' => $set,
		    	),
		    ));
		}

		return $this;
	}
  
  /**
	 * Gets the image URL for the emoticon
	 */
	public function getImageUrl($publicPath = false)
	{
		if(!$publicPath){
			$publicPath = Yii::app()->controller->module->publicPath;
		}
		return $publicPath . $this->set->slug . '/' . $this->file_name;
	}
	
	/**
	 * Sets the new ordering position of the emoticon based on user input
	 * @param string $direction whether the user wants to move the emoticon up or down
	 */
	public function setNewPosition($direction)
	{
  	$numOrdered = $this->set->countOrdered();
  	
  	if($numOrdered <= 0){
      $this->position = 2;
    } else {
      
      if($direction == 'up')
          $this->position--;
      elseif($direction == 'down')
          $this->position++;
    }
	}
  
  /**
	 * Gets the dimensions of the emoticon
	 * @param string $folder the folder where the emoticon is stored. Defaults to the set's slug
	 */
	public function getDimensions($folder = false)
	{
		if(!$folder) $folder = $this->set->slug;
		if(file_exists(Yii::app()->controller->module->uploadPath . '/' . $folder . '/' . $this->file_name)){
			list($width, $height) = getimagesize(Yii::app()->controller->module->uploadPath . '/' . $folder . '/' . $this->file_name);
			return array('width' => $width, 'height' => $height);
		}

		return false;
	}
}