function updateUIButtons(container)
{
    container.find('input[type="button"], input[type="submit"], input[type="reset"], button, a.button ').each(function(i,o){
            var object = $(o);

            if(object.is('a')) {
                var code =
                    '<div class="button-content">' +
                        '<div class="button-icon"></div>'+
                        '<div class="button-label">' +
                            object.text() + object.val()+
                        '</div><div style="clear: both"></div>'+
                    '</div>';
                object.html(code);
            } else {
                var originalClass = object.attr('class');
                if(originalClass == undefined)
                    originalClass = '';

                var code = '<div class="button '+ originalClass/*.replace('button','')*/ +'">' +
                    '<div class="button-content">' +
                        '<div class="button-icon"></div>'+
                        '<div class="button-label">' +
                            object.text() + object.val()+
                        '</div><div style="clear: both"></div> '+
                    '</div>'+
                '</div>';
                object.hide();
                object.after(code);
            }

        });

        container.find('.button').each(function(i,o){
            var object = $(o);

            object.hover(function(){
                object.addClass('button-hover');
                object.removeClass('button-active');
            }, function(){
                object.removeClass('button-hover');
                object.removeClass('button-active');
            });

            object.mousedown(function(){
                object.addClass('button-active');
            });

            object.click(function(){
                var prev = $(this).prev();
                prev.click();
            });
        });
}
$(function(){
    updateUIButtons($('body'));
});