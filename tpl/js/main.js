$(function(){
    var dHeight = $(window).height();
    $('.display-slide').height(dHeight);
    $(window).on('resize', function(){
        var dHeight = $(window).height();
        $('.display-slide').height(dHeight);
    });
    $('.to-slide').on('click', function(e){
        var toSlide = $(e.currentTarget).attr('data-slide');
        var point = $('#slide'+toSlide).offset().top;
        $('html, body').animate({scrollTop : point},900, 'easeInOutQuint');
    });
})
