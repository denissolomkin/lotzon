(function () {

    Content = {

        enableForm: function () {

            D.log('Content.enableForm');
            if (form = document.querySelector('form.filter-render-list-unwatched')) {
                form.classList.remove('filter-render-list-unwatched');
                form.classList.add('filter-render-list');
            }

        },

        enableAutoload: function (event) {

            var submit = this.querySelector('input[type="submit"]');
            submit.classList.add('infinite-scrolling');

            D.log('Content.enableAutoload', this);
            event.preventDefault();

            Content.autoload.call(this);

        },

        autoload: function (event) {

            D.log('Content.autoload', this);

            var form = this,
                renderList = form.querySelector(".render-list"),
                query = $(form).serializeObject();

            while (form && form.nodeName !== 'FORM')
                form = form.parentElement;


            query.first_id = renderList.firstElementChild.getAttribute('data-id');
            query.last_id = renderList.lastElementChild.getAttribute('data-id');
            query.offset = renderList.childElementCount;

            R.push({
                href: form.action,
                replace: this.nodeName === 'INPUT' ? '.render-list-container' : '.render-list',
                query: query,
                after: Content.afterInfiniteScrolling
            });

        },

        infiniteScrolling: function () {

            var infiniteScrolling = $('.infinite-scrolling:not(.loading)').filter(':visible').first()[0];

            if (infiniteScrolling && this.isVisible.call(infiniteScrolling, -200)) {

                this.clearLoading();

                D.log('Content.infiniteScrolling', 'func');
                infiniteScrolling.classList.add("loading");

                this.autoload.call(infiniteScrolling.parentElement);

            }

        },

        clearLoading: function () {

            var infiniteScrollingLoading = document.querySelectorAll('.infinite-scrolling.loading');
            if (infiniteScrollingLoading.length)
                for (var i = 0; i < infiniteScrollingLoading.length; i++)
                    infiniteScrollingLoading[i].classList.remove('loading')

        },

        afterInfiniteScrolling: function (options) {

            D.log('Content.checkInfiniteScrolling', 'func');

            if (!Object.size(options.json)) {

                if (infiniteScrolling = document.querySelector('.infinite-scrolling.loading'))
                    infiniteScrolling.remove();
            }

            Content.clearLoading();

        },

        isVisible: function (y) {

            y = y || 0;
            var bounds = this.getBoundingClientRect();
            return bounds.top + y < window.innerHeight && bounds.bottom + y > 0;

        }

    };


})();