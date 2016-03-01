(function () {

    Livedate = {

        diff: 0,

        init: function (unix) {

            this.diff = (Math.abs(moment().unix() - unix) > 10) ? (moment().unix() - unix) : (1);

            livedateInterval = window.setInterval(function () {

                var livedates = DOM.visible('.live-date');

                for (var i = 0; i < livedates.length; i++) {
                    var el = livedates[i],
                        date = Livedate.fn.from(el.getAttribute('data-stamp'));

                    if (date != el.innerHTML) {
                        el.innerHTML = date;
                        fadeIn(el);
                    }

                    /* todo set different interval for newest and older timestamps
                     if(Math.abs(moment.unix(Livedate.fn.diff(date)).diff(moment(), 'days')) >= 2){
                     }
                     * */

                }

            }, 1000 * 60);

        },


        fn: {

            day: function (date, format) {

                format = typeof format === 'string' && format || 'DD.MM.YYYY';
                date = Livedate.fn.now(date);

                switch (true) {
                    case moment.unix(date).diff(moment(), 'days') === -1 && format === 'DD.MM.YYYY':
                        return i18n('title-day-yesterday');
                        break;
                    case moment.unix(date).diff(moment(), 'days') === -0 && format === 'DD.MM.YYYY':
                        return i18n('title-day-today');
                        break;
                    default:
                        return moment.unix(date).format(format);
                        break;
                }

            },

            from: function (date) {
                return moment.unix(Livedate.fn.now(date)).fromNow();
            },

            destroy: function () {
                window.clearInterval(livedateInterval);
            },

            now: function (date) {
                date = date || moment().unix();
                return parseInt(date) + Livedate.diff;
            }

        }

    }

})();