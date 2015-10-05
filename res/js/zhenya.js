$( document ).ready(function() {
  
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

  
  
  $('.done').on('click',function(){
  
    $(this).removeClass('done').addClass('active').css({'background-color':'#B7CFD3'});
  })
  
  
})