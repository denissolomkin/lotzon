(function () {

    Content = {

        enableForm: function () {

            D.log('Content.enableForm', 'content');
            if (form = document.querySelector('form.render-list-form-unwatched')) {
                form.classList.remove('render-list-form-unwatched');
                form.classList.add('render-list-form');
            }

        },

        enableAutoload: function (event) {

            D.log(['Content.enableAutoload', 'content']);
            event.preventDefault();

            var submit = this.querySelector('button[type="submit"]:not(.loading):not(.infinite-scrolling)');
            if (submit) {
                submit.classList.add('infinite-scrolling');
                Content.autoload.call(this, event);
            }

        },

        autoload: function (event) {

            D.log('Content.autoload', 'content');
            var form = this;

            while (form && form.nodeName !== 'FORM')
                form = form.parentElement;

            /* Object.keys(renderList.classList).map(function (key) {
             return renderList.classList[key]
             }).join('.')
             */

            var renderList = form.querySelector(".render-list"),
                query = $(form).serializeObject(),
                isFilterChange = event && event.type === 'change',
                replaceForm = 'form[action="' + form.getAttribute('action') + '"]',
                replacePlace = replaceForm + (isFilterChange
                        ? ' .render-list-container' /* change filter */
                        : ' .render-list') /* submit or scroll */;

            query.first_id = renderList.firstElementChild && renderList.firstElementChild.getAttribute('data-id');
            query.last_id = renderList.lastElementChild && renderList.lastElementChild.getAttribute('data-id');
            query.offset = renderList && renderList.childElementCount;

            R.push({
                href: form.action,
                replace: replacePlace,
                query: query,
                after: Content.afterInfiniteScrolling
            });

        },

        infiniteScrolling: function () {

            var infiniteScrolling = $('.infinite-scrolling:not(.loading)').filter(':visible').first()[0];

            if (infiniteScrolling && isVisible.call(infiniteScrolling, -200)) {

                this.clearLoading();

                D.log('Content.infiniteScrolling', 'func');
                infiniteScrolling.classList.add("loading");

                this.autoload.call(infiniteScrolling);

            }

        },

        afterInfiniteScrolling: function (options) {

            D.log('Content.checkInfiniteScrolling', 'content');

            var name = null,
                renderList = null,
                className = [];

            if (options.replace.indexOf('.render-list-container') !== -1) { /* new filter, so update class render-list */
                for (name in options.query) {
                    if (options.query[name] && ['offset', 'first_id', 'last_id'].indexOf(name) === -1 && name.indexOf('date') === -1 && options.query[name]) { /* skip unimportant filters */
                        if (!renderList && !(renderList = document.querySelector(options.replace + ' .render-list')))  /* break, if can't find render-list */
                            break;
                        className = [name, options.query[name]];
                        renderList.classList.add(className.join('-'));
                    }
                }
            }

            if (!Object.size(options.json)) {

                if (infiniteScrolling = document.querySelector(options.replace + ' .infinite-scrolling.loading'))
                    infiniteScrolling.remove();
            }

            Content.clearLoading(options)
                .infiniteScrolling();

        },

        clearLoading: function () {

            var infiniteScrollingLoading = document.querySelectorAll('.infinite-scrolling.loading');
            if (infiniteScrollingLoading.length)
                for (var i = 0; i < infiniteScrollingLoading.length; i++)
                    infiniteScrollingLoading[i].classList.remove('loading')

            return this;

        }

    };


})();