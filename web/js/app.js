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
	
	if(!isMobile()){
		sidebar.stick_in_parent({offset_top: 80});
	}
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
				'emoticons template textcolor colorpicker textpattern imagetools',
				'powerpaste bootstrapaccordion'
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
			toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image pastetext',
			toolbar2: 'print preview media | forecolor backcolor emoticons codesample | pastetext removeformat | fullscreen | bootstrapaccordion',
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

function isMobile(){
	var isMobile = false;
	if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
		|| /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) isMobile = true;
	return isMobile;
}

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