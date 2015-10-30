$(function () {

    $.extend(Slider,{

        init: function(){

            // Slider carousel
            R.push({
                'box': '.inf-slider',
                'template': 'menu-slider',
                'json': Slider,
                'url': false,
                'after': function () {
                    Slider.countdown();
                    Slider.carousel();

                }
            });

        },

carousel: function () {

                    $(".slider-top").owlCarousel({
                        navigation: false,
                        slideSpeed: 300,
                        paginationSpeed: 400,
                        singleItem: true,
                        autoPlay: true
                    });

},

countdown: function () {

                    $("#countdownHolder").countdown({
                        until: (Slider.timer),
                        layout: '{hnn}<span>:</span>{mnn}<span>:</span>{snn}',
                        onExpiry: function(){

          console.log('Slider.timeout');
          Slider.timeout();}
                    });

},

 "update": function () {

            var url = '/lottery/slider';
            
            $.getJSON(url, function(response) {

                if (response.res.id == Slider.lottery.id) {

                    setTimeout(function() {
                        Slider.update()
                    }, 3000)

                } else {
                    
                    $.extend(Slider, data.res);
                    Slider.init();
                }

            });
        },
        
        timeout: function(){
            if (Tickets.countFilled() > 0 && !Game.isRun()) {
                Lottery.prepareData();
            } else {
                Lottery.update();
            }
        },


    });

});