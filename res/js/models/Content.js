(function () {

    Content = {

        initDaterange: function () {

            var ranges = {};
            ranges[i18n("title-of-today")] = [moment(), moment()];
            ranges[i18n("title-of-yesterday")] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
            ranges[i18n("title-of-last-7-days")] = [moment().subtract(6, 'days'), moment()];
            ranges[i18n("title-of-last-30-days")] = [moment().subtract(29, 'days'), moment()];
            ranges[i18n("title-of-this-month")] = [moment().startOf('month'), moment().endOf('month')];

            D.log('Reports.init');
            if ($('.daterange')
                    .filter(':visible')
                    .filter(function () {
                        return !$(this).data('daterangepicker')
                    })
                    .daterangepicker({
                        "autoUpdateInput": false,
                        "alwaysShowCalendars": true,
                        "buttonClasses": "btn-flat",
                        "opens": "left",
                        "locale": {
                            "applyLabel": i18n("button-apply"),
                            "cancelLabel": i18n("button-cancel"),
                            "customRangeLabel": i18n("button-custom"),
                        },
                        "ranges": ranges
                    }).on('apply.daterangepicker', function (ev, picker) {
                        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY')).change();

                    }).length)
                Content.enableForm();

        },

        captcha: {

            render: function () {
                if(!DOM.byId('popup-captcha') && window['grecaptcha'])
                    R.push('popup/captcha');
            },

            init: function () {
                grecaptcha.render('popup-captcha-render', {
                    'sitekey': Config.captchaKey,
                    'callback': function (key) {
                        Form.post({
                            action: 'players/captcha',
                            data: {key: key}
                        })
                    }
                });
            },

            success: function () {
                DOM.remove(DOM.byId('popup-captcha'));
                Player.ping();
            }
        },

        badge: {

            close: function () {
                DOM.remove(DOM.up('.badge', this));
            },

            init: function (badges) {

                var types = ['notifications', 'messages', 'system'];

                for (var i = 0; i < types.length; i++) {
                    if (badges.hasOwnProperty(types[i]) && Object.size(badges[types[i]])) {
                        document.getElementById('badges-' + types[i]) &&
                        R.push({
                            template: 'badges-' + types[i] + '-list',
                            json: badges[types[i]].filter(function (el) {
                                return !document.getElementById('badges-' + types[i] + '-' + el.key + (el.id ? '-' + el.id : ''));
                            })
                        });
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
            if (!form || !form.classList.contains('render-list-form'))
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

        users4ping: function () {

            var userStatuses = DOM.visible('.user-status'),
                users = [],
                id = 0;

            if (userStatuses.length) {
                for (var i = 0; i < userStatuses.length; i++) {
                    id = userStatuses[i].getAttribute('data-user-id');
                    users[id] = id;
                }
            }

            return Tools.getArrayKeys(users);

        },

       updateStatuses: function (statuses) {
           if (statuses) {
               for (var id in statuses) {
                   if (statuses.hasOwnProperty(id)) {

                       var userStatuses = document.querySelectorAll('.user-status[data-user-id="' + id + '"]'),
                           online = Player.isOnline({id: id, ping: statuses[id]});

                       if (userStatuses.length) {
                           for (var i = 0; i < userStatuses.length; i++) {
                               switch (online){
                                   case true:
                                       userStatuses[i].classList.remove('offline');
                                       userStatuses[i].classList.add('online');
                                       break;
                                   case false:
                                       userStatuses[i].classList.add('offline');
                                       userStatuses[i].classList.remove('online');
                                       break;
                                   case null:
                                       userStatuses[i].classList.remove('online');
                                       userStatuses[i].classList.remove('offline');
                                       break;
                               }
                           }
                       }
                   }
               }
           }
           return true;
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
                'first_id': renderList && renderList.firstElementChild && parseInt(renderList.firstElementChild.getAttribute('data-id')) || null,
                'last_id' : renderList && renderList.lastElementChild && parseInt(renderList.lastElementChild.getAttribute('data-id')) || null
            });

            return res;

        },

        after: {

            changeFilter: function (options) {

                D.log('Content.after.changeFilter', 'content');

                var name = null,
                    className = [],
                    form = null;

                if (options.rendered && typeof options.rendered === 'object' && options.rendered.classList) {
                    for (name in options.query) {
                        if (options.query.hasOwnProperty(name) && options.query[name] && name.indexOf('date') === -1) { /* skip unimportant filters */
                            className = [name, options.query[name]];
                            options.rendered.classList.add(className.join('-').replace(/ /g,''));
                        }
                    }

                    if (form = options.rendered.getElementsByTagName('FORM')[0]) {
                        form.action = options.href.replace('container',(form.action.indexOf('list')!== -1 ? 'list' : ''));
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

        destroyPopBox: function () {

            DOM.remove(DOM.up('.pop-box', this));

        }
    };


})();