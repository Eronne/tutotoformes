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
    $('.message .close')
        .on('click', function () {
            $(this)
                .closest('.message')
                .transition('fade')
            ;
        })
    ;

});