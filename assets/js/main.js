var currentBackground,arr;

function script(script){
  background();
  square();
  slider();
  if(script=="home"){
    $.getJSON("stay.json",function(json){
      searchShow();
      searchBlur();
      searchInput(json);
      suggestion(json);
    });
  }
  else if(script=="stay"){
    bookingButton();
    initMap();
  }
  else if(script=="disqus"){
    (function() { // DON'T EDIT BELOW THIS LINE
        var d = document, s = d.createElement('script');
        s.src = '//travrnr.disqus.com/embed.js';
        s.setAttribute('data-timestamp', +new Date());
        (d.head || d.body).appendChild(s);
    })();
  }
}

function findHoliday(){

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
      $.post( url+"updatebooking", { booking: arr } );
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
    var zoom = element.data('zoom');
    var myLatLng = {lat: latitude, lng: longitude};

        var map = new google.maps.Map(element[0], {
          zoom: zoom,
          center: myLatLng,
          scrollwheel: false,
          styles: [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]
        });

        var marker = new google.maps.Marker({
          position: myLatLng,
          map: map,
          title: 'Hello World!'
        });
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
    var background = 'assets/img/background.jpg';
    $(this).css('background-image','url('+background+')');
    $(this).addClass('video');
    currentBackground = background;
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
