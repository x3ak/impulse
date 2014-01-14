$(function(){
    $.openedDialogs = [];
    $('a.modal-dialog-link').click(function(event){
        event.preventDefault();
        $('body').append('<div class="modal-dialog" id="modal-dialog-'+$.openedDialogs.length + '">' +
                '<div class="modal-dialog-close-x"></div>'+
                '<div class="modal-dialog-content">Loading...</div>' +
                '</div>');
        $('#modal-dialog-shadow').show();

        var dialogObject = $('#modal-dialog-'+$.openedDialogs.length);

        if($(this).is('.dialog-size-s')) {
            dialogObject.css({
                minWidth: 400,
                minHeight: 100
            });
        }

        if($(this).is('.dialog-size-l')) {
            dialogObject.css({
                minWidth: 800,
                minHeight: 200
            });
        }

        var ww = $(window).width();
        var wh = $(window).height();
        var dw = dialogObject.width();
        var dh = dialogObject.height();

        dialogObject.css({
            left: (ww - dw) / 2,
            top: (wh - dh) / 6
        });

        dialogObject.data('dialog', dialogObject);

        $.openedDialogs.push(dialogObject);

        $.get($(this).attr('href'), function(data){
            dialogObject.find('.modal-dialog-content').html(data);
            updateUIButtons(dialogObject);
        });

    });

    $('body').append('<div id="modal-dialog-shadow" style="display: none;"></div>');

    $(window).keypress(function(event){
        //TODO: close on esc key
    });

    $('body').delegate('.modal-dialog-close-x', 'click',function(event){
         $(this).closest('.modal-dialog').fadeOut('fast');
         $('#modal-dialog-shadow').fadeOut('fast');
    });
});