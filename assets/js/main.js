var currentBackground,arr;

function script(script){
  background();
  square();
  slider();
  markdown();
  svg();
  form();
  scroll();
  section();
  headerimg();
  // linetitle();
  if(script=="home"){
    swiper();
    background();
  }
  else if(script=="category"){
    category();
  }
  else if(script=="stayall"){

  }
  else if(script=="packagebook"){
    bookingButton();
  }
  else if(script=="stay"){
    tripadvisor();
    category();
    swiper();
    roomdetail();
    initMap();
  }
  else if(script=="staybook"){
    bookingButton();
    roomcount();
  }
  else if(script=="article"){
    disqus();
  }
  else if(script=="country"){
    countryTabs();
    contentWrap();
  }
}

function roomdetail() {
  $('.stay.room>li').on('click',function() {
    $('.roomdetails').addClass('active');
    var id = $(this).data("room");
    $('.roomdetails>.specificroom').removeClass('active');
    $('.specificroom.room'+id).addClass('active');
  });
}

function swiper(){
  var mySwiper = new Swiper ('.swiper-container', {
    direction: 'horizontal',
    nextButton: '.swiper-button-next',
    prevButton: '.swiper-button-prev',
    preloadImages: false,
    lazyLoading: true
  });
}

function headerimg(){
  $('body').removeClass('headerimg');
  if ($('section:first-child').hasClass('header-image'))
  $('body').addClass('headerimg');
  $(window).on('scroll',function() {
    if ($('section:first-child').hasClass('header-image')){
      if($(window).scrollTop() > $(window).height()) {
        $('body').removeClass('headerimg');
      }
      else{
        $('body').addClass('headerimg');
      }
    }
  });
}

function category() {
  $('.stay').mixItUp({
    animation: {
  		duration: 400,
  		effects: 'fade translateZ(-360px) stagger(34ms)',
  		easing: 'ease'
  	}
  });
}

function linetitle() {
  $(".line-title").each(function() {
    var fullwidth = $(this).width();
    var width = $('*',this).width();
    var value = fullwidth - width;
    $('*',this).data('width',value);
  });
}

function section() {
  var height = $(window).height();
  $('#arrow-bottom').on('click',function(){
    $('main').animate({ scrollTop: height }, 1000);
  });
  $(".full-height").each(function() {
    $(this).css('min-height',height);
  });
}

function speaker() {
  $('#speaker').on('click',function(){
    $(this).toggleClass('active');
    if($( "#speaker" ).hasClass( "active" )){
      $("#background>video").prop('muted', true);
    }
    else{
      $("#background>video").prop('muted', false);
    }
  });
}

function swipeimages(element) {
  var mySwiper = new Swiper ('.swiper-container', {
    // Optional parameters
    direction: 'horizontal',

    // Navigation arrows
    nextButton: '.swiper-button-next',
    prevButton: '.swiper-button-prev'
  });
}

function roomcount() {
  $('.roomcount').on('change',function(){
    var total = 0;
    $('.roomcount').each(function() {
      var price = $(this).data('price');
      var quantity = $(this).val();
      total += price*quantity;
    });
    $('.price-summary>.total').html(total);
  });
}

function scroll(){
  var didScroll;
  var lastScrollTop = 0;
  var delta = 5;
  var navbarHeight = $('header').outerHeight();

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
          $('header').removeClass('nav-down').addClass('nav-up');
      } else {
          // Scroll Up
          if(st + $(window).height() < $(document).height()) {
              $('header').removeClass('nav-up').addClass('nav-down');
          }
      }

      lastScrollTop = st;
  }
}

