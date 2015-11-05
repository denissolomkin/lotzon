(function () {

    Carousel = {
        Owl: null,
        initOwl: function () {
            // OWL CAROUSEL =========================== //
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

        destroy: function(){

        }
    }

})();