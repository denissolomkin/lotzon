(function () {

    Form = {

        websocketConnection: null,

        timeout: {
            remove : 3000,
            fadeout: 200,
            submit : 0
        },

        get: function (form) {
            form = Form.parseForm(form);
            form.method = 'get';
            Form.send.call(this, form)
        },

        post: function (form) {
            form = Form.parseForm(form);
            form.method = 'post';
            Form.send.call(this, form)
        },

        ws: function (form) {
            form = Form.parseForm(form);
            form.method = 'ws';
            Form.send.call(this, form)
        },

        put: function (form) {
            form = Form.parseForm(form);
            form.method = 'put';
            Form.send.call(this, form)
        },

        delete: function (form) {
            form = Form.parseForm(form);
            form.method = 'delete';
            Form.send.call(this, form)
        },

        parseForm: function (form) {
            if (typeof form === 'string')
                form = {
                    action: form,
                    method: 'get',
                    data  : null
                };
            return form;
        },

        send: function (form) {

            var that = this;
            form = Form.parseForm(form);
            if (form.href) {
                form.action = form.href;
            }

            form.callback = U.parse(U.parse(form.action), 'tmpl');
            form.url = U.generate(form.action, form.method);

            /* FORM
             * method: GET | POST | PUT | DELETE | WS
             * url: ACTION | HREF
             * data: JSON
             * callback: FUNCTION
             * */

            setTimeout(function () {

                console.log(form.data);

                if (form.method.toLowerCase() === 'ws') {

                    var conn = Form.websocketConnection,
                        path = form.url,
                        data = form.data;

                    if (!conn || conn.readyState !== 1) {

                        conn = new WebSocket(Config.websocketUrl);

                        conn.onopen = function (e) {
                            console.info('Socket open');
                            Form.websocketConnection = conn;
                            conn.send(JSON.stringify({'path': path, 'data': data}));
                        };

                        conn.onerror = function (e) {
                            var message = 'There was an un-identified Web Socket error';
                            Form.stop.call(that)
                                .message.call(that, message);
                            console.error(message);
                        };

                        conn.onmessage = function (e) {

                            data = JSON.parse(e.data);

                            if (data.error) {
                                Form.stop.call(that)
                                    .message.call(that, data.error);
                            } else {

                                sample = null;
                                path = data.path;
                                if (data.res) {

                                    if (data.res.app && data.res.app.uid && data.res.app.uid != App.uid) {
                                        App = {};
                                    } else if (App.winner) {
                                        App['winner'] = null;
                                        App['fields'] = null;
                                    }

                                    for (var key in data.res) {
                                        if (key !== 'app') {
                                            App[key] = data.res[key]
                                        }
                                    }

                                    if('app' in data.res){
                                        for (var key in data.res['app'])
                                            App[key] = data.res['app'][key]
                                        data = null;
                                    }

                                    Apps.playAudio([App.key, App.action]);
                                }

                                action = data && (data.res && data.res.action || path) || App.action;
                                console.log(path, action, data);

                                switch ('function') {

                                    /* Common Game Callback Action */
                                    case data && typeof Game.callback[action]:
                                        console.log('Game.callback.' + action);
                                        eval('Game.callback.' + action)(data);
                                        break;

                                    /* Specific App action */
                                    case App.key && typeof eval(App.key + '.' + action):
                                        console.log(App.key + '.' + action);
                                        eval(App.key + '.' + action)(data);
                                        break;

                                    /* Common Game Action */
                                    case !data && typeof Game.action[action]:
                                        console.log('Game.action.' + action);
                                        eval('Game.action.' + action)(data);
                                        break;

                                    /* Default App Action */
                                    case App.key && typeof eval(App.key + '.action'):
                                        console.log(App.key + '.action');
                                        eval(App.key + '.action')(data);
                                        break;
                                }

                                Form.stop.call(that)
                                    .message.call(that, data && data.message);
                            }
                        };

                    } else {
                        conn.send(JSON.stringify({
                            'path': path,
                            'data': data
                        }));
                    }

                } else {

                    $.ajax({
                        url     : form.url,
                        method  : /192.168.56.101/.test(location.hostname) && (form.method.toLowerCase() === 'delete' || form.method.toLowerCase() === 'put')
                            ? "post"
                            : form.method,
                        data    : form.data,
                        dataType: 'json',
                        success : function (data) {

                            if (data.hasOwnProperty('responseText')) {

                                Form.stop.call(that);
                                D.error.call(that, 'SERVER RESPONSE ERROR: ' + form.url);

                            } else {

                                form.json = data;

                                Form.stop.call(that)
                                    .message.call(that, data.message);

                                Cache.init(data);

                                if (Callbacks[form.method.toLowerCase()][form.callback]) {
                                    D.log(['C.' + form.method.toLowerCase() + '.callback']);
                                    Callbacks[form.method.toLowerCase()][form.callback].call(that, form);
                                }

                                if (form.hasOwnProperty('after') && typeof form.after === 'function') {
                                    form.after.call(that, form);
                                }
                            }

                        },

                        error: function (data) {

                            form.json = data.responseJSON || data;
                            Cache.init(form.json);

                            Form.stop.call(that);
                            D.error.call(that, data && (data.message || data.responseJSON && data.responseJSON.message || data.statusText) || 'NOT FOUND' + "<br>" + form.url);

                            if (Callbacks['error'][form.callback]) {
                                D.log(['C.error.callback']);
                                Callbacks['error'][form.callback].call(that, form);
                            }
                        }
                    })
                }
            }, Form.timeout.submit);

        },

        do: {

            validate: function (event) {

                var form = this;

                while (form.nodeName !== 'FORM')
                    form = form.parentNode;

                var submit = form.elements['submit'],
                    valid = true,
                    incompleteElements = form.querySelectorAll('.incomplete'),
                    errorElements = form.querySelectorAll('.error'),
                    requiredElements = form.querySelectorAll('.required'),
                    filterRequiredElements = Array.prototype.filter.call(requiredElements, Form.filterRequired),
                    callback = U.parse(U.parse(form.getAttribute('action')), 'tmpl');

                D.log(['C.validate.' + callback]);

                if (form.nodeName === 'FORM') {

                    if (errorElements.length) {
                        $.each($(errorElements), function (index, element) {
                            // $(element).removeClass('error');
                        });
                        valid = false;
                    }

                    if (filterRequiredElements.length) {

                        $.each($(filterRequiredElements), function (index, element) {
                            // $(element).parent().addClass('error');
                        });
                        valid = false;
                    }

                    if (incompleteElements.length) {
                        $.each($(incompleteElements), function (index, element) {
                            // $(element).parent().addClass('error');
                        });
                        valid = false;
                    }

                    if (Callbacks.validate[callback]) {
                        valid = !Callbacks.validate[callback].call(this, event) ? false : valid;
                    }

                }

                if (submit)
                    valid ? submit.classList.add('on') : submit.classList.remove('on');

                return valid;
            },

            submit: function (event) {

                var form = this;

                while (form.nodeName !== 'FORM')
                    form = form.parentNode;

                var button = form.elements['submit'],
                    ajax = {
                        action: form.getAttribute('action'),
                        method: form.getAttribute('method'),
                        data  : $(form).serializeObject()
                    },
                    formContenteditable = form.querySelectorAll("div[contenteditable='true']");

                D.log(['Form.submit.', form.action]);

                for (var i = 0; i < formContenteditable.length; i++) {
                    ajax.data[formContenteditable[i].getAttribute('name')] = formContenteditable[i].innerHTML;
                }

                if (event) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                Form.start.call(form, event);

                if (!button || button.classList.contains('on')) {
                    D.log('button.submit', 'info');
                    Form.send.call(form, ajax);
                }
            }
        },

        filterRequired: function (node) {

            var filter = true;

            switch (node.tagName) {

                case 'INPUT':

                    switch (node.type) {

                        case 'text':
                        case 'hidden':
                            filter = node.value === ''
                            || (node.classList.contains('float') && parseFloat(node.value) <= 0)
                            || (node.classList.contains('int') && parseInt(node.value) <= 0);
                            break;
                        case 'radio':
                            filter = node.form.querySelectorAll('[name="' + node.name + '"]:checked').length !== 1;
                            break;
                        case 'checkbox':
                            filter = node.form.querySelectorAll('[name="' + node.name + '"]:checked').length === 0;
                            break;
                    }

                    break;

                case 'DIV':
                    filter = node.innerHTML === '';
                    break;
            }

            return filter;

        },

        start: function (event) {

            var button = this.elements && this.elements['submit'];

            if (Form.do.validate.call(this, event)) {
                D.log('button.loading', 'info');
                button && button.classList.contains('on') && button.classList.add('loading') || this.classList.add('loading');
            }

            return Form;
        },

        stop: function () {

            if ('nodeType' in this) {
                var button = this.elements && this.elements['submit'];
                button && button.classList.contains('loading') && button.classList.remove('loading') || this.classList.remove('loading');
            } else {
                // DOM.all('button.loading').removeClass('loading');
            }

            return Form;
        },

        getTimeout: function (name) {
            return name
                ? (this.timeout.hasOwnProperty(name) ? this.timeout[name] : 0)
                : this.timeout.fadeout + this.timeout.remove;
        },

        message: function (message) {

            var form = this,
                formContenteditable;

            if (!DOM.isNode(form))
                return Form;

            // clear form after adding new entities
            if (form.tagName === 'FORM' && form.getAttribute('method').toLowerCase() === 'post') {
                formContenteditable = form.querySelectorAll("div[contenteditable='true']");
                form.reset();
                for (var i = 0; i < formContenteditable.length; i++) {
                    formContenteditable[i].innerHTML = '';
                }
            }

            if (!message)
                return Form;

            var modal = DOM.create('<div class="modal-message"><div>' + Cache.i18n(message) + '</div></div>');
            form.appendChild(modal);

            setTimeout(
                function () {
                    DOM.fadeOut(modal);
                    setTimeout(function () {
                        DOM.remove(modal);
                    }, Form.timeout.fadeout);
                },
                Form.timeout.remove);
        }

    };

    WebSocketAjaxClient = function (url, data) {
        Form.send({
            action: url,
            data  : data,
            method: 'ws'
        })
    };

})();