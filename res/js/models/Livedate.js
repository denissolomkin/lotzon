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

                format = typeof format === 'string' && format || false;
                date = Livedate.fn.now(date);

                switch (true) {

                    case moment().add('days', -1).isSame(moment.unix(date), 'day') && (!format || format == 'calendar'):
                        return i18n('title-day-yesterday');
                        break;
                    case moment().isSame(moment.unix(date), 'day') && (!format || format == 'calendar'):
                        return i18n('title-day-today');
                        break;
                    case format === 'calendar':
                        return moment.unix(date).format('D MMM');
                        break;
                    default:
                        return moment.unix(date).format(format || 'DD.MM.YYYY');
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