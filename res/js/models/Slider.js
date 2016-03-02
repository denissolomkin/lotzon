(function () {

    Slider = {

        init: function (init) {

            D.log('Slider.init', 'func');
            Object.deepExtend(this, init);

            R.push({
                'template': 'menu-slider',
                'json': this
            });

            return this;

        },

        after: function () {

            D.log('Slider.after', 'func');
            Slider.countdown();
            Slider.carousel();
        },

        carousel: function () {

            D.log('Slider.carousel', 'func');

            var owl = $(".slider-top");

            if (owl.data('owlCarousel'))
                owl.data('owlCarousel').reinit();
            else
                owl.owlCarousel({
                    navigation: false,
                    slideSpeed: 300,
                    paginationSpeed: 400,
                    singleItem: true,
                    autoPlay: true
                });

        },

        countdown: function (timer) {

            D.log('Slider.countdown: '+timer, 'func');
            $("#countdownHolder")
                .countdown('destroy')
                .countdown({
                until: (timer || Slider.timer),
                layout: '{hnn}<span>:</span>{mnn}<span>:</span>{snn}',
                onExpiry: Slider.timeout
            });

        },

        timeout: function () {

            D.log('Slider.timeout', 'func');
            if (Tickets.countFilled() > 0 && !Game.isRun()) {
                Lottery.prepareData();
            } else {
                Lottery.update();
            }
        }

    };

})();