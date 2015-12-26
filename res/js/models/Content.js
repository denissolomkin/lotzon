(function () {

    Content = {

        initDaterange : function () {

            D.log('Reports.init');
            if ($('.daterange')
                    .filter(':visible')
                    .filter(function () {
                        return !$(this).data('daterangepicker')
                    })
                    .daterangepicker().length)
                Content.enableForm();

        },
        initDatepicker: function () {


        },
        enableForm    : function () {

            D.log('Content.enableForm', 'content');
            if (form = document.querySelector('form.render-list-form-unwatched')) {
                form.classList.remove('render-list-form-unwatched');
                form.classList.add('render-list-form');
            }

        },

        enableAutoload: function (event) {

            D.log(['Content.enableAutoload', 'content']);
            event.preventDefault();

            var submit = this.querySelector('button[type="submit"]:not(.loading)');
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

            if (form.elements['submit'])
                form.elements['submit'].classList.add("loading");

            try {

                var pingForm = Content.form4Ping.call(form);
                pingForm = pingForm[Object.keys(pingForm)[0]];

                if (event && event.type === 'change') {

                    R.push({
                        href : form.action.replace('list', 'container'),
                        json : {},
                        query: pingForm.query,
                        after: Content.after.changeFilter
                    });

                } else {

                    if (pingForm.first_id && pingForm.last_id) {
                        if (pingForm.first_id > pingForm.last_id)
                            pingForm.query.before_id = pingForm.last_id;
                        else
                            pingForm.query.after_id = pingForm.last_id;
                    }

                    if (pingForm.offset) {
                        pingForm.query.offset = pingForm.offset;
                        Object.deepExtend(pingForm.query.ping, pingForm);
                    }

                    R.push({
                        href : form.action,
                        query: pingForm.query,
                        after: Content.after.autoload
                    });
                }
            } catch (e) {
                D.error.call(form, e.message);
            }

        },

        infiniteScrolling: function () {

            var infiniteScrolling = DOM.visible([
                '.die-infinite-scrolling:not(.loading)',
                '.once-infinite-scrolling:not(.loading)',
                '.infinite-scrolling:not(.loading)'
            ]);

            if (infiniteScrolling.length) {

                for (var i = 0; i < infiniteScrolling.length; i++) {
                    if (Device.onScreen.call(infiniteScrolling[i], -200)) {
                        D.log('Content.infiniteScrolling', 'func');

                        if (infiniteScrolling[i].classList.contains('once-infinite-scrolling')) {
                            infiniteScrolling[i].classList.remove('once-infinite-scrolling');
                            infiniteScrolling[i].classList.add('never-infinite-scrolling');
                        }

                        Content.autoload.call(infiniteScrolling[i]);
                    }
                }
            }


        },

        forms4ping: function () {

            var renderForms = DOM.visible('.render-list-form:not(.track-disabled)'),
                parseForms = [];

            if (renderForms.length) {
                for (var i = 0; i < renderForms.length; i++) {
                    parseForms.push(Content.form4Ping.call(renderForms[i]));
                }
            }

            return parseForms;

        },

        form4Ping: function () {

            var renderList = document.getElementById(U.parse(this.action)) || this.parentNode.querySelector(".render-list"),
                query = $(this).serializeObject(),
                first_id = renderList && renderList.firstElementChild && renderList.firstElementChild.getAttribute('data-id') || null,
                last_id = renderList && renderList.lastElementChild && renderList.lastElementChild.getAttribute('data-id') || null,
                offset = renderList && renderList.childElementCount || null,
                timing = Cache.timing(U.parse(this.action)),
                res = [];

            res[U.parse(this.action)] = Object.filter({
                'query'   : Object.filter(query),
                'offset'  : offset,
                'timing'  : timing,
                'first_id': first_id,
                'last_id' : last_id
            });

            return res;

        },

        updateBanners: function () {

            if (/new.lotzon.com/.test(location.hostname)) {
                if (Device.mobile) {
                    R.push('/banner/tablet/top');
                    R.push('/banner/tablet/bottom');
                } else {
                    // R.push('/banner/desktop/fixed');
                    R.push('/banner/desktop/top');
                    R.push('/banner/desktop/right');
                }
            }
        },

        after: {

            changeFilter: function (options) {

                D.log('Content.after.changeFilter', 'content');

                var name = null,
                    className = [];

                if (options.rendered && typeof options.rendered === 'object' && options.rendered.classList)
                    for (name in options.query) {
                        if (options.query.hasOwnProperty(name) && options.query[name] && name.indexOf('date') === -1) { /* skip unimportant filters */
                            className = [name, options.query[name]];
                            options.rendered.classList.add(className.join('-'));
                        }
                    }

            },

            autoload: function (options) {

                D.log(['Content.after.autoload', options.node.id], 'content');

                if (infiniteScrolling = options.node.parentNode.querySelector('button.loading')) {
                    if (!Object.size(options.json)
                        || options.hasOwnProperty('lastItem')
                        || infiniteScrolling.classList.contains('die-infinite-scrolling')) {
                        DOM.remove(infiniteScrolling);
                    } else {
                        console.log(infiniteScrolling);
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
        },

        modal: function (message) {

            message = '<div class="modal-message"><div class="animated zoomIn"><p>' + Cache.i18n(message) + '</p></div></div>';
            DOM.append(message, this);

        }
    };


})();