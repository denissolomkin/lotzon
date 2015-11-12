(function () {
    Carousel = {
        Owl: null,
        initOwl: function () {
            // OWL CAROUSEL =========================== //
//            console.error($matchesCarousel.css('box-shadow'));
            
            if ($('.carousel-spin').length){
                if (parseFloat(Device.get()) <= 0.5) {
                    $('.carousel-spin').owlCarousel({
                        singleItem: true,
                        autoPlay: false
                    });
                    Carousel.Owl = $('.carousel-spin').data('owlCarousel');
                    return;

                } else {
                    if (Carousel.Owl !== null) {
                        Carousel.Owl.destroy();
                        Carousel.Owl = null;
                    }
                }
            }

            var $matchesCarousel = $('.carousel');
            if ($matchesCarousel.length) {
                if ($matchesCarousel.css('box-shadow') === 'none') {
                    
                    if (Carousel.Owl !== null) {
                        Carousel.Owl.destroy();
                        Carousel.Owl = null;
                    }
                } else {
                    if (Carousel.Owl === null) {
                        $matchesCarousel.owlCarousel({
                            singleItem: true,
                            autoPlay: true
                        });
                        Carousel.Owl = $matchesCarousel.data('owlCarousel');
                    }
                }
            }
        },
        destroy: function () {

        }
    }

})();