function headerToggle(action){

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

function countryTabs(){
  $('.countrytabs>div>ul>li:first-child>a,.countrytabs>.tabdescription>div:first-child').addClass('active');
  $('.countrytabs>div>ul>li>a').on('click',function(){
    var id = $(this).data('id');
    $('.countrytabs>.tabdescription>div').removeClass('active');
    $('.countrytabs>.tabdescription>div#countrytab'+id).addClass('active');
  });
}

function singleSelect() {
  $('.single-select').each(function() {
    var element = this;
    $('li',this).on('click',function(){
      $('li',element).removeClass('active');
      $(this).addClass('active');
    });
  });
}

function multipleSelect() {
  $('.multiple-select').each(function() {
    $('li',this).on('click',function(){
      $(this).toggleClass('active');
    });
  });
}

function navigation() {
  var $grid = $('.slabs.nav').isotope({
    itemSelector: '.slabs.nav>li',
    layoutMode: 'fitRows'
  });

  $('.navigation>li').on( 'click','a', function() {
    $('.navigation>li>a').removeClass('active');
    $(this).addClass('active');
    var filterValue = $( this ).attr('data-filter');
    $grid.isotope({ filter: filterValue });
  });
}

function contentWrap(){
  $('.content.markdown>*:nth-child(n+2)').addClass('hide');
  $('.content.markdown').append('<span class="showmoretext align-center"><a class="button large">Read more</a></span>')
  $('.showmoretext>.button').on('click',function(){
    $('.showmoretext').hide();
    $('.content.markdown>*:nth-child(n+2)').removeClass('hide');
  });
}

function countrymap(variable) {
  $('.countrymap>svg>g.'+variable+' *').attr("class", "active");
  $('.countrydestination>li.'+variable).addClass('active');
  $('.countrymap>svg>g').on('mouseover',function() {
    var id = $(this).data('id');
    if(id){
      $('.countrymap>svg>g.exist *').attr("class", " ");
      $('*',this).attr("class", "active");
      $('.countrydestination>li').removeClass('active');
      $('.countrydestination>li.'+id).addClass('active');
    }
  });
}

function continentmap() {
  $('.continentmap>svg>g').on('click',function() {
    var id = $(this).data('id');
    if(id){
      $('.continentmap>svg>g.exist *').attr("class", " ");
      $('*',this).attr("class", "active");
      $('.continentlist>li').removeClass('active');
      $('.continentlist>li.'+id).addClass('active');
    }
  });
}

function opacity() {
  $( ".opacity" ).each(function() {
    var opacity = $(this).data('opacity');
    $(this).css('opacity',opacity);
  });
}

function svg() {
  $( ".svg" ).each(function() {
    var svg = $(this).data('svg');
    var callback = $(this).data('callback');
    var variable = $(this).data('variable');
    $( this ).load( svg, function() {
      if(callback=='countrymap')
      countrymap(variable);
      else if(callback=='continentmap')
      continentmap();
    });
  });
}

function markdown(){
  $('.markdown>p').each(function(){
    $( this ).has( "img" ).addClass( "full" );
  });
}

function disqus() {
  (function() { // DON'T EDIT BELOW THIS LINE
      var d = document, s = d.createElement('script');
      s.src = '//travrnr.disqus.com/embed.js';
      s.setAttribute('data-timestamp', +new Date());
      (d.head || d.body).appendChild(s);
  })();
}

function tripadvisor(){
  var count = $('#tripadvisor script').length;
  if(count==1){
    var url = $('html').data('url');
    var permalink = $('#tripadvisor').data('permalink');
    var link = url+'tripadvisor/'+permalink;
    $('#tripadvisor').html('<iframe src="'+link+'" width="500" height="47" frameBorder="0">Browser not compatible.</iframe>');
  }
}

function initMap() {
  $( ".map" ).each(function() {
    var element = $(this);
    var latitude = element.data('latitude');
    var longitude = element.data('longitude');
    var airportlongitude = element.data('airportlongitude');
    var airportlatitude = element.data('airportlatitude');
    var zoom = element.data('zoom');
    var myLatLng = {lat: latitude, lng: longitude};
    var airportlatlong = {lat: airportlatitude, lng: airportlongitude};

    var latlngbounds = new google.maps.LatLngBounds();

    var map = new google.maps.Map(element[0], {
      zoom: zoom,
      center: myLatLng,
      scrollwheel: false,
      draggable: !("ontouchend" in document),
      styles: [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]
    });

    var flightPlanCoordinates = [
      {lat: airportlatitude, lng: airportlongitude},
      {lat: latitude, lng: longitude},
    ];

    var flightPath = new google.maps.Polyline({
      path: flightPlanCoordinates,
      geodesic: true,
      strokeColor: '#000000',
      strokeOpacity: .1,
      strokeWeight: 1
    });

    flightPath.setMap(map);

    var icon = {
      path: "M11.4,0C5.1,0,0,5.1,0,11.4s9.5,19,11.4,19c1.9,0,11.4-12.7,11.4-19S17.7,0,11.4,0z M11.4,19c-4.2,0-7.6-3.4-7.6-7.6s3.4-7.6,7.6-7.6c4.2,0,7.6,3.4,7.6,7.6S15.6,19,11.4,19z",
      fillColor: '#f39c12',
      fillOpacity: 1,
      anchor: new google.maps.Point(10,24),
      strokeWeight: 0,
      scale: 1
    }

    var plane = {
      path: "M20.2,9.2h-4.6L7.6,0H6.1l2.7,9.2H3.5L0,4.6v4.7v1.5v1.5v1.5v4.6l3.5-4.6h5.3l-2.6,9.2h1.5l7.9-9.2h4.6c1.4,0,2.6-1,2.9-2.3C22.8,10.2,21.6,9.2,20.2,9.2z",
      fillColor: '#f39c12',
      fillOpacity: 1,
      anchor: new google.maps.Point(6,12),
      strokeWeight: 0,
      scale: 1
    }

    var marker = new google.maps.Marker({
      position: myLatLng,
      map: map,
      icon: icon
    });

    latlngbounds.extend(marker.position);

    var airport = new google.maps.Marker({
      position: airportlatlong,
      map: map,
      icon: plane
    });

    latlngbounds.extend(airport.position);

    var bounds = new google.maps.LatLngBounds();

    map.setCenter(latlngbounds.getCenter());
    map.fitBounds(latlngbounds);
  });
}

function slider() {
    var slideCount = $('.slider>ul>li').length;
  	var slideWidth = $('.slider>ul>li').width();
  	var slideHeight = $('.slider>ul>li').height();
  	var sliderUlWidth = slideCount * slideWidth;
    $('.slider').css({ width: slideWidth });
    $('.slider>ul>li').css({ width: slideWidth });
    $('.slider>ul').css({ width: sliderUlWidth, marginLeft: - slideWidth });
    $('.slider>ul>li:last-child').prependTo('.slider>ul');
    function moveLeft() {
        $('.slider>ul').animate({
            left: + slideWidth
        }, 500, function () {
            $('.slider>ul>li:last-child').prependTo('.slider>ul');
            $('.slider>ul').css('left', '');
        });
    };

    function moveRight() {
        $('.slider>ul').animate({
            left: - slideWidth
        }, 500, function () {
            $('.slider>ul>li:first-child').appendTo('.slider>ul');
            $('.slider>ul').css('left', '');
        });
    };

    $('.slider>a.previous').click(function () {
        moveLeft();
    });

    $('.slider>a.next').click(function () {
        moveRight();
    });
}

function square() {
  $( ".square" ).each(function() {
    var width = $(this).width();
    $( this ).height( width );
  });
}

function background() {
  $( ".background" ).each(function() {
    var background = $(this).data('background');
    var backgroundobj = this;
    $('<img/>').load(background,function() {
       $(this).remove();
       $( backgroundobj ).css('background-image','url('+background+')');
    });
  });
}

function searchShow(){
  $('#search').addClass('visible');
}

function searchBlur(){
  $('#search>.search-input>input').on('focus',function(){
    $('#search').addClass('blur');
  });
  $('#background').on('click',function(){
    $('#search').removeClass('blur');
  });
  $('#background').on('mouseover',function(){
    $(this).attr('style',"");
    $(this).addClass('video');
    currentBackground = null;
  });
}

function searchInput(json){
  $('#search>.search-input>input').on('input',function(){
    var string = $(this).val().toLowerCase();
    if(string.length > 3)
    resultLocate(json,string);
    else
    resultHide();
  });
}

function suggestion(json) {
  $('.categories>div>ul>li').on('click',function(){
    var string = $(this).html();
    $('#search>.search-input>input').val(string);
    string = string.toLowerCase();
    resultLocate(json,string);
  });
}

function resultLocate(json,string){
  var returnarray = new Array();
  $.each( json, function() {
    if (this.search.indexOf(string) >= 0)
    returnarray.push(this);
  });
  resultShow(returnarray,json);
}


function resultShow(returnarray,json){
  var url = $('html').data('url');
  if(returnarray.length > 0){
    $('.results').addClass('active');
    $('.results').html('<ul></ul>');
    $.each( returnarray, function() {
      $('.results>ul').append('<li><a class="ajax-nav" href="'+url+'stay/'+this.link+'" data-background="'+this.image.main_image+'"><ul><li>'+this.name+'<span class="currency float-right">'+this.price+'</span></li><li>'+this.destination.name+'</li></ul></a></li>');
    });
    $('.results>ul>li>a').on('mouseover',function(){
      var background = $(this).data('background');
      var link = url+'uploads/image/'+background;
      if(currentBackground!=background){
        $('#background').addClass('hide');
        var backgroundobj = this;
        $('<img/>').load(link,function() {
           $(this).remove();
           $('#background').css('background-image','url('+link+')');
           $('#background').removeClass('hide');
           $('#background').removeClass('video');
           currentBackground = background;
        });
      }
    });
    ajaxRequest.rebuildLinks();
  }
  else
  resultHide();
}

function resultHide(){
  $('.results').removeClass('active');
  $('.results').html('');
}
