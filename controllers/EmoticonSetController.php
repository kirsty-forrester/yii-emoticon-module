<?php

class EmoticonSetController extends EController
{
	/**
     * Methods in this class:
     *
     * actionCreate()
     * actonUpdate()
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

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new EmoticonSet('create');

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['EmoticonSet']))
		{
			$model->attributes=$_POST['EmoticonSet'];

			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	public function actionUpdate($id)
	{
		$set = $this->loadModel($id);

        if(isset($_POST['EmoticonSet']))
        {
            $set->attributes = $_POST['EmoticonSet'];
            if($set->save()){
            	if(Yii::app()->request->isAjaxRequest)
            		echo $set->name;
            	else
            		$this->redirect(array('update', 'id' => $id));
            }  
        }else{

	        $model = new Emoticon('search');
	        $model->unsetAttributes();  // clear any default values
	        $model->set_id = $id;
	        if(isset($_GET['Emoticon']))
	            $model->attributes = $_GET['Emoticon'];

	        $this->render('update',array(
	            'model'=> $model,
	            'set' => $set,
	        ));
    	}
	}

	/**
	 * Loads partial view of emoticon set(s)
	 */
	public function actionLoad()
	{
		if(isset($_GET['id'])){
			$id = (int)$_GET['id'];
			$emoticons = $this->loadModel($id)->emoticons;
			$showLess = false;
		}else{
			throw new CHttpException(404,'The requested set does not exist');
		}

		$this->renderPartial('_load', array(
			'emoticons' => $emoticons,
			'publicPath' => $this->module->publicPath,
			'containerClass' => 'emoticon-container',
		));	
	}


}
