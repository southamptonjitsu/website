var $ = require('jquery');

$(function() {
    var url = 'https://www.google.com/maps/embed/v1/place?key=AIzaSyBjg8z5GsVNkNJC813LyUUEPsq3SOnHMYI&q=';
    var addr = $('#contactDetails_Address').text();
    $('.googleMap').attr('src',url+addr);

    var mobileNav = document.querySelector('.nav-mobile');
    var nav = document.querySelector('nav');
    $(mobileNav).click(function() {
        $(this).toggleClass('nav-mobile-open');
        $(nav).toggleClass('nav-active');
    });
});
