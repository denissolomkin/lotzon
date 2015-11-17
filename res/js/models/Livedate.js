(function () {

    Livedate = {

        init: function () {

            livedateInterval = window.setInterval(function () {

                var livedates = visible('.live-date');

                for(var i = 0; i<livedates.length;i++ ){
                    var el = livedates[i],
                        date = Livedate.fn.from(el.getAttribute('data-stamp'));

                    if (date != el.innerHTML){
                        el.innerHTML = date;
                        fadeIn(el);
                    }
                }

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