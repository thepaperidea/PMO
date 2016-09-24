var currentBackground,arr;

function script(script){
  background();
  square();
  slider();
  markdown();
  svg();
  navigation();
  opacity();
  form();
  scroll();
  $('body').removeClass('home');
  if(script=="home"){
    $('body').addClass('home');
    $('#background').addClass('video');
    $.getJSON("stay.json",function(json){
      searchShow();
      searchBlur();
      searchInput(json);
      suggestion(json);
    });
    makeHoliday();
  }
  else if(script=="stayall"){

  }
  else if(script=="stay"){
    //bookingButton();
    swipeimages();
    initMap();
  }
  else if(script=="staybook"){
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

function swipeimages() {
  var elem = document.getElementById('homeSlider');
  window.mySwipe = Swipe(elem, {
    auto: 3000,
    speed: 600,
    continuous: true
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

function makeHoliday(){
  var check = new Array();
  var preference = new Array();
  $('.select-change').each(function() {
    var selectid = $(this).data('id');
    check.push({
        name: selectid,
        value:  ""
    });
    preference[selectid] = new Array();
    $(this).on('click','li',function(){
      var id = $(this).data('id');
      if(selectid=="finance")
      preference[selectid] = new Array();
      if(jQuery.inArray( id, preference[selectid] )==-1){
    		preference[selectid].push(id);
    	}else{
        var index = preference[selectid].indexOf(id);
      	preference[selectid].splice(index, 1);
      }
      $.each(check,function() {
        if(this.name==selectid){
          this.value = preference[selectid];
        }
      });
      var index = preference[selectid].indexOf(id);
      var url = $('html').data('url');
      $.post( url+"update", { preferences: check } );
    });
  });
  singleSelect();
  multipleSelect();
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

function findHoliday(){
  $('#lightbox').toggleClass('active');
}

function bookingButton(){
  $('.booking-input').on('change',function(){
    var empty = true;
    arr = [];
    $( ".booking-input" ).each(function() {
      var name = $(this).attr('name');
      var value = $(this).val();
      arr.push({
          name: name,
          value:  value
      });
      if(!value)
      empty = false;
    });
    if(empty){
      $('.booking-button').addClass('active');
      var url = $('html').data('url');
      $.post( url+"update", { booking: arr } );
    }
    else{
      $('.booking-button').removeClass('active');
    }
  });
  var count = $('#tripadvisor script').length;
  if(count==1){
    var url = $('html').data('url');
    var permalink = $('#tripadvisor').data('permalink');
    var link = url+'tripadvisor/'+permalink;
    $('#tripadvisor').html('<iframe src="'+link+'" width="500" height="47" frameBorder="0">Browser not compatible.</iframe>');
  }
}

function download(id) {
  var url = $('html').data('url');
  $.getJSON( url + "download/" + id, function( data ) {
    downloadFile(url + 'uploads/file/' + data.download);
    $('.download'+id).html('Downloaded');
    $('.download'+id).attr( "onclick", "" );
  });
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
      disableDefaultUI: true,
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
      path: "M9.9,25c-0.2,0-0.4-0.3-0.6-0.6L0.2,2.7C0,2.1,0,1.8,0,1.5c0.2-0.3,0.4-0.6,0.8-0.6l0,0l8.7,2.7c0.2,0,0.4,0,0.6,0l8.9-2.7l0,0c0.4,0,0.6,0.3,0.8,0.6s0.2,0.9,0,1.2l-9.3,21.6C10.3,24.7,10.1,25,9.9,25z",
      fillColor: '#52C3C2',
      fillOpacity: 1,
      anchor: new google.maps.Point(10,24),
      strokeWeight: 0,
      scale: 1
    }

    var plane = {
      path: "M0,0c0.4-1.1,0.3-1.9-0.2-2.5c-0.6-0.5-1.4-0.6-2.5-0.2c-1.1,0.4-2.1,1-2.9,1.8l-2.7,2.7l-11.4-2.7c-0.2-0.1-0.4,0-0.5,0.1l-2.2,2.2c-0.1,0.1-0.2,0.3-0.2,0.5c0,0.2,0.1,0.3,0.3,0.4l8.7,4.8l-4.4,4.4l-3.3-0.9c0,0-0.1,0-0.1,0c-0.2,0-0.3,0.1-0.4,0.2l-1.6,1.7c-0.1,0.1-0.2,0.3-0.2,0.4c0,0.2,0.1,0.3,0.2,0.4l4.3,3.2l3.2,4.3c0.1,0.1,0.2,0.2,0.4,0.2h0c0.2,0,0.3-0.1,0.4-0.2l1.6-1.6c0.1-0.2,0.2-0.3,0.1-0.5l-0.9-3.3l4.4-4.4l4.8,8.7c0.1,0.1,0.2,0.2,0.4,0.3c0,0,0.1,0,0.1,0c0.1,0,0.2,0,0.3-0.1l2.2-1.6c0.2-0.2,0.3-0.3,0.2-0.6l-2.7-11.9l2.8-2.8z",
      fillColor: '#52C3C2',
      fillOpacity: 1,
      anchor: new google.maps.Point(-12,12),
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
