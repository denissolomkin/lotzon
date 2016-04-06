(function () {

    /* ========================================================= */
    //                        ENGINE
    /* ========================================================= */

    // Debugger
    D = {

        "config": {},
        "statBox": null,
        "lastError": null,

        "init": function (init) {

            Object.deepExtend(this, init);
            D.log('Debugger.init', 'func');

            if(this.isEnable('stat')){

                this.statBox = DOM.create('<div id="debug-stat-box"></div>');;
                DOM.append(this.statBox, document.getElementsByTagName('body')[0]);
            }
            /*
             $.ajaxSetup({
             error: function (xhr, status, message) {
             D.error.call(this,['AJAX Error: ', message]);
             }
             });
             */

            window.onerror = function (message, url, line, col, error) {
                D.debug([message, url, line]);
                return true;
            };
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
                            output += JSON.stringify(obj).replace(/"/g, "").substring(0, type == "error" ? 1000 : 100) + ' ';
                    });

                } else {
                    output = log && JSON.stringify(log).replace(/"/g, "").substring(0, type == "error" ? 1000 : 100);
                }

                console[type](d.toLocaleTimeString('ru-RU') + ' ' + pre + output);
            }

        },

        "stat": function (options) {

            if (this.isEnable('stat') && this.statBox) {

                stat = options.stat;

                if(typeof stat ==='object'){
                    var message = "<h2>"+ U.parse(options.href,'url')+" ";
                    for(part in stat)
                    {

                        if(!Object.size(Object.filter(stat[part])))
                            continue;

                        message += '<span style="display: none;">'+(part!=='total' ? part+': ' : '');


                        for(prop in stat[part])
                        {

                            switch (prop){
                                case "count":
                                    message += (stat[part]["count"])+' in ';
                                    break;
                                case "timer":
                                    if(stat[part]["timer"] < -1000)
                                        message += '<b>';
                                    message += (stat[part]["timer"]*-1)+'ms ';
                                    if(stat[part]["timer"] < -1000)
                                        message += '</b>';
                                    break;
                                case "size":
                                    message += (stat[part]["size"])+'bytes ';
                                    break;

                            }

                        }

                        if(part!=='total')
                            message += '</span>';
                        else
                            message += '</span></h2>';
                    }
                }

                DOM.prepend('<div onclick="DOM.toggle(\'span\',this)">'+message+'</div>', this.statBox);
            }
        },

        "isEnable": function (key) {

            return D["config"] && D["config"][key];

        },

        "debug": function (data) {

            Form.stop();
            R.event('error');

            if(data[0] === 'Script error.' || data[0] === 'Script error' || data[0] === D.lastError)
                return false;
            else
                D.lastError = data[0];

            D.isEnable("dev") && Content.badge.init({
                system:
                    [{
                        uid: Math.random().toString(16).slice(2),
                        message: message,
                        timer: 3,
                        timeout: 'close'
                    }]
                });

            Form.post({
                action: 'debug',
                data: {
                    log: data[0],
                    url: data[1],
                    line: data[2]
                }
            });

            return false;
        },

        "error": function (message) {


            if(typeof message === 'object') {

                if (message[0] === 'Unauthorized') {
                    location.reload();
                    return false;
                } else {
                    message[0] = i18n(message[0]);
                    message = message.join('<br>');
                }
            }

            if (message === 'error')
                return;

            D.log(message, 'error');
            D.isEnable("alert") && alert(message);

            //if (D.isEnable("clean"))
            //    $(".modal-error").remove();

            // $(".modal-loading").remove();

            Form.stop();
            R.event('error');

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

            if (node.querySelector('.modal-error div')) {
                DOM.append('<p>' + message + '<p>', node.querySelector('.modal-error div'));
            } else {
                DOM.append('<div onclick="this.parentNode.removeChild(this)" class="modal-error"><div class="animated zoomIn"><span>' + Cache.i18n('title-error') + '</span><p>' + message + '</p></div></div>', node);
            }


            if(node.classList.contains('loading'))
                node.classList.remove('loading');

            DOM.removeClass('loading',
                Array.prototype.filter.call(
                    document.getElementsByTagName('button'),
                    function (el) { return el.classList.contains('loading');}
                ));

            if (0 && D.isEnable("clean"))
                if (errors = DOM.all(".modal-error"))
                    setTimeout(function () {
                        DOM.fadeOut(errors);
                        setTimeout(function () {
                            DOM.remove(errors);
                        }, 500);
                    }, 1000);

            return false;
        }
    };

})();