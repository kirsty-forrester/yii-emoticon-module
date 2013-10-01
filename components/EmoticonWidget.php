<?php

/**
 * EmoticonWidget class file.
 * Widget for displaying emoticon images which can be clicked to insert either HTML 
 * or representative code, e.g. :happy: into a textarea or rich text editor.
 * 
 * @author Kirsty Forrester <kirstyaforrester@gmail.com>
 * @copyright Copyright &copy; Kirsty Forrester 2013
 * @license GPL version 3.0
 */

class EmoticonWidget extends CWidget
{
    /**
     * Variable to store CModule instance
     * @var CModule
     */
    public $module;

    /**
     * id of the textarea that the emoticon codes should be inserted into
     * @var string
     */
    public $textareaId = '';

    /*   The view of the widget. Options:
     *   Single: will show one set based on $this->set. If not set, will use set with most smileys
     *   All: show all available emoticons together
     *   Tabbed (default): will show every set with over one smiley on its own tab
     *   Expand: will show $this->set then display "Show more" link to display more
     *   @var string
     */
    public $display = 'tabbed';

    /**
     * The view file used to display the emoticons
     * @var string
     */
    public $viewFile = 'single';

    /**
     * The slug of the smiley set to be shown
     * Used when $view is set to 'single' or 'expand'
     * Will default to the first set (by order) if not specified
     * @var string
     */
    public $set;

    /**
     * The model of the selected emoticon set 
     */
    public $emoticonSet;

    /**
     * Array used for storing tabs when view is set to 'tabbed'
     * @var array
     */
    public $tabs = array();

    /**
     * Whether to insert the HTML for the smiley image rather than the code
     * @var boolean
     */
    public $html = false;

    /**
     * Whether widget should be displayed as a redactor plugin or not
     * If true, a happy face icon will appear on the toolbar.
     * When clicked, a modal will be opened with the emoticon widget inside
     * @var boolean
     */
    public $redactor = false;

    /**
     * The URL to load the rest of the emoticons when in 'expand' mode
     * @var mixed
     */
    public $ajaxUrl;

    /**
     * Valid display options
     * @var array
     */
    public $displayOptions = array('single', 'all', 'tabbed', 'expand');

    /**
     * HTML options for the widget's surrounding div
     * @var array
     */
    public $htmlOptions = array();

    /**
     * Array of ActiveRecord Emoticon models
     * @var array
     */
    public $emoticons = array();

    /**
     * Array of ActiveRecord EmoticonSet models
     * @var array
     */
    public $emoticonSets = array();

    /**
     * Load emoticon models from database
     */
    public function getEmoticons()
    {
        // If user has defined emoticons manually, return
        if(!empty($this->emoticons))
            return;

        // If display is single or expand, load the first set. Otherwise load all emoticons
        if($this->display == 'single' || ($this->display == 'expand')){
            
            $this->emoticonSet = EmoticonSet::model()->with('emoticons')->findByAttributes(array('slug' => $this->set));

            if(!isset($this->emoticonSet)){
                $this->emoticonSet = EmoticonSet::model()->visible()->default()->with('emoticons')->find();
            }

            $this->emoticons = $this->emoticonSet->emoticons;

        }elseif($this->display == 'all'){

            $this->emoticons = Emoticon::model()->visibleSets()->findAll();
        }   

    }

    /**
     * For tabbed mode: create an array which holds the tabs and their contents
     */
    public function createTabs()
    {
        $emoticonSets = EmoticonSet::model()->visible()->ordered()->with('emoticons:relationOrdered')->findAll();
        $module = $this->module->id;
        $counter = 0;

        foreach($emoticonSets as $set){

            $emoticons = $set->emoticons;

            if(count($emoticons) > 0){
                // Preload contents of first tab only
                if($counter == 0){
                    $this->tabs[$set->slug]['view'] = 'emoticons.components.views.single';
                    $this->tabs[$set->slug]['title'] = ucwords($set->name);
                    $this->tabs[$set->slug]['data'] = array(
                        'emoticons' => $emoticons,
                        'publicPath' => $this->module->publicPath,
                        'display' => $this->display,
                    );
                // Load other tabs via ajax
                }else{
                    $this->tabs[$set->slug] = array(
                        'title' => ucwords($set->name),
                        'ajax' => true,
                        'url' => Yii::app()->createUrl($module . '/emoticonSet/load', array(
                            'id' => $set->id,
                        )),

                    );
                }

                $counter++;   
            }
            
        }
    }

    /**
     * Initialize view variable and tabs if in tabbed mode
     */
    public function setView()
    {
        // If selected display option is invalid, use 'tabbed' as default
        if(!in_array($this->display, $this->displayOptions))
            $this->display = 'tabbed';   

        // Set view and tabs for tabbed view
        if($this->display == 'tabbed'){
            $this->createTabs();
            $this->viewFile = 'tabbed';
        }

        // Set view and ajaxUrl for expand view
        if($this->display == 'expand'){
            $this->ajaxUrl = Yii::app()->createUrl('emoticons/emoticon/load', array(
                'set_id' => $this->emoticonSet->id,
            ));
            $this->viewFile = 'expand';
        }
    }

    /**
     * Set default HTML options and merge with user defined options
     */
    public function setHtmlOptions()
    {
        $htmlOptions = array();

        // Hide widget if using redactor plugin
        if($this->redactor){
            $htmlOptions['class'] = 'hidden';
        }
        // Add default html id if not set
        if(!isset($this->htmlOptions['id'])){
            $htmlOptions['id'] = 'emoticon-widget';
        }

        // Merge html options
        $this->htmlOptions = CMap::mergeArray($this->htmlOptions, $htmlOptions);
    }

    /**
     * Initialize widget
     */
    public function init()
    {
        $this->module = Yii::app()->getModule('emoticons');

        $this->getEmoticons();
        $this->setView();
        $this->setHtmlOptions();

        /**
         * Set variables for use in js files
         * Functions for adding emoticon to fields are in emoticons.js
         */
        Yii::app()->clientScript->registerScript('setJSvariables', "
            EmoticonsModule = {};
            EmoticonsModule.textareaId = ".json_encode($this->textareaId).";
            EmoticonsModule.insertHtml = ".json_encode($this->html).";

            // If textarea id isn't set, use first textarea on page
            if(EmoticonsModule.textareaId.length < 1){
                EmoticonsModule.textareaId = $('body').find('textarea').attr('id');
            }
        ", CClientScript::POS_END);

        parent::init();
    }

    /**
     * Run widget
     */
    public function run()
    { 
        $this->render('emoticonWidget', array(
            'emoticons' => $this->emoticons,
            'textareaId' => $this->textareaId,
            'publicPath' => $this->module->publicPath,
            'tabs' => $this->tabs,
            'display' => $this->display,
            'ajaxUrl' => $this->ajaxUrl,
        ));

    }

}