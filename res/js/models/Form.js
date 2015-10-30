$(function () {

    Form = {

        validate: function () {

            var $form = $(this).closest('form'),
                $incompleteElements = $('.incomplete', $form),
                $errorElements = $('.error', $form),
                $requiredElements = $('.required', $form).filter(Form.filterRequired),
                valid = true,
                callback = U.parse($form.attr('action'));

            D.log(['C.validate.'+callback]);

            if ($form.length) {

                if ($errorElements.length) {
                    $.each($errorElements, function(index, element){
                        // $(element).removeClass('error');
                    })
                }

                if ($requiredElements.length) {

                    $.each($requiredElements, function(index, element){
                        // $(element).parent().addClass('error');
                    })

                    valid = false;
                }

                if($incompleteElements.length){
                    $.each($incompleteElements, function(index, element){
                        // $(element).parent().addClass('error');
                    })

                    valid = false;
                }


                if (Callbacks.validate[callback]) {
                    valid = !Callbacks.validate[callback].call(this) ? false : valid;
                }

            }

            valid
                ? $('button[type="submit"]', $form).addClass('on')
                : $('button[type="submit"]', $form).removeClass('on');

            return valid;
        },

        filterRequired: function(){

            var name = $(this).attr('name'),
                type = $(this).attr('type'),
                filter = true,
                $form = $(this).closest('form');

            switch(type){
                case 'text':
                    filter = $(this).val() === ''
                        || ($(this).hasClass('float') && parseFloat($(this).val()) <= 0)
                        || ($(this).hasClass('int') && parseInt($(this).val()) <= 0);
                    break;
                case 'radio':
                    filter = $('[name="' + name + '"]', $form).filter(':checked').length !== 1
                    break;
                case 'checkbox':
                    filter =  $('[name="' + name + '"]', $form).filter(':checked').length === 0
                    break;

            }

            return filter;

        },

        start: function () {

            if (Form.validate.call(this)) {
                D.log('button.loading', 'info');
                $(this).hasClass('on') && $(this).addClass('loading');
            }

            return Form;
        },

        stop: function () {

            if (this instanceof jQuery) {
                this.removeClass('loading');
            } else
                $('button.loading').removeClass('loading');

            return Form;
        },

        message: function (message) {

            if(!message)
                return Form;

            var $button = this;
            var $status = $('<div class="status">' + M.i18n(message) + '</div>');

            $button.fadeOut(200).delay(2400).fadeIn(200);
            $status.delay(200).insertAfter($button).fadeIn(200).delay(2000).fadeOut(200, function () {
                $(this).remove()
            });
        },

        submit: function (event) {

            var $button = $(this),
                $form = $button.closest('form'),
                formData = $form.serializeObject(),
                formMethod = $form.attr('method'),
                formUrl = U.generate($form.attr('action'), formMethod),
                formCallback = U.parse($form.attr('action'));

            event.preventDefault();
            Form.start.call(this);

            if ($button.hasClass('on')) {

                D.log('button.submit', 'info');

                $.ajax({
                    url: formUrl,
                    method: formMethod, // 'post'
                    data: formData,
                    dataType: 'json',
                    statusCode: {

                        404: function(data) {
                            throw(data.message);
                        },

                        200: function(data) {

                            Form.stop.call($button)
                                .message.call($button, data.message);

                            if (Callbacks[formMethod][formCallback ]) {
                                D.log(['C.'+formMethod+'.callback']);
                                Callbacks[formMethod][formCallback ](data.res);
                            }

                        },

                        201: function(data) {
                            throw(data.message);
                        },

                        204: function(data) {
                            throw(data.message);
                        }
                    }
                });
            }
        }

    }

});