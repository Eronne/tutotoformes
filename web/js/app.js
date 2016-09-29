var flashDuration = 6000;

$(document).ready(function () {
	
	var doc = $(document);
	var header = $('nav.main-nav');
	var relativeUrl = getUrlParts(document.documentURI).pathname;
	
	
	$('.alert .fa-close, .notification .fa-close')
		.on('click', function () {
			$(this)
				.closest('.alert')
				.addClass('animated fadeOutRight')
				.delay(1000)
				.queue(function () {
					$(this).remove();
				})
			;
		})
	;
	
	if ($('.notification').length > 0) {
		window.setTimeout(function () {
			$('.notification').addClass('animated fadeOutRight');
		}, flashDuration);
		window.setTimeout(function () {
			$('.flashbags').remove();
		}, flashDuration + 1000);
	}
	
	
	if (relativeUrl === "/") {
		$(document)
			.on('scroll', function (e) {
				console.log(doc.scrollTop());
				if (doc.scrollTop() === 0) {
					header.addClass('transparent');
				} else if (doc.scrollTop() > 0 && doc.scrollTop() < 100) {
					header.removeClass('transparent');
				}
			})
		;
	}
	
	if (doc.scrollTop() === 0 && relativeUrl === "/") {
		header.addClass('transparent');
	} else if (doc.scrollTop() > 0 && doc.scrollTop() < 100 && relativeUrl === "/") {
		header.removeClass('transparent');
	}
	
	if ($('textarea').length > 0) {
		tinymce.init({
			selector: 'textarea',
			theme: 'modern',
			language: 'fr_FR',
			plugins: [
				'advlist autolink lists link image charmap print preview hr anchor pagebreak',
				'searchreplace wordcount visualblocks visualchars code codesample fullscreen',
				'insertdatetime media nonbreaking save table contextmenu directionality',
				'emoticons template paste textcolor colorpicker textpattern imagetools'
			],
			codesample_languages: [
				{text: 'HTML/XML', value: 'markup'},
				{text: 'JavaScript', value: 'javascript'},
				{text: 'CSS', value: 'css'},
				{text: 'PHP', value: 'php'},
				{text: 'Java', value: 'java'},
				{text: 'SCSS', value: 'scss'},
				{text: 'Git', value: 'git'},
				{text: 'JSON', value: 'json'},
				{text: 'C#', value: 'csharp'},
				{text: 'Twig', value: 'twig'}
			],
			style_formats: [
				{title: 'H1', block: 'h1'},
				{title: 'H2', block: 'h2'},
				{title: 'H3', block: 'h3'},
				{title: 'H4', block: 'h4'},
				{title: 'H5', block: 'h5'},
				{title: 'H6', block: 'h6'},
				{title: 'Paragraphe', block: 'p'},
				{title: 'Pre', block: 'pre'},
				{title: 'Span', block: 'span'},
				{
					title: 'Message', block: 'div', styles: {
					padding: '20px', background: '#2aa0cc', color: 'white', border: '1px solid #2AA0CC'
				}
				}
			],
			toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
			toolbar2: 'print preview media | forecolor backcolor emoticons codesample',
			content_css: ['/styles/mce.css']
		});
	}
	var test = "qsd";
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