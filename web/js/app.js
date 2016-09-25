$(document).ready(function () {

    $('.ui.dropdown')
        .dropdown()
    ;
    $('.ui.rating')
        .rating({
            onRate: function (value) {
                $('input[name="_tutoriel[difficulty]"]').val(value);
            }
        })
    ;
    $('.alert .fa-close')
        .on('click', function () {
            $(this)
                .closest('.alert')
                .addClass('animated fadeOutRight')
                .delay(2000)
	            .queue(function(){
	            	$(this).remove();
	            })
            ;
        })
    ;

});