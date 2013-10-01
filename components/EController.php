<?php

class EController extends Controller
{

    public $modelClass;
    public $admins;

    public function init()
    {
        $this->admins = !empty(Yii::app()->controller->module->admins) ? Yii::app()->controller->module->admins : array('admin');
        $this->modelClass = ucwords($this->id);
        //$this->layout = $this->module->layout;
    }

    public function beforeRender($view)
    {
      
      Yii::app()->clientScript->registerScript('emoticonNamespace', "EmoticonsModule = {};", CClientScript::POS_HEAD);
      
      return parent::beforeRender($view);
    }

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(

            array('allow',
                'users'=> $this->admins,
            ),
            array('allow',
                'actions' => array('load'),
                'users' => array('*'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    
    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view',array(
            'model'=>$this->loadModel($id),
        ));
    }

    /**
     * Manages all models.
     */
    public function actionIndex()
    {
        $model = new $this->modelClass('search');
        $model->unsetAttributes();  // clear any default values
        
        if(isset($_GET[$this->modelClass]))
            $model->attributes = $_GET[$this->modelClass];

        $this->render('index',array(
            'model'=>$model,
        ));
    }
  
    /**
     * Reorder 
     */
    public function actionOrder()
    {
        $direction = $_GET['direction'];
        $id = (int)$_GET['id'];
        $model = $this->loadModel($id);
        $class = $this->modelClass;
        
      
        if(!empty($direction) && ($direction == 'up' || $direction == 'down')){

            $model->setNewPosition($direction);
 
            $sets = $class::model()->findAll(array('condition'=>'t.id !='.$model->id)); 

            if($model->position > 0 && $model->position <= count($sets)+1){

                $model->update('position');
                $i = 1;

                foreach($sets as $set){
                    if($i != $model->position){
                        $set->position = $i;
                    }else{
                        $set->position = $i + 1;
                        $i++;
                    }
                            
                    $set->update('position');
                    $i++;
                }
            }
        }
                
        else{
            $this->redirect(array('index'));
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

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST[$this->modelClass]))
        {
            $model->attributes = $_POST[$this->modelClass];

            if($model->save())
                $this->redirect(array('view','id'=>$model->id));
        }

        $this->render('update',array(
            'model'=>$model,
        ));
    }

    /**
     * Updates a column based on parameters sent via ajax
     */
    public function actionUpdateColumn()
    {
        $id = (int)$_POST['id'];
        $class = $_POST['class'];
        $name = $_POST['name'];
        $value = $_POST['value'];

        $model = $class::model()->findByPk($id);
        $p = new CHtmlPurifier();
        $model->{$name} = $p->purify($value);
        $model->update();
    }

    /**
     * Updates one value in an array of models
     */
    public function actionUpdateSelected()
    {
        $selected = $_POST['selected'];
        $attribute = $_POST['attribute'];
        $value = $_POST['value'];

        $models = Emoticon::model()->findAllByPk($selected);
        $p = new CHtmlPurifier();

        // Have to do each model individually to trigger beforeSave event
        foreach($models as $model) {
            $model->{$attribute} = $p->purify($value);
            $model->update($attribute);
        }
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }

    /**
     * Deletes an array of models
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     */
    public function actionDeleteSelected()
    {
        $selected = $_POST['selected'];
        $class = $this->modelClass;

        $models = $class::model()->findAllByPk($selected);

        // Have to do each model individually to trigger beforeDelete event
        foreach($models as $model) {
            $model->delete();
        }
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='emoticon-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $class = $this->modelClass;
        $model = $class::model()->findByPk($id);
        if($model === null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

}