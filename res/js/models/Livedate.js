(function () {

    Livedate = {

        init: function () {

            livedateInterval = window.setInterval(function () {
                $('.live-date:visible').each(function () {
                    var el = $(this),
                        date = Livedate.from(el.data('stamp'));
                    if (date != el.text())
                        el.text(date).hide().fadeIn();
                })
            }, 1000 * 60);

        },

        fn: {

            day: function (date, format) {

                format = typeof format === 'string' && format || 'DD.MM.YYYY';
                date = parseInt(date);

                switch (moment.unix(date).diff(moment(), 'days')) {
                    case -0:
                    case -1:
                        return moment.unix(date).calendar();
                        break;
                    default:
                        return moment.unix(date).format(format);
                        break;
                }

            },

            from: function (date) {
                return moment.unix(parseInt(date)).fromNow();
            },

            destroy: function () {
                window.clearInterval(livedateInterval);
            }
        }

    }

})();