let flashDuration = 6000;

$(document).ready(function () {
	
	let doc = $(this);
	let header = $('nav.main-nav');
	let relativeUrl = getUrlParts(document.documentURI).pathname;
	
	let sidebar = $('.sidebar');
	
	let leftNav = $('.navigation .left a');
	let rightNav = $('.navigation .right a');
	let markButton = $('#markButton');
	
	let forgotButton = $('button#forgotPassword');
	
	
	$(forgotButton).on('click', () => {
		openPopup();
	});
	
	sidebar.stick_in_parent({offset_top: 80});
	sidebar.children('.page:not(".active")').on('mouseover', function () {
		let page = $(this);
		page.children('i.fa.fa-angle-right').addClass('unfolded');
		let subpages = page.children('.subpages');
		subpages.addClass('visible');
	}).on('mouseleave', function () {
		let page = $(this);
		page.children('i.fa.fa-angle-right').removeClass('unfolded');
		let subpages = page.children('.subpages');
		subpages.removeClass('visible');
	});
	
	$('.page.active .subpage a').on('click', function (e) {
		e.stopPropagation();
		e.preventDefault();
		let link = $(this);
		$('html, body').animate({
			scrollTop: $('h2#' + link.data('to')).offset().top - 100
		}, 400);
	});
	
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
				{
					title: 'Headers', items: [
					{title: 'Header 1', format: 'h1'},
					{title: 'Header 2', format: 'h2'},
					{title: 'Header 3', format: 'h3'},
					{title: 'Header 4', format: 'h4'},
					{title: 'Header 5', format: 'h5'},
					{title: 'Header 6', format: 'h6'}
				]
				},
				{
					title: 'Inline', items: [
					{title: 'Bold', icon: 'bold', format: 'bold'},
					{title: 'Italic', icon: 'italic', format: 'italic'},
					{title: 'Underline', icon: 'underline', format: 'underline'},
					{title: 'Strikethrough', icon: 'strikethrough', format: 'strikethrough'},
					{title: 'Superscript', icon: 'superscript', format: 'superscript'},
					{title: 'Subscript', icon: 'subscript', format: 'subscript'},
					{title: 'Code', icon: 'code', format: 'code'}
				]
				},
				{
					title: 'Blocks', items: [
					{title: 'Paragraph', format: 'p'},
					{title: 'Blockquote', format: 'blockquote'},
					{title: 'Div', format: 'div'},
					{title: 'Pre', format: 'pre'}
				]
				},
				{
					title: 'Alignment', items: [
					{title: 'Left', icon: 'alignleft', format: 'alignleft'},
					{title: 'Center', icon: 'aligncenter', format: 'aligncenter'},
					{title: 'Right', icon: 'alignright', format: 'alignright'},
					{title: 'Justify', icon: 'alignjustify', format: 'alignjustify'}
				]
				},
				{
					title: 'Alerts', items: [
					{
						title: 'Message info',
						block: 'div',
						styles: {
							padding: '20px',
							background: '#2aa0cc',
							color: 'white',
							border: '3px solid #61c1e4',
							margin: '10px 0'
						}
					},
					{
						title: 'Message success',
						block: 'div',
						styles: {
							padding: '20px',
							background: '#56b965',
							color: 'white',
							border: '3px solid #84d08f',
							margin: '10px 0'
						}
					},
					{
						title: 'Message error',
						block: 'div',
						styles: {
							padding: '20px',
							background: '#e74c3c',
							color: 'white',
							border: '3px solid #fd7c6f',
							margin: '10px 0'
						}
					}
				]
				}
			],
			toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
			toolbar2: 'print preview media | forecolor backcolor emoticons codesample | pastetext removeformat | fullscreen',
			content_css: ['/styles/mce.css']
		});
	}
	
	if (leftNav.length > 0) {
		doc.on('keydown', function (e) {
			if (e.shiftKey && e.keyCode === 81) {
				console.log('shortcut');
				leftNav[0].click();
			}
		});
	}
	
	if (rightNav.length > 0) {
		doc.on('keydown', function (e) {
			
			if (e.shiftKey && e.keyCode === 68) {
				console.log('shortcut');
				rightNav[0].click();
			}
		});
	}
	
	if (markButton.length > 0) {
		doc.on('keydown', function (e) {
			
			if (e.shiftKey && e.keyCode === 32) {
				console.log('shortcut');
				markButton.click();
			}
		});
	}
	
	doc.on('keydown', (e) => {
		if ($('.popup.visible').length > 0 && e.keyCode === 27) {
			e.preventDefault();
			closePopup()
		}
	});
	
	$('.popup i.fa.fa-close').on('click', (e) => {
		
		e.preventDefault();
		closePopup();
	});
	
	$('.overlay').on('click', (e) => {
		e.stopPropagation();
		if ($(e.target).is($('.overlay.visible'))) {
			e.preventDefault();
			closePopup();
		}
	});
	
	
});

function getUrlParts(url) {
	let a = document.createElement('a');
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

function openPopup() {
	$('.overlay').addClass('visible');
	$('.popup').addClass('visible');
	$('body').css({overflow: 'hidden'});
	
}

function closePopup() {
	
	$('.overlay').removeClass('visible');
	$('.popup').removeClass('visible');
	$('body').css({overflow: 'auto'});
	
	
	
}