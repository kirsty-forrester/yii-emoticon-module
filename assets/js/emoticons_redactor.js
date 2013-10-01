if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

RedactorPlugins.emoticons = {

    init: function()
    {
        var widget;
        var callback = $.proxy(function()
        {
            // Unhide widget div
            $('#redactor_modal #emoticon-widget').removeClass('hidden');

            // Save cursor position
            this.saveSelection();

            // Add click event which triggers insertion of html
            $(document).on('click', '#redactor_modal .add-emoticon', $.proxy(function(event){  
                this.insertFromMyModal(event);
                return false;
            }, this));

        }, this);
    
        this.addBtn('emoticons', 'Emoticons', function(obj)
        {
            // If widget exists, remove existing events then append to body
            if(widget){
                $(document).undelegate('#redactor_modal .add-emoticon', 'click');
                widget.appendTo('body');
            }
            // Create modal
            obj.modalInit('', '#emoticon-widget', 500, callback);
            // Remove other widget div and save for later insertion
            widget = $('#emoticon-widget').not('#redactor_modal #emoticon-widget').remove();
        }); 
        
        this.addBtnSeparatorBefore('emoticons');
            
    },
    insertFromMyModal: function(event)
    {
        // Restore cursor position
        this.restoreSelection();

        if(EmoticonsModule.insertHtml){
            var code = this.outerHTML($(event.currentTarget).removeAttr('class data-code'));
        }else{
            var code = $(event.currentTarget).data('code');
        }
 
        this.insertHtml(code);
        this.modalClose();
    }
}