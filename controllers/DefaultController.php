<?php

class DefaultController extends Controller
{
    public function actionIndex()
	{  
        $emoticonSets = EmoticonSet::model()->with('emoticons')->search();
        $emoticonSets->pagination->pageSize = 9;

        $emoticon = Emoticon::model()->sets()->findAll();
        $this->render('index', array(
            'emoticonSets' => $emoticonSets,
        ));
	}
}