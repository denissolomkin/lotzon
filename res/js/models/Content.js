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

                if (!submit.classList.contains('never-infinite-scrolling'))
                    submit.classList.add('infinite-scrolling');

                Content.autoload.call(this, event);
            }

        },

        autoload: function (event) {

            D.log('Content.autoload', 'content');
            var form = event && event.target || this;

            while (form && form.nodeName !== 'FORM')
                form = form.parentElement;

            // can be reply form
            if (!form.classList.contains('render-list-form'))
                return true;

            var renderList = form.querySelector(".render-list"),
                query = $(form).serializeObject();

            if (event && event.type === 'change') {

                R.push({
                    href: form.action.replace('list', 'container'),
                    json: {},
                    query: query,
                    after: Content.after.changeFilter
                });

            } else {

                var first_id = renderList.firstElementChild && renderList.firstElementChild.getAttribute('data-id') || null,
                    last_id = renderList.lastElementChild && renderList.lastElementChild.getAttribute('data-id') || null;

                if (first_id && last_id) {
                    if (first_id > last_id)
                        query.before_id = last_id;
                    else
                        query.after_id = last_id;
                }

                query.offset = renderList && renderList.childElementCount || null;

                R.push({
                    href: form.action,
                    query: query,
                    after: Content.after.autoload
                });
            }

        },

        infiniteScrolling: function () {

            var infiniteScrolling = DOM.visible([
                '.die-infinite-scrolling:not(.loading)',
                '.once-infinite-scrolling:not(.loading)',
                '.infinite-scrolling:not(.loading)'
            ]);

            if (infiniteScrolling.length) {
                // Content.clearLoading();
                for (var i = 0; i < infiniteScrolling.length; i++) {
                    if (Device.onScreen.call(infiniteScrolling[i], -200)) {
                        D.log('Content.infiniteScrolling', 'func');
                        infiniteScrolling[i].classList.add("loading");

                        if (infiniteScrolling[i].classList.contains('once-infinite-scrolling')) {
                            infiniteScrolling[i].classList.remove('once-infinite-scrolling');
                            infiniteScrolling[i].classList.add('never-infinite-scrolling');
                        }

                        Content.autoload.call(infiniteScrolling[i]);
                    }
                }
            }


        },

        updateBanners: function () {

            if (/192.168.56.101|lotzon.com/.test(location.hostname)) {
                R.push('/banner/desktop/top');
                R.push('/banner/desktop/right');
                //R.push('/banner/desktop/fixed');
                //R.push('/banner/tablet/top');
                //R.push('/banner/tablet/bottom');
            }
        },

        after: {

            changeFilter: function (options) {

                D.log('Content.after.changeFilter', 'content');

                var name = null,
                    className = [];

                for (name in options.query) {
                    if (options.query.hasOwnProperty(name) && options.query[name] && name.indexOf('date') === -1) { /* skip unimportant filters */
                        className = [name, options.query[name]];
                        options.rendered.classList.add(className.join('-'));
                    }
                }

            },

            autoload: function (options) {

                D.log('Content.after.autoload', 'content');

                if (infiniteScrolling = options.node.parentNode.querySelector('.loading')) {
                    if (!Object.size(options.json) || infiniteScrolling.classList.contains('die-infinite-scrolling')) {
                        infiniteScrolling.parentNode.removeChild(infiniteScrolling);
                    } else {
                        infiniteScrolling.classList.remove('loading');
                    }
                }

                Content.infiniteScrolling();

            }
        },

        clearLoading: function () {

            var infiniteScrollingLoading = document.querySelectorAll('.infinite-scrolling.loading');
            if (infiniteScrollingLoading.length)
                for (var i = 0; i < infiniteScrollingLoading.length; i++)
                    infiniteScrollingLoading[i].classList.remove('loading')

            return this;

        },

        style: function () {

            if ((css = document.querySelector("link[href='/res/css/screen/style.css']"))
                || (css = document.querySelector("link[href='" + location.origin + "/res/css/screen/style.css']")))
                css.href = css.href.replace('screen', 'mobile');
            else if ((css = document.querySelector("link[href='/res/css/mobile/style.css']"))
                || (css = document.querySelector("link[href='" + location.origin + "/res/css/mobile/style.css']")))
                css.href = css.href.replace('mobile', 'screen');
        }

    };


})();