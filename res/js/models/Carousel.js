(function () {
    Carousel = {
        Owl: null,
        initOwl: function () {

            if ($('.carousel-spin').height() > 0) {
                if ((parseFloat(Device.get()) <= 0.5 && parseFloat(Device.get()) != 0.3) || $(document).width() <= 768) {
                    $('.carousel-spin').owlCarousel({
                        singleItem: true,
                        autoPlay: false,
                        autoHeight: false,
                        afterInit: function () {
//                            this.jumpTo(1);
                        }
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