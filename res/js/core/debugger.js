$(function () {

    /* ========================================================= */
    //                        ENGINE
    /* ========================================================= */

    // Debugger
    D = {

        "isEnabled": {
            "info": true,
            "warn": true,
            "error": true,
            "log": true,
            "clean": true
        },

        "init": function () {

            $.ajaxSetup({
                error: function (xhr, status, message) {
                    D.error(['AJAX Error: ', message]);
                }
            });

            window.onerror = function (message, url, line, col, error) {
                D.error([message, url, line]);
                return true;
            }
        },

        "log": function (log, type) {

            type = type || 'log';

            if (D.isEnabled[type]) {
                var d = new Date();

                var output = '';

                if (log && typeof log == 'object' && log.length) {

                    $.each(log, function (index, obj) {
                        if (obj)
                            output += obj && JSON.stringify(obj) && JSON.stringify(obj).replace(/"/g, "").substring(0, type == "error" ? 1000 : 40) + ' ';
                    });

                } else {
                    output = log && JSON.stringify(log).replace(/"/g, "").substring(0, type == "error" ? 1000 : 40);
                }

                console[type](d.toLocaleTimeString('ru-RU') + ' ' + output);
            }

        },

        "error": function (message) {

            message = typeof message === 'object'
                ? message.join(' ')
                : message;

            D.log(message, 'error');
            alert(message);

            if (D.isEnabled.clean)
                $(".modal-error").remove();

            $(".modal-loading").remove();

            Form.stop();
            R.event.stop();

            var box = $('.content-box:visible').length == 1 ? $('.content-box:visible').first() : $('.content-top:visible').first(),
                error = $('<div class="modal-error"><div><span>' + M.i18n('title-error') + '</span>' + message + '</div></div>'),
                buttons = null,
                errors = null;

            box.append(error);

            if (D.isEnabled.clean)
                if (errors = $(".modal-error"))
                    setTimeout(function () {
                        errors.fadeOut(500);
                        setTimeout(function () {
                            errors.remove();
                        }, 500)
                    }, 1000);

            return false;
        }
    };

});