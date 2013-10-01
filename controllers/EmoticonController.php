<?php

class EmoticonController extends EController
{
	/**
   * Methods in this class:
   *
   * init()
   * createModels()
   * createModelsFromZip()
   * actionCreate()
   * actionUpdate()
   * actionLoad()
   */
    
	/**
   * Methods inherited from EController:
   * 
   * actionView()
   * actionIndex()
   * actionOrder()
   * actionUpdateColumn()
   * actionUpdateSelected()
   * actionDelete()
   * actionDeleteSelected()
   * performAjaxValidation()
   * loadModel()
   */

  public $path, $uploadPath, $publicPath, $allowedMimeTypes;

	public function init()
	{
		$this->uploadPath = Yii::app()->controller->module->uploadPath;
		$this->publicPath = Yii::app()->controller->module->publicPath;
    $this->allowedMimeTypes = array('image/gif', 'image/jpg', 'image/png', 'image/jpeg');

		return parent::init();
	}

    /**
     * Create models from an array of CUploadedFiles
     * @param  array $images
     * @param  integer $set_id
     */
    public function createModels($images, $set_id)
    { 
        foreach($images as $image => $pic){
            $random = uniqid();
            $model = new Emoticon;
            $model->file_name = $random . '_' . $pic->name;
            $model->set_id = $set_id;
            $mime = CFileHelper::getMimeType($pic->name);

            // If mime type of file is allowed, save image and model
            if(in_array($mime, $this->allowedMimeTypes)){
                if($pic->saveAs($this->path . $random . '_' . $pic->name))
                    $model->save();
                else
                    Yii::app()->user->setFlash('error', 'Could not save');
            // If mime type is zip, use special method for processing zip
            }elseif($mime == 'application/zip'){
                $model->zip = $pic;
                $this->createModelsFromZip($model, $set_id);
            }

        } //end foreach

    }

    /**
     * Iterate through a zip file grabbing and saving the images
     * Create an Emoticon model for every image file
     */
    
    public function createModelsFromZip($model, $set_id)
    {
        $model->zip->saveAs($this->path . $model->zip->name);
        $zip = new ZipArchive;

        if($zip->open($this->path . $model->zip->name)) {
          // Extract contents of zip to temp folder
          $zip->extractTo($this->path . 'temp/');
          $zip->close();
          // Delete zip file after its contents have been extracted
          unlink($this->path . $model->zip->name);

          $it = new RecursiveDirectoryIterator($this->path . 'temp/');
                               
          foreach(new RecursiveIteratorIterator($it) as $file)
          {
            $mime = CFileHelper::getMimeType($file);
            $filename = basename($file);
            $random = uniqid();
            // If mime type of file is allowed, save image and model
            if(in_array($mime, $this->allowedMimeTypes)){
                if(rename($file, $this->path . $random . '_' . $filename)){
                    $model = new Emoticon;
                    $model->file_name = $random . '_' . $filename;
                    $model->set_id = $set_id;
                    $model->save();
                }
            }
          }

        } else {
            Yii::app()->user->setFlash('error', 'Could not unzip file');
        }     
    }

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'emoticon/index' page.
	 */
	public function actionCreate()
	{
		$model = new Emoticon('create');

        // Set the model's set id if given in URL
        if(isset($_GET['set_id']))
            $model->set_id = (int)$_GET['set_id'];
        
        if(isset($_POST['Emoticon'])){
            // Get all files as CUploadedFiles
            $files = CUploadedFile::getInstancesByName('Emoticon');

            // Create new set if set name field is filled
            if($_POST['Emoticon']['set_name'] != ''){

                $set = new EmoticonSet;
                $set->name = $_POST['Emoticon']['set_name'];
                $set->save();

                // Grab id and folder of newly created set
                $set_id = $set->id;
                $folder = $set->slug;

            }elseif(isset($_POST['Emoticon']['set_id'])){
                $set_id = (int)$_POST['Emoticon']['set_id'];   
            }

            // If we don't already know the folder, i.e. set wasn't just created
            if(!isset($folder)){
                // Find the set and grab its folder name
                $folder = EmoticonSet::model()->folder()->findByPk($set_id)->slug;
            }

            // Set upload path
            $this->path = $this->uploadPath . $folder . '/';

            // Create upload folder if it doesn't already exist
            if(!file_exists($this->path))
                mkdir($this->path);

            // If there are files, create the models
            if(count($files) > 0){
                $this->createModels($files, $set_id);
            }

            $this->redirect(array('emoticonSet/update', 'id' => $set_id));
        }
        else{  
            $this->render('create',array(
                'model'=>$model,
            ));
        }
        
	}

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        if(isset($_POST[$this->modelClass]))
        {
            $model->attributes = $_POST[$this->modelClass];
            $file = CUploadedFile::getInstance($model, 'image');

            // If user wants to replace image with a new one
            if(isset($file)){
                // Get the uplaod path
                $folder = EmoticonSet::model()->folder()->findByPk($model->set_id)->slug;
                $this->path = $this->uploadPath . $folder . '/';

                // Check the file's mime type  
                $mime = CFileHelper::getMimeType($file->tempName);

                // If mime type of file is allowed, save image and model
                if(in_array($mime, $this->allowedMimeTypes)){
                    $model->file_name = uniqid() . '_' . $file->name;
                    $file->saveAs($this->path . $model->file_name);
                }
            }

            if($model->save())
                $this->redirect(array('view', 'id'=>$model->id));
        }

        $this->render('update',array(
            'model'=>$model,
        ));
    }

    /**
     * Load all emoticons via ajax
     * 
     * This action is called from the EmoticonWidget when it's in extend mode
     * The idea is to switch back and forth between one set and all emoticons
     * The $set_id variable holds the id of the original set, so we can switch back to it
     */
    public function actionLoad()
    {
        $set_id = isset($_GET['set_id']) ? (int)$_GET['set_id'] : null;

        if(empty($set_id)){
            $emoticons = Emoticon::model()->visibleSets()->findAll();
        }else{
            $emoticons = Emoticon::model()->visibleSets()->findAll('set_id != :set_id', array(
                ':set_id' => $set_id,
            ));   
        }

        $this->renderPartial('_load', array(
            'emoticons' => $emoticons,
            'publicPath' => $this->publicPath,
            'set_id' => $set_id,
        ));
    }

}
