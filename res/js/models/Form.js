(function () {

    Form = {

        timeout: {
            remove: 3000,
            fadeout: 200,
            submit: 0
        },

        do: {

            validate: function (event) {

                var $form = $(this).closest('form'),
                    $incompleteElements = $('.incomplete', $form),
                    $errorElements = $('.error', $form),
                    $requiredElements = $('.required', $form).filter(Form.filterRequired),
                    valid = true,
                    callback = U.parse(U.parse($form.attr('action')), 'tmpl');

                D.log(['C.validate.' + callback]);

                if ($form.length) {

                    if ($errorElements.length) {
                        $.each($errorElements, function (index, element) {
                            // $(element).removeClass('error');
                        })
                    }

                    if ($requiredElements.length) {

                        $.each($requiredElements, function (index, element) {
                            // $(element).parent().addClass('error');
                        })

                        valid = false;
                    }

                    if ($incompleteElements.length) {
                        $.each($incompleteElements, function (index, element) {
                            // $(element).parent().addClass('error');
                        })

                        valid = false;
                    }


                    if (Callbacks.validate[callback]) {
                        valid = !Callbacks.validate[callback].call(this, event) ? false : valid;
                    }

                }

                valid
                    ? $('button[type="submit"]', $form).addClass('on') : $('button[type="submit"]', $form).removeClass('on');

                return valid;
            },

            submit: function (event) {

                var button = this,
                    form = button.form,
                    formMethod = form.getAttribute('method'),
                    formUrl = U.generate(form.action, formMethod),
                    formCallback = U.parse(U.parse(form.action), 'tmpl'),
                    formData = $(form).serializeObject(),
                    formContenteditable = form.querySelectorAll("div[contenteditable='true']");

                for(var i = 0; i<formContenteditable.length;i++){
                    formData[formContenteditable[i].getAttribute('name')] = formContenteditable[i].innerHTML;
                }

                event.preventDefault();
                event.stopPropagation();

                Form.start.call(button, event);

                if (button.classList.contains('on')) {

                    D.log('button.submit', 'info');

                    setTimeout(function () {
                        $.ajax({
                            url: formUrl,
                            method: formMethod,
                            data: formData,
                            dataType: 'json',
                            statusCode: {

                                404: function (data) {
                                    D.error.call(form, data.message || 'NOT FOUND');
                                },

                                200: function (data) {

                                    if ('responseText' in data) {

                                        D.error('SERVER RESPONSE ERROR');

                                    } else {

                                    Form.stop.call(button)
                                        .message.call(form, data.message);

                                    if (Callbacks[formMethod][formCallback]) {
                                        D.log(['C.' + formMethod + '.callback']);
                                        Callbacks[formMethod][formCallback].call(form, data.res);
                                    }

                                    console.log(data.res);
                                    Cache.update(data.res);

                                    }

                                },

                                201: function (data) {
                                    throw (data.message);
                                },

                                204: function (data) {
                                    throw (data.message);
                                },

                                405: function () {
                                    D.error('METHOD NOT ALLOWED');
                                }
                            }
                        })
                    }, Form.timeout.submit);
                }
            }
        },

        filterRequired: function () {

            var name = $(this).attr('name'),
                type = $(this).attr('type'),
                filter = true,
                $form = $(this).closest('form');

            switch (this.tagName){

                case 'INPUT':

                    switch (type) {
                        case 'text':
                        case 'hidden':
                            filter = $(this).val() === '' || ($(this).hasClass('float') && parseFloat($(this).val()) <= 0) || ($(this).hasClass('int') && parseInt($(this).val()) <= 0);
                            break;
                        case 'radio':
                            filter = $('[name="' + name + '"]', $form).filter(':checked').length !== 1
                            break;
                        case 'checkbox':
                            filter = $('[name="' + name + '"]', $form).filter(':checked').length === 0
                            break;

                    }

                    break;

                case 'DIV':
                    filter = this.innerHTML === '';
                    break;
            }

            return filter;

        },

        start: function (event) {

            if (Form.do.validate.call(this, event)) {
                D.log('button.loading', 'info');
                this.classList.contains('on') && this.classList.add('loading');
            }

            return Form;
        },

        stop: function () {

            if ('nodeType' in this) {
                this.classList.remove('loading');
            } else {
                // DOM.all('button.loading').removeClass('loading');
            }

            return Form;
        },

        getTimeout: function () {
            return this.timeout.fadeout + this.timeout.remove;
        },

        message: function (message) {

            var form = this,
                formContenteditable = form.querySelectorAll("div[contenteditable='true']");

            form.reset();

            for(var i = 0; i<formContenteditable.length;i++){
                formContenteditable[i].innerHTML = '';
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