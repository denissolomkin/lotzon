$(function () {

    Form = {

        validate: function () {

            var $form = $(this).closest('form');

            if ($form) {

                var $incomplete = $('.incomplete', $form),
                    $required = $('.required', $form).filter(function () {
                        return $(this).val() === '' || $(this).val() === 0;
                    });

                if ($required.length || $incomplete.length) {

                    $.each($required, function(index, element){
                        $(element).parent().addClass('error');
                    })

                    $.each($incomplete, function(index, element){
                        $(element).parent().addClass('error');
                    })

                    $('button[type="submit"]', $form).removeClass('on');
                    return false;

                } else {

                    $('button[type="submit"]', $form).addClass('on');
                    return true;
                }

            } else
                return true;
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

            var $button = this;
            var $status = $('<div class="status">' + M.i18n(message) + '</div>');

            $button.fadeOut(200).delay(2400).fadeIn(200);
            $status.delay(200).insertAfter($button).fadeIn(200).delay(2000).fadeOut(200, function () {
                $(this).remove()
            });
        },

        submit: function (event) {

            var button = this,
                $button = $(button),
                $form = $button.closest('form'),
                formData = $form.serializeObject(),
                formUrl = $form.attr('action');

            event.preventDefault();
            Form.start.call(button);

            if ($button.hasClass('on')) {

                D.log('button.submit', 'info');
                $.post(
                    U.Generate.Post(formUrl),
                    formData,
                    function (data) {
                        if (data.status == 1) {

                            Form.stop.call($button)
                                .message.call($button, data.message);

                            formUrl = U.Parse.Undo(formUrl);

                            if (C['post'][formUrl]) {
                                D.log(['C.post.callback']);
                                C['post'][formUrl](data.res);
                            }

                        } else {
                            throw(data.message);
                        }

                    }, "json");
            }
        }

    }

});