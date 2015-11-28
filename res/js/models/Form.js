(function () {

    Form = {

        timeout: {
            remove: 3000,
            fadeout: 200,
            submit: 0
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

                if(submit)
                    valid ? submit.classList.add('on') : submit.classList.remove('on');

                return valid;
            },

            submit: function (event) {

                console.log(this, event);

                var form = this;

                while (form.nodeName !== 'FORM')
                    form = form.parentNode;

                var button = form.elements['submit'],
                    formMethod = form.getAttribute('method'),
                    formUrl = U.generate(form.action, formMethod),
                    formCallback = U.parse(U.parse(form.action), 'tmpl'),
                    formData = $(form).serializeObject(),
                    formContenteditable = form.querySelectorAll("div[contenteditable='true']");

                D.log(['Form.submit.', form.action]);

                for (var i = 0; i < formContenteditable.length; i++) {
                    formData[formContenteditable[i].getAttribute('name')] = formContenteditable[i].innerHTML;
                }

                event && event.preventDefault() && event.stopPropagation();

                Form.start.call(form, event);

                if (!button || button.classList.contains('on')) {

                    D.log('button.submit', 'info');

                    setTimeout(function () {
                        $.ajax({
                            url: formUrl,
                            method: "post", // formMethod
                            data: formData,
                            dataType: 'json',
                            statusCode: {

                                404: function (data) {
                                    Form.stop.call(form);
                                    D.error.call(form, data.message || 'NOT FOUND: '+formUrl+'');
                                },

                                200: function (data) {

                                    if ('responseText' in data) {

                                        Form.stop.call(form);
                                        D.error.call(form,'SERVER RESPONSE ERROR: '+formUrl);

                                    } else {

                                        Form.stop.call(form)
                                            .message.call(form, data.message);

                                        if(data.player)
                                            Player.init(data.player);

                                        if (Callbacks[formMethod][formCallback]) {
                                            D.log(['C.' + formMethod + '.callback']);
                                            Callbacks[formMethod][formCallback].call(form, data.res);
                                        }

                                        if(data.res)
                                            Cache.update(data.res);

                                    }

                                },

                                201: function (data) {

                                    if ('responseText' in data) {

                                        Form.stop.call(form);
                                        D.error.call(form,'SERVER RESPONSE ERROR: '+formUrl);

                                    } else {

                                        Form.stop.call(form)
                                            .message.call(form, data.message);

                                        if(data.player)
                                            Player.init(data.player);

                                        if (Callbacks[formMethod][formCallback]) {
                                            D.log(['C.' + formMethod + '.callback']);
                                            Callbacks[formMethod][formCallback].call(form, data.res);
                                        }

                                        if(data.res)
                                            Cache.update(data.res);

                                    }

                                },

                                204: function (data) {
                                    Form.stop.call(form);
                                    throw (data.message);
                                },

                                405: function () {
                                    Form.stop.call(form);
                                    D.error.call(form,'METHOD NOT ALLOWED: '+formMethod+'');
                                }
                            }
                        })
                    }, Form.timeout.submit);
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
                            filter = node.form.querySelectorAll('[name="' + node.name + '"]:checked').length !== 1
                            break;
                        case 'checkbox':
                            filter = node.form.querySelectorAll('[name="' + node.name + '"]:checked').length === 0
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

            var button = this.elements['submit'];

            if (Form.do.validate.call(this, event)) {
                D.log('button.loading', 'info');
                button && button.classList.contains('on') && button.classList.add('loading') || this.classList.add('loading');
            }

            return Form;
        },

        stop: function () {

            if ('nodeType' in this) {
                var button = this.elements['submit'];
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
            if(form.getAttribute('method') === 'post' || form.getAttribute('method') === 'POST') {
                form.reset();
                for (var i = 0; i < formContenteditable.length; i++) {
                    formContenteditable[i].innerHTML = '';
                }
            }

            if (!message)
                return Form;

            var modal = DOM.create('<div class="modal-message"><div>' + Cache.i18n(message) + '</div></div>')[0];
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