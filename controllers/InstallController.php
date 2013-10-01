<?php

class InstallController extends Controller
{
    public $layout;
    
    public function init()
    {
      $this->layout = $this->module->layout;
    }
    
    public function actionIndex()
    {
        $model = new InstallForm;
        $model->exampleSet = 1;
        $model->emoticonTable = 'emoticon';
        $model->emoticonSetTable = 'emoticon_set';

        if(isset($_POST['InstallForm']))
        {
            $model->attributes = $_POST['InstallForm'];

            if($model->validate())
            {
                $this->install($model);
                return;
            }
        }
        $this->render('index', array('model'=>$model));

    }

    public function install($model)
    {
        if(!isset($this->module->install) || (!$this->module->install)){
          throw new CException("This module must be in install mode for the script to run. Add 'install' => true to the config");
        }

        $db = Yii::app()->db;

        if(!$db){
          throw new CException('There was an error connnecting to your database. Please check your database config.');
        }
        
        $transaction = $db->beginTransaction();  
        
        // If using mysql
        if(strpos(Yii::app()->db->connectionString, 'mysql') !== false){
          // Create emoticon table
          $sql = "CREATE TABLE IF NOT EXISTS `".Yii::app()->db->tablePrefix.$model->emoticonTable."` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `set_id` int(11) NOT NULL,
            `file_name` varchar(100) NOT NULL,
            `code` varchar(25) NOT NULL,
            `alt_text` varchar(100) NOT NULL,
            `width` int(11) NOT NULL,
            `height` int(11) NOT NULL,
            `position` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_set_id` (`set_id`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=latin1";
          $db->createCommand($sql)->execute();
  
          // Create emoticon set table
          $sql = "CREATE TABLE IF NOT EXISTS `".Yii::app()->db->tablePrefix.$model->emoticonSetTable."` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(50) NOT NULL,
            `slug` varchar(50) NOT NULL,
            `position` int(11) NOT NULL,
            `visible` tinyint(1) NOT NULL DEFAULT '1',
            `prefix` varchar(25) NOT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=latin1";
          $db->createCommand($sql)->execute();
        // If using sqlite
        }elseif(strpos(Yii::app()->db->connectionString, 'sqlite') !== false){
        
                // Create emoticon table
    
          $sql = "CREATE TABLE `".Yii::app()->db->tablePrefix.$model->emoticonTable."`
            (
              id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
              set_id INTEGER NOT NULL,
              file_name VARCHAR(128) NOT NULL,
              code VARCHAR(16) NOT NULL,
              alt_text VARCHAR(128) NOT NULL,
              width INTEGER NOT NULL,
              height INTEGER NOT NULL,
              position INTEGER NOT NULL);";
            
          $db->createCommand($sql)->execute();
  
          // Create emoticon set table
          $sql = "CREATE TABLE `".Yii::app()->db->tablePrefix.$model->emoticonSetTable."`
          (
            id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(64) NOT NULL,
            slug VARCHAR(64) NOT NULL,
            position INTEGER NOT NULL,
            visible INTEGER NOT NULL);";
          $db->createCommand($sql)->execute();
          
        }else{
          throw new CException('You are using an unsupported PDO driver. Only MySQL and SQLite are supported');  
        }

        
      // Copy smilies images
      $oldPath = Yii::app()->assetManager->getPublishedPath(Yii::getPathOfAlias('emoticons.assets'));
      $oldPath .= '/img/smilies';

      $newPath = $this->module->uploadPath . 'smilies ';
      
      if(CFileHelper::copyDirectory($oldPath, $newPath)){
  
        $sql = "INSERT INTO `".Yii::app()->db->tablePrefix.$model->emoticonSetTable."` (`id`, `name`, `slug`, `position`, `visible`, `prefix`) VALUES
  (1, 'Smilies', 'smilies', 0, 1, '');";
        $db->createCommand($sql)->execute();
        
        $sql = "INSERT INTO `".Yii::app()->db->tablePrefix.$model->emoticonTable."` (`id`, `set_id`, `file_name`, `code`, `alt_text`, `width`, `height`, `position`) VALUES
          (1, 1, '51363067ab748_wink.gif', ':wink:', 'wink', 20, 24, 0),
          (2, 1, '51363067b3470_laugh.gif', ':laugh:', 'laugh', 20, 24, 0),
          (3, 1, '51363067b8343_thumbsup.gif', ':thumbsup:', 'thumbsup', 26, 24, 0),
          (4, 1, '51363067bd5da_tongue.gif', ':tongue:', 'tongue', 20, 25, 0),
          (5, 1, '51363067c2948_nah.gif', ':nah:', 'nah', 36, 26, 0),
          (6, 1, '51363067c7cd1_dance.gif', ':dance:', 'dance', 42, 25, 0),
          (7, 1, '51363067cccf8_sad2.gif', ':sad2:', 'sad2', 20, 24, 0),
          (8, 1, '51363067d21c9_shrug.gif', ':shrug:', 'shrug', 32, 20, 0),
          (9, 1, '51363067d76f0_crying.gif', ':crying:', 'crying', 40, 18, 0),
          (10, 1, '51363067dc92d_blush.gif', ':blush:', 'blush', 20, 20, 0),
          (11, 1, '51363067e1e22_sunglasses.gif', ':sunglasses:', 'sunglasses', 28, 22, 0),
          (12, 1, '51363067e7130_wacko.gif', ':wacko:', 'wacko', 20, 20, 0),
          (13, 1, '51363067ec889_happy.gif', ':happy:', 'happy', 42, 27, 0),
          (14, 1, '51363067f23b4_shock.gif', ':shock:', 'shock', 20, 20, 0),
          (15, 1, '5136306803b9d_eyeroll.gif', ':eyeroll:', 'eyeroll', 20, 20, 0),
          (16, 1, '5136306808f1e_scared.gif', ':scared:', 'scared', 20, 20, 0);";  
        $db->createCommand($sql)->execute();
        
      }

      $transaction->commit();
      
      
    }


}
