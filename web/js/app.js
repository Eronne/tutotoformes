var flashDuration = 6000;

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
                .delay(1000)
	            .queue(function(){
	            	$(this).remove();
	            })
            ;
        })
    ;

    if($('.alert:not(.persistent)').length > 0) {
        window.setTimeout(function () {
            $('.alert').addClass('animated fadeOutRight');
        }, flashDuration);
        window.setTimeout(function () {
            $('.flashbags').remove();
        }, flashDuration + 1000);
    }

    var doc = $(document);
    var header = $('nav.main-nav');
    var relativeUrl = getUrlParts(document.documentURI).pathname;
    if(relativeUrl === "/"){
        $(document)
            .on('scroll', function(e){
                console.log(doc.scrollTop());
                if(doc.scrollTop() === 0){
                    header.addClass('transparent');
                    console.log("test à 0");
                } else if(doc.scrollTop() > 0 && doc.scrollTop() < 100){
                    header.removeClass('transparent');
                    console.log("test à + 0");

                }
            })
        ;
    }

});

function getUrlParts(url) {
    var a = document.createElement('a');
    a.href = url;

    return {
        href: a.href,
        host: a.host,
        hostname: a.hostname,
        port: a.port,
        pathname: a.pathname,
        protocol: a.protocol,
        hash: a.hash,
        search: a.search
    };
}