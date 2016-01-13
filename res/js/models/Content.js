(function () {

    Content = {

        initDaterange: function () {

            D.log('Reports.init');
            if ($('.daterange')
                    .filter(':visible')
                    .filter(function () {
                        return !$(this).data('daterangepicker')
                    })
                    .daterangepicker().length)
                Content.enableForm();

        },

        banner: {

            moment: function (data) {

                if (Device.isMobile())
                    return;

                if (data.json.hasOwnProperty('block') && data.json.block) {

                    var node = data.hasOwnProperty('node') && data.node.getElementsByClassName('ad')[0];
                    if (node) {

                        var div = document.createElement('div');
                        div.innerHTML = data.json.block;

                        while (div.children.length > 0) {
                            if (div.children[0].tagName === 'SCRIPT') {
                                var s = document.getElementsByTagName('script')[0],
                                    po = document.createElement('script');
                                po.type = 'text/javascript';
                                po.async = true;

                                if (div.children[0].src) {
                                    po.src = div.children[0].src;
                                } else {
                                    po.innerHTML = div.children[0].innerHTML;
                                }

                                div.removeChild(div.children[0]);
                                s.parentNode.insertBefore(po, s);

                            } else {
                                node.appendChild(div.children[0]);
                            }

                        }
                    }
                }
            }
        },

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
                if (event && event.type === 'change') {

                    R.push({
                        href : form.action.replace('list', 'container'),
                        json : {},
                        query: Object.filter($(form).serializeObject()),
                        after: Content.after.changeFilter
                    });

                } else {

                    var pingForm = Content.form4Ping.call(form);
                    pingForm = {'ping': pingForm[Object.keys(pingForm)[0]]};
                    var query = pingForm.ping.query;

                    if (pingForm.ping.first_id && pingForm.ping.last_id) {
                        if (pingForm.ping.first_id > pingForm.ping.last_id)
                            query.before_id = pingForm.ping.last_id;
                        else
                            query.after_id = pingForm.ping.last_id;
                    }

                    if (pingForm.ping.offset) {
                        query.offset = pingForm.ping.offset;
                    }

                    if (form.classList.contains('track-disabled')) {
                        delete pingForm.ping;
                    }

                    R.push({
                        href : form.action,
                        query: query,
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
                parseForms = {};

            if (renderForms.length) {
                for (var i = 0; i < renderForms.length; i++) {
                    Object.deepExtend(parseForms, Content.form4Ping.call(renderForms[i]));
                }
            }

            return parseForms;

        },

        form4Ping: function () {

            var renderList = document.getElementById(U.parse(this.action)) || this.parentNode.querySelector(".render-list"),
                key = U.parse(this.action).replace(/-list|-container/g, ''),
                res = {};

            res[key] = Object.filter({
                'query'   : Object.filter($(this).serializeObject()),
                'offset'  : renderList && renderList.childElementCount || null,
                'timing'  : Cache.validate(key),
                'first_id': renderList && renderList.firstElementChild && renderList.firstElementChild.getAttribute('data-id') || null,
                'last_id' : renderList && renderList.lastElementChild && renderList.lastElementChild.getAttribute('data-id') || null
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

        },
        
        destroyPopBox: function(){
   
            DOM.remove(DOM.up('.pop-box',this));
    
        }
    };


})();