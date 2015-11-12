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

            D.log('Content.enableAutoload', this);
            event.preventDefault();

            var submit = this.querySelector('input[type="submit"]');
            submit.classList.add('infinite-scrolling');

            Content.autoload.call(this);

        },

        changeFilter: function (event) {

            D.log('Content.changeFilter', this);
            event.preventDefault();

            Content.autoload.call(this);

        },

        autoload: function (event) {

            D.log('Content.autoload', this);

            var form = this;

            while (form && form.nodeName !== 'FORM')
                form = form.parentElement;

            var renderList = form.querySelector(".render-list"),
                query = $(form).serializeObject(),
                replace = this.nodeName === 'INPUT' ? '.render-list-container' : '.' + Object.keys(renderList.classList).map(function (key) {
                    return renderList.classList[key]
                }).join('.');

            query.first_id = renderList.firstElementChild && renderList.firstElementChild.getAttribute('data-id');
            query.last_id = renderList.lastElementChild && renderList.lastElementChild.getAttribute('data-id');
            query.offset = renderList && renderList.childElementCount;

            R.push({
                href: form.action,
                replace: replace,
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

                this.autoload.call(infiniteScrolling.parentElement); // fix for checking this.nodeName === 'INPUT'

            }

        },

        clearLoading: function () {

            var infiniteScrollingLoading = document.querySelectorAll('.infinite-scrolling.loading');
            if (infiniteScrollingLoading.length)
                for (var i = 0; i < infiniteScrollingLoading.length; i++)
                    infiniteScrollingLoading[i].classList.remove('loading')

            return this;

        },

        afterInfiniteScrolling: function (options) {

            D.log('Content.checkInfiniteScrolling', 'func');

            if (!Object.size(options.json)) {

                if (infiniteScrolling = document.querySelector('.infinite-scrolling.loading'))
                    infiniteScrolling.remove();
            }

            Content.clearLoading()
                .infiniteScrolling();

        },

        isVisible: function (y) {

            y = y || 0;
            var bounds = this.getBoundingClientRect();
            return bounds.top + y < window.innerHeight && bounds.bottom - y > 0;

        }

    };


})();