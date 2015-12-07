(function () {

    Form = {

        timeout: {
            remove: 3000,
            fadeout: 200,
            submit: 0
        },

        send: function (form) {

            var that = this;

            form.callback = U.parse(U.parse(form.action), 'tmpl');
            form.url = U.generate(form.action, form.method);

            /* FORM
             * method: GET | POST | PUT | DELETE
             * url: ACTION | HREF
             * data: JSON
             * callback: FUNCTION
             * */

            setTimeout(function () {
                $.ajax({
                    url: form.url,
                    method: /192.168.56.101/.test(location.hostname) ? "post" : form.method,
                    data: form.data,
                    dataType: 'json',
                    success: function (data) {

                        if ('responseText' in data) {

                            Form.stop.call(that);
                            D.error.call(that, 'SERVER RESPONSE ERROR: ' + form.url);

                        } else {

                            Form.stop.call(that)
                                .message.call(that, data.message);

                            if (data.player)
                                Player.init(data.player);

                            if (Callbacks[form.method][form.callback]) {
                                D.log(['C.' + form.method + '.callback']);
                                Callbacks[form.method][form.callback].call(that, data.res);
                            }

                            if (data.res)
                                Cache.update(data.res);

                        }

                    },
                    error: function (data) {
                        Form.stop.call(that);
                        D.error.call(that, (data.responseJSON.message || 'NOT FOUND') + "<br>" + form.url + '');
                    }
                })
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
                    callback = U.parse(U.parse(form.action), 'tmpl');

                D.log(['C.validate.' + callback]);

                if (form.nodeName === 'FORM') {

                    if (errorElements.length) {
                        $.each($(errorElements), function (index, element) {
                            // $(element).removeClass('error');
                        });
                        console.log(1);
                        valid = false;
                    }

                    if (filterRequiredElements.length) {

                        $.each($(filterRequiredElements), function (index, element) {
                            // $(element).parent().addClass('error');
                        });
                        console.log(2);
                        valid = false;
                    }

                    if (incompleteElements.length) {
                        $.each($(incompleteElements), function (index, element) {
                            // $(element).parent().addClass('error');
                        });
                        console.log(3);
                        valid = false;
                    }

                    if (Callbacks.validate[callback]) {
                        console.log(4);
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
                        action: form.action,
                        method: form.getAttribute('method'),
                        data: $(form).serializeObject()
                    },
                    formContenteditable = form.querySelectorAll("div[contenteditable='true']");

                D.log(['Form.submit.', form.action]);

                for (var i = 0; i < formContenteditable.length; i++) {
                    ajax.data[formContenteditable[i].getAttribute('name')] = formContenteditable[i].innerHTML;
                }

                event && event.preventDefault() && event.stopPropagation();
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
                formContenteditable = form.querySelectorAll("div[contenteditable='true']");

            // clear form after adding new entities
            if (form.getAttribute('method') === 'post' || form.getAttribute('method') === 'POST') {
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
                        modal && form && form.removeChild(modal);
                    }, Form.timeout.fadeout);
                },
                Form.timeout.remove);
        }

    }

})();