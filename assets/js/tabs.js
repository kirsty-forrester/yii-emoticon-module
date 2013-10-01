/**
 * jQuery Yii plugin file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2010 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

;(function($) {

    $.extend($.fn, {
        yiitab: function() {

            function activate(id, link) {

                var $tab=$(id);
                var $container=$tab.parent();
                $container.find('>ul a').removeClass('active');
                link.addClass('active');
                $container.children('div').hide();
                $tab.show();
            }

            $(document).on('click', 'ul.tabs li a', function(event){
            //this.find('>ul a').click(function(event) {

                event.preventDefault();

                var ajax = $(this).data('ajax');
                var href = $(this).attr('href');
                var pos = href.indexOf('#');
                var link = $(this);

                if(ajax != undefined){
                    var div = $(this).data('div');

                    var tab = $('#' + div);
 
                    if($.trim($('#' + div).html()).length > 0){
                        activate('#' + div, link);
                    }else{
                        tab.load(href, function(){
                          activate('#' + div, link); 
                        });
                    }

                }else{
                    activate(href, link);
                    if(pos==0 || (pos>0 && (window.location.pathname=='' || window.location.pathname==href.substring(0,pos))))
                        return false;
                }
            });

            // activate a tab based on the current anchor
            var url = decodeURI(window.location);
            var pos = url.indexOf("#");
            if (pos >= 0) {
                var id = url.substring(pos);
                var link = this.find('>ul a[href="'+id+'"]');
                if (link.length > 0) {
                    activate(id, link);
                    return;
                }
            }
        }
    });

})(jQuery);
