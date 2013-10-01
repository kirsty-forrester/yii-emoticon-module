<?php

class EmoticonsModule extends CWebModule
{
	// Table names
	//public $emoticonTable = 'emoticons';
	//public $emoticonSetTable = 'emoticon_set';
	public $install;

	public $uploadPath;
	public $publicPath;
	public $uploadFolder;
	public $admins;

	public $cssFile = 'emoticons.css';
	public $scriptFile = 'emoticons.js';
	public $forceCopy = false;

	private $_assetsUrl;
	
	public function init()
	{
		Yii::setPathOfAlias('emoticons', Yii::getPathOfAlias('application.modules.emoticons'));

		$this->uploadFolder = isset($this->params['uploadFolder']) ? $this->params['uploadFolder'] : 'images/emoticons';
		$this->uploadPath = Yii::getPathOfAlias('webroot') . '/' . $this->uploadFolder . '/';
		$this->publicPath = Yii::app()->baseUrl . '/' . $this->uploadFolder . '/';

		$this->publishAssets();

		Yii::app()->clientScript->registerScript('namespace', 'var EmoticonsModule = {};', CClientScript::POS_HEAD);
		// import the module-level models and components
		$this->setImport(array(
			'emoticons.models.*',
			'emoticons.components.*',
			'emoticons.components.actions.*',
		));

		if(!isset(Yii::app()->db->tablePrefix))
		  Yii::app()->db->tablePrefix = '';
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		
		return false;
	}

	/**
	* @return string the base URL that contains all published asset files of this module.
	*/
	public function getAssetsUrl()
	{
		if($this->_assetsUrl===null)
			$this->_assetsUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('emoticons.assets'));

		return $this->_assetsUrl;
	}

	public function registerImage($file)
	{
		return $this->getAssetsUrl().'/img/'.$file;
	}

	protected function publishAssets($loadAssets=true)
    {
        $publish = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.emoticons').'/assets/',false,-1,$this->forceCopy);

        if($loadAssets)
        {
            Yii::app()->clientScript->registerCoreScript('jquery');
            
            if($this->cssFile == 'emoticons.css')
                Yii::app()->clientScript->registerCssFile($publish.'/css/emoticons.css');
            else
                Yii::app()->clientScript->registerCssFile($this->cssFile);
            
            if($this->scriptFile == 'emoticons.js')
                Yii::app()->clientScript->registerScriptFile($publish.'/js/emoticons.js'); 
            else
                Yii::app()->clientScript->registerScriptFile($this->scriptFile);    
                
        }
        return $publish;
    }
}
