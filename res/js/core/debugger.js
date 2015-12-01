(function () {

    /* ========================================================= */
    //                        ENGINE
    /* ========================================================= */

    // Debugger
    D = {

        "config": {},

        "init": function (init) {

            D.log('Debugger.init', 'func');
            Object.deepExtend(this, init);
            /*
             $.ajaxSetup({
             error: function (xhr, status, message) {
             D.error.call(this,['AJAX Error: ', message]);
             }
             });
             */
            /**/
            window.onerror = function (message, url, line, col, error) {
             D.error([message, url, line]);
             return true;
             }
        },

        "log": function (log, type) {

            type = type || 'log';

            var d = new Date(),
                pre = '',
                output = '';

            if (D.isEnable(type)) {

                if (!console[type])
                    type = 'log';

                if (log && typeof log == 'object' && log.length) {

                    $.each(log, function (index, obj) {
                        if (obj)
                            output += obj && JSON.stringify(obj) && JSON.stringify(obj).replace(/"/g, "").substring(0, type == "error" ? 1000 : 40) + ' ';
                    });

                } else {
                    output = log && JSON.stringify(log).replace(/"/g, "").substring(0, type == "error" ? 1000 : 40);
                }

                console[type](d.toLocaleTimeString('ru-RU') + ' ' + pre + output);
            }

        },

        "isEnable": function (key) {

            return D["config"] && D["config"][key];

        },

        "error": function (message) {

            message = typeof message === 'object'
                ? message.join(' ')
                : message;

            D.log(message, 'error');
            D.isEnable("alert") && alert(message);

            //if (D.isEnable("clean"))
            //    $(".modal-error").remove();

            // $(".modal-loading").remove();

            Form.stop();
            R.isRendering && R.event('stop');

            //var box = $('.content-box:visible').length == 1 ? $('.content-box:visible').first() : $('.content-top:visible').first(),
            //    error = $('<div class="modal-error"><div><span>' + Cache.i18n('title-error') + '</span>' + message + '</div></div>'),
            //    buttons = null,
            //    errors = null;

            //box.append(error);



            if (this && this.node) {
                node = this.node;
            } else if (this && 'nodeType' in this) {
                node = this;
            } else {
                node = document.getElementById('content');
            }

            DOM.remove('.modal-loading', node);

            if(node.querySelector('.modal-error div')){
                DOM.append('<p>' + message + '<p>', node.querySelector('.modal-error div'));
            } else {
                DOM.append('<div onclick="this.parentNode.removeChild(this)" class="modal-error"><div class="animated zoomIn"><span>' + Cache.i18n('title-error') + '</span><p>' + message + '</p></div></div>', node);
            }

            if (0 && D.isEnable("clean"))
                if (errors = DOM.all(".modal-error"))
                    setTimeout(function () {
                        DOM.fadeOut(errors);
                        setTimeout(function () {
                            DOM.remove(errors);
                        }, 500)
                    }, 1000);

            return false;
        }
    };

})();