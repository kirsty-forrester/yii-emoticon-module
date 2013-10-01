// Functions

(function($){
   $.fn.setCursorPosition = function(pos) {
    if ($(this).get(0).setSelectionRange) {
      $(this).get(0).setSelectionRange(pos, pos);
    } else if ($(this).get(0).createTextRange) {
      var range = $(this).get(0).createTextRange();
      range.collapse(true);
      range.moveEnd('character', pos);
      range.moveStart('character', pos);
      range.select();
    }
   };
}(jQuery));

(function($){
   $.fn.outerHTML = function() {
    return jQuery('<div />').append(this.eq(0).clone()).html();
   };
}(jQuery));

(function($){
   $.fn.insertEmoticon = function(code) {
    if($(this).is(':visible')){
      $(this).val(code).focus().setCursorPosition($(this).val().length);
    }else{
      $(this).val(code);  
    }   
   };
}(jQuery));

(function($){
   $.fn.addEmoticonCode = function(event) {

    if($(this).length == 0)
        return;

    var currentText = $(this).val();
    var code = $(event.currentTarget).data('code');
    currentText += ' ' + code + ' ';

    if($(this).is(':visible')){
      $(this).val(currentText).focus().setCursorPosition($(this).val().length);
    }else{
      $(this).val(currentText);  
    }

    
   };
}(jQuery));

(function($){
   $.fn.addEmoticonHtml = function(event) {
      var code = $(event.currentTarget).clone().removeAttr('class data-code').outerHTML();

      $(this).insertHtml(code);
   };
}(jQuery));

(function($){
   $.fn.getSelected = function() {
    var selected = new Array();
    var numSelected = 0;
      
    $('#' + $(this).attr('id') + ' input[type=checkbox]:checked').each( function() {
      selected[numSelected] = $(this).val();
      numSelected++;        
    });

    return selected;
   };
}(jQuery));

// Event handlers
$(function(){

 
  $(document).on('click', '.add-emoticon:not(#redactor_modal .add-emoticon)', function(event){
      if(EmoticonsModule.insertHtml)
          $('#' + EmoticonsModule.textareaId).addEmoticonHtml(event);
      else
         $('#' + EmoticonsModule.textareaId).addEmoticonCode(event); 
      return false;
  });


  $(document).on('click', '.delete-set', function(){
      if(confirm('Are you sure you want to delete this emoticon set?')){
          var id = $(this).data('id');
          var url = $(this).attr('href');

          $.get(url, function() {
              $.fn.yiiListView.update('emoticon-set-list');
          });
      }
      return false;
  });

  $('.delete-selected').click(function(){

      if(confirm('Are you sure you want to delete these?')){
        var gridId = $('.grid-view').attr('id');
        var selected = $('#' + gridId).getSelected();

        $.ajax({
            url: EmoticonsModule.deleteSelectedUrl,
            type: 'post',
            data: {selected: selected},
            success: function(data) {
                $.fn.yiiGridView.update(gridId);
                $('#' + gridId).find('input[type=checkbox]:checked').removeAttr('checked');
            }    
        });
      }
      
    });

    $(document).on('change', '.update-list-value', function(){

      var self = $(this);
      var listId = $('.list-view').attr('id');
  
      $.ajax({
          url: self.data('url'),
          type: 'post',
          data: {
            id: self.data('id'),
            name: self.data('name'),
            value: self.val(),
            class: self.data('class')
          },
          success: function(data) {
              self.addClass('field-success').delay(750).queue(function(){
                  self.removeClass('field-success').blur();
              });
          }    
      });
      
    });

    $('#move_set_id').change(function(){
        var gridId = $('.grid-view').attr('id');
        var selected = $('#' + gridId).getSelected();
        var value = $(this).val();

        $.ajax({
            url: EmoticonsModule.updateSetIdUrl,
            type: 'post',
            data: {attribute: 'set_id', selected: selected, value: value},
            success: function(data) {
                $.fn.yiiGridView.update(gridId);
                $('#' + gridId).find('input[type=checkbox]:checked').removeAttr('checked');
            }    
        });

    });

});