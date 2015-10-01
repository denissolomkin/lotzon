$( document ).ready(function() {
     $('.slider-top').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: false,
        autoplaySpeed: 2000,
        dots:true
      });
  
  $("#countdownHolder").countdown({
                until: (27943),
                layout: '{hnn}<span>:</span>{mnn}<span>:</span>{snn}',
            });
  $("#countdownHolder-mobile").countdown({
                until: (27943),
                layout: '{hnn}<span>:</span>{mnn}<span>:</span>{snn}',
            });
  
  
  $('a[href=#top]').click(function(){
        $('html, body').animate({scrollTop:0}, 'slow');
        return false;
    });
  
  
  
  $(window).scroll(function() {

    if ($(window).scrollTop() >= 150 ) {

        $('.go-to-top').fadeIn(200);

    } else {
        $('.go-to-top').fadeOut(300);
    }
});
  
  
  if ( $(window).width() > 739) {
    $(window).scroll(function(){
					getscroll();
					if (yScroll > 135)
					{$('.header').css('position','fixed').css('top' ,'0px')} else
					{$('.header').css('position','relative').css('top','0px')}
      

				});
				function getscroll(){
				if (self.pageYOffset){
				yScroll = self.pageYOffset;
				xScroll = self.pageXOffset;
				} else if (document.documentElement && document.documentElement.scrollTop){
				yScroll = document.documentElement.scrollTop;
				xScroll = document.documentElement.scrollLeft;
				} else if (document.body){
				yScroll = document.body.scrollTop;
				xScroll = document.body.scrollLeft;
				}
				};
  }else {
        $(window).scroll(function(){
					getscroll();
					if (yScroll > 0)
					{$('.header').css('position','fixed').css('top' ,'0px')} else
					{$('.header').css('position','relative').css('top','0px')}
				});
				function getscroll(){
				if (self.pageYOffset){
				yScroll = self.pageYOffset;
				xScroll = self.pageXOffset;
				} else if (document.documentElement && document.documentElement.scrollTop){
				yScroll = document.documentElement.scrollTop;
				xScroll = document.documentElement.scrollLeft;
				} else if (document.body){
				yScroll = document.body.scrollTop;
				xScroll = document.body.scrollLeft;
				}
				};
}

  
  
  
  
  
  
  
})