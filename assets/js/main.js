$(function() {
	svg();
	Barba.Pjax.start();
	initialLoad();
	fixLinks();
	scroll();
	mouseBottom();
	message();
});

function fixLinks(){
	var links = document.querySelectorAll('a[href]');
	var cbk = function(e) {
		if(e.currentTarget.href === window.location.href) {
			e.preventDefault();
			e.stopPropagation();
		}
	};

	for(var i = 0; i < links.length; i++) {
		links[i].addEventListener('click', cbk);
	}
}

function always(){
	backgroundImage();
	animatedButton();
	setTheme();
	sorter();
	galleryTop();
	jobs();
	form();
}

function initialLoad(){
	setTimeout(function(){
		$('body').addClass('initial-load');
	},1000);
}

function message(){
	$('#message').on('click', '.close', function(event) {
		$(this).parent().removeClass('active');
	});

	$('.message').on('click', function(event) {
		$('#message').addClass('active');
	});
}

function form(){
	$('.form-submit').on('click',function(){
		var form = new Array();
		var check = true;
		$('.form-input').each(function() {
			var name = $(this).attr('name');
			var value = $(this).val();
			form.push({
				name: name,
				value: value
			});
			if((value=="")&&($(this).hasClass("required"))){
				check = false;
				$(this).addClass('red');
			}
			else{
				$(this).removeClass('red');
			}
		});
		if(check){
			$('.form-submit').unbind( "click" );
			$('.form-submit').html('Submitting');
			$('.form-input').prop('disabled', true);
			var url = $('html').data('url');
			$.post( url+"send",{ formdata: form })
			.success(function(){
				$('.form-return').html('<p class="green">Successfully sent</p>');
				$('.form-submit').html('Submitted');
			})
			.fail(function(){
				$('.form-input').prop('disabled', false);
				$('.form-return').html('<p class="red">Cannot send request. Try again later.</p>');
			});
		}
	});
}

function mouseBottom(){
	$(document).on("mousemove", function(event) {
		var windowHeight = $(window).height();
		var mouseY = event.pageY - $(window).scrollTop();
		var bottomFirst = windowHeight-$('footer').height()-50;
		var bottomLast = windowHeight;
		if ((mouseY > bottomFirst) && (mouseY < bottomLast)){
			$('body').addClass('footerAbsolute');
		} else {
			$('body').removeClass('footerAbsolute');
		}
	});
}

function jobs(){
	if ($('.job').length){
		$('.job>ul>li').on('click', '.name,.caret', function(event) {
			$(this).parent().toggleClass('active');
		});
	}
}

function galleryTop() {
	if ($('.gallery-top').length){
		var galleryTop = new Swiper('.gallery-top', {
			spaceBetween: 10,
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			lazy: true
		});
		var galleryThumbs = new Swiper('.gallery-thumbs', {
			spaceBetween: 10,
			centeredSlides: true,
			slidesPerView: 'auto',
			touchRatio: 0.2,
			slideToClickedSlide: true,
		});
		galleryTop.controller.control = galleryThumbs;
		galleryThumbs.controller.control = galleryTop;
	}
}

function sorter() {
	if ($('.projects').length){
		var mixer = mixitup('.projects', {
			animation: {
				effects: 'fade stagger(100ms)'
			}
		});
	}
}

function setTheme(){
	var theme = $('#theme-input').val();
	if (theme == undefined){
		$('body').attr('data-theme', 'default');
	} else {
		$('body').attr('data-theme', theme);
	}
}

function scroll(){
	var didScroll;
	var lastScrollTop = 0;
	var delta = 5;
	var navbarHeight = $('header').outerHeight()-40;

	$(window).scroll(function(event){
		didScroll = true;
	});

	setInterval(function() {
		if (didScroll) {
			hasScrolled();
			didScroll = false;
		}
	}, 250);

	function hasScrolled() {
		var st = $(this).scrollTop();

      // Make sure they scroll more than delta
      if(Math.abs(lastScrollTop - st) <= delta)
      	return;

      // If they scrolled down and are past the navbar, add class .nav-up.
      // This is necessary so you never see what is "behind" the navbar.
      if (st > lastScrollTop && st > navbarHeight){
          // Scroll Down
          $('body').removeClass('not-reading').addClass('reading');
      } else {
          // Scroll Up
          if(st + $(window).height() < $(document).height()) {
          	$('body').removeClass('reading').addClass('not-reading');
          }
      }

      lastScrollTop = st;
  }
}

function scrolled(){
	var lastScrollTop = 0;
	$(window).scroll(function() {
		var st = $(this).scrollTop();
		if($(window).scrollTop() == 0) {
			$('body').removeClass('reading');
		}
		else if($(window).scrollTop() + $(window).height() == $(document).height()) {
			$('body').removeClass('reading');
		} else {
			if (st > lastScrollTop){
				$('body').addClass('reading');
			} else {
				$('body').removeClass('reading');
			}
		}

		lastScrollTop = st;
	});
}

Barba.Dispatcher.on('linkClicked', function() {
	loader();
});

Barba.Dispatcher.on('transitionCompleted', function() {
	always();
});

function loader(){
	$('body').addClass('loading');
	setTimeout(function(){
		$('body').removeClass('loading');
	}, 1500);
}

function animatedButton(){
	$(".animated-button").each(function( index ) {
		if ($(this).data('done') != true){
			var width = $(this).width();
			var height = $(this).height();
			var perimeter = 2*(width+height);
			var offset = (perimeter/4)+(0.1*perimeter);
			$(this).data('hover-offset', offset*2);
			$(this).data('reset-offset', offset)
			var svg = '<svg width="'+width+'" height="'+height+'"><rect width="'+width+'" height="'+height+'" style="fill:none;stroke-width:3;stroke-dasharray:'+((perimeter/4)+(0.15*perimeter))+','+((perimeter/4)-(0.15*perimeter))+';stroke-dashoffset:'+offset+';" /></svg>';
			$(this).append(svg);
			$(this).data('done',true)
		}
	});

	$(".animated-button").hover(
		function() {
			var offset = $(this).data('hover-offset');
			$(">svg>rect",this).css('stroke-dashoffset',offset)
		}, function() {
			var offset = $(this).data('reset-offset');
			$(">svg>rect",this).css('stroke-dashoffset',offset)
		}
		);
}

function svg() {
	$( ".svg" ).each(function() {
		var svg = $(this).data('svg');
		$( this ).load( svg );
	});
}

function backgroundImage(){
	$(".background").each(function() {
		if ($(this).data('done') != true){
			var queue = new createjs.LoadQueue(false);
			queue.on("fileload", attachImage, this);
			queue.loadFile($(this).data('background'));
			$(this).data('done',true)
		}
	});

	function attachImage(event) {
		$(this).append(event.result);
		var imageRatio = event.result.naturalWidth/event.result.naturalHeight;
		var thisRatio = $(this).width()/$(this).height();
		if (imageRatio > thisRatio){
			$(this).addClass('portrait');
		} else {
			$(this).addClass('landscape');
		}
		$(this).addClass('active')
	}
}
