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
        cashoutPopup: {
            init: function (cashout) {
            
                if (document.querySelector('#popup-cashout')) return;
                
                R.push({
                    template: 'popup-cashout',
                    json: cashout,
                    after: function(e){
                        
                        // tmp
                        $('.ae-social').socialLikes({
                            url: 'https://lotzon.com/?ref=' + Player.id,
                            title: 'LOTZON. Выигрывает каждый!',
                            counters: false,
                            singleTitle: 'LOTZON. Выигрывает каждый!',
                            data: {
                                media: 'https://lotzon.com/res/img/lotzon_soc.jpg'
                            }
                        });
                        
                        $('.ae-social').on('popup_opened.social-likes', function(event, service, win) {
                            Content.cashoutPopup.sendStatus(cashout.id, 2);
                            document.querySelector('#popup-cashout').remove();
                        });

                        document.querySelector('#popup-cashout .close-pop-box').onclick = function(){
                            Content.cashoutPopup.sendStatus(cashout.id, 1);
                        }
                    }
                });
            },
            // в посте параметр status=1 (отказался) или status=2 (запостил)
            sendStatus: function(id, status, callback){
                
                if(!id || !status) return;
                $.ajax({
                        url: "/cashout/"+id+"/status ",
                        method: 'POST',
                        data: {
                            status: status
                        },
                        async: true,
                        dataType: 'json',
                        success: function(data) {
                            
                            if(data.tickets && data.tickets.filledTickets){
                                Tickets.filledTickets = data.tickets.filledTickets;
                                Tickets.renderTickets();
                                Tickets.upadteTabs();
                            }
                            
                        },
                        error: function() {
                            // 
                       }
                    });
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
        // .render-list .reCount - пересчитывает детей без рек-блоков
        form4Ping: function () {

            var renderList = document.getElementById(U.parse(this.action)) || this.parentNode.querySelector(".render-list"),
                key = U.parse(this.action).replace(/-list|-container/g, ''),
                res = {},
                reCount = renderList && renderList.className.indexOf('reCount') !== -1 ? true : false;
            

            res[key] = Object.filter({
                'query'   : Object.filter($(this).serializeObject()),
                'offset'  : ( reCount ? renderList.parentNode.querySelectorAll('.render-list > div:not(.addbox)').length : renderList.childElementCount ) || null,
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
            if($('.pop-box:visible').length === 1){
                var body = document.body;
                body.className = body.className.replace(/\bscrollLock\b/,'');
            }

            DOM.remove(DOM.up('.pop-box', this));

        },

        popup:{
            current:'',
            referer:'',

            init:function(){
                Content.popup.fixBody();
                // >>> check empty EMAIL input !! old class
                $('.pop-box .m_input').on('keyup', function() {
                    console.debug('>>> !! m_input length ', $.trim($(this).val().length));
                    var val = $.trim($(this).val().length);
                    if (val > 0) {
                        $(this).closest('form').find('.sb_but').removeClass('disabled').prop('disabled', false);
                    } else {
                        $(this).closest('form').find('.sb_but').addClass('disabled').prop('disabled', true);
                    }
                });
            },
            
            onClose: function(callback){

                var target = document.querySelector('.close-pop-box');
                
                if(!target) return;

                target.onclick = function(e){

                    if (typeof callback === "function") {
                        callback(e);
                    }
                }


            },
            formError: function(form) {

                if (form) {
                    form.addClass('error');

                    setTimeout(function() {
                        form.removeClass('error');
                    }, 3000);

                    return true;
                }

                // console.log('>> form not found')
                return false;
            },

            // scrollLock - hide scrollbar
            fixBody:function(){
                var body = document.body;
                if(body.className.indexOf("scrollLock") === -1){
                    body.className += " scrollLock";
                }
            },
            enter:function(){
                console.debug('>> popup >> enter');

                R.push({
                    template: 'popup-unregistred-enter',
                    after: function(){
                        Content.popup.fixBody();
                        Content.popup.referer = 'enter';
                        Content.popup.onClose(function(){
                            Content.popup.referer = '';
                            // re-init lottery
                            if($('#lottery-ticket:visible').length){
                                Lottery.init();
                            }
                        });
                    }
                });
            },
            login:function(){
                console.debug('>> popup >> login');

                R.push({
                    template: 'popup-unregistred-login',
                    after: function(){
                        Content.popup.init();

                        if(Content.popup.referer == 'enter'){   
                            $('#popup-unregistred-enter').remove();

                            Content.popup.onClose(function(){
                                    Content.popup.referer = '';
                                    Content.popup['enter']();    
                            });
                        }
                        // >>> toggle recover-pass
                        $('.login-box #rec-pass, .password-recovery-box .back-to').on('click', function() {

                            //restore form|msg
                            $('#pass-rec-form-success').hide();
                            $('form[name="rec-pass"]').show();

                            $('.password-recovery-box').toggle();
                            $('.login-box').toggle();

                        });

                        $('form[name="login"]').on('submit', function(e) {
                            var form = $(this);
                            var email = form.find('input[name="login"]').val();
                            var pwd = form.find('input[name="password"]').val();
                            var remember = form.find("#remcheck:checked").length ? 1 : 0;

                            Content.popup.do.loginPlayer({ 'email': email, 'password': pwd, 'remember': remember }, function(data) {

                                form.addClass('success');
                                document.location.href = "/";

                            }, function(data) {

                                Content.popup.formError(form);
                                form.find('.alert').text(data.message);

                            });

                            return false;
                        });


                        // >>> restore password
                        $('form[name="rec-pass"]').submit(function() {
                            var form = $(this);
                            var email = $(this).find('input[name="login"]').val();

                            Content.popup.do.resendPassword(email, function() {

                                form.find('input[name="login"]').val('');
                                // form.addClass('success');

                                form.hide();
                                $('#pass-rec-form-success').show();

                                setTimeout(function() {
                                    form.show();
                                    $('#pass-rec-form-success').hide();
                                    $('.password-recovery-box').hide();
                                    $('.login-box').show();
                                }, 5000);

                                form.removeClass('loading');
                            }, function(data) {
                                form.removeClass('loading');

                                Content.popup.formError(form);
                                form.find('.alert').text(data.message);

                            });

                            // event.preventDefault();
                            return false;
                        });

                    }
                });
            },
            error:function(){
                console.debug('>> popup >> error');

                R.push({
                    template: 'popup-unregistred-error',
                    after: function(){
                        Content.popup.fixBody();
                    }
                });
            },
            locked:function(){
                console.debug('>> popup >> locked');

                R.push({
                    template: 'popup-unregistred-locked',
                    after: function(){
                        Content.popup.fixBody();
                    }
                });
            },
            register:function(){
                console.debug('>> popup >> register');

                R.push({
                    template: 'popup-unregistred-registration',
                    after: function(){
                        Content.popup.init();
                        if(Content.popup.referer == 'enter'){   
                            $('#popup-unregistred-enter').remove();

                            Content.popup.onClose(function(){
                                    Content.popup.referer = '';
                                    Content.popup['enter']();    
                            });
                        }
                        // >>> registration handler
                        $('form[name="register"]').on('submit', function(e) {
                            console.debug('>>> registration handler');

                            var form = $(this);
                            var email = form.find('input[name="login"]').val();
                            var rulesAgree = 1; //form.find('#rulcheck').prop('checked') ? 1 : 0;
                            var ref = $(this).data('ref');

                            Content.popup.do.registerPlayer({ 'email': email, 'agree': 1, 'ref': ref }, function(data) {
                                console.debug('register success!!');

                                form.find('input[name="login"]').val(''); // resset value
                                form.addClass('success');

                                $('#popup-unregistred-registration').remove();

                                Content.popup.confirmEmail(email);
                                // // >>>> переписать на нормальный код ...как только время будет
                                // // go to next step // вывод окна с переотправки пароля
                                // form.hide();
                                // var compleetForm = $('form[name="email-send"]');
                                // compleetForm.show();
                                // compleetForm.find('.current-mail').text(email);

                                // $('form[name="email-send"] .back').on('click', function() {
                                //     $('form[name="email-send"]').hide();
                                //     $('form[name="register"]').show();
                                // });

                                // $('form[name="email-send"] a.resend').on('click', function() {
                                //     Content.popup.do.resendEmail(email, function() {
                                //         // some callback
                                //     }, function(data) {
                                //         // some error
                                //     });
                                // });

                            }, function(data) {
                                console.debug('register error!!');

                                Content.popup.formError(form);
                                form.find('.alert').text(data.message);

                            });

                            return false;
                        });
                    }
                });
            },
            confirmEmail:function(email){
                console.debug('>> popup >> confirmEmail');

                R.push({
                    template: 'popup-unregistred-confirm-email',
                    after: function(){
                        Content.popup.init();
                        Content.popup.onClose(function(){
                            Content.popup['register']();
                        });

                        var compleetForm = $('form[name="email-send"]');
                        compleetForm.find('.current-mail').text(email);

                        $('form[name="email-send"] .back-to').on('click', function() {
                            $('#popup-unregistred-confirm-email').remove();
                            Content.popup.register();
                        });

                        $('form[name="email-send"] a.resend').on('click', function() {
                            Content.popup.do.resendEmail(email, function() {
                                // some callback
                            }, function(data) {
                                // some error
                            });
                        });

                    }
                });
            },
            changeEmail:function(){
                console.debug('>> popup >> changeEmail');

                R.push({
                    template: 'popup-unregistred-change-email',
                    after: function(){
                        Content.popup.init();
                        Content.popup.onClose(function(){
                            Content.popup['register']();
                        });
                    }
                });
            },
            socialExist:function(){
                console.debug('>> popup >> socialExist');

                R.push({
                    template: 'popup-unregistred-social-exist',
                    after: function(){
                        Content.popup.init();
                        Content.popup.onClose(function(){
                            Content.popup['register']();
                        });
                    }
                });
            },
            rules:function(){

                console.debug('>> popup >> rules');
                
                R.push({
                    template: 'popup-unregistred-rules',
                    after: function(){
                        R.push({
                            template: 'common-rules',
                            href: '/res/rules/' + Player.language.current,
                            after: function(){
                                Content.popup.fixBody();
                            }
                        });
                    }
                });
            },

            do:{
                loginPlayer: function(authData, successFunction, failFunction, errorFunction){
                    $.ajax({
                        url: "/players/login/",
                        method: 'POST',
                        data: authData,
                        async: true,
                        dataType: 'json',
                        success: function(data) {
                            if (data.status == 1) {
                                successFunction.call(authData, data);
                            } else {
                                failFunction.call(authData, data);
                            }
                        },
                        error: function() {
                            errorFunction.call(authData, data);
                       }
                    });
                },

                registerPlayer: function(playerData, successFunction, failFunction, errorFunction)
                {
                    $.ajax({
                        url: "/players/register/",
                        method: 'POST',
                        data: playerData,
                        async: true,
                        dataType: 'json',
                        success: function(data) {
                            if (data.status == 1) {
                                successFunction.call(playerData, data);
                            } else {
                                failFunction.call(playerData, data);
                            }
                        },
                        error: function() {
                            errorFunction.call(playerData, data);
                       }
                    });
                },
                resendPassword: function(email, successFunction, failFunction, errorFunction)
                {
                    $.ajax({
                        url: "/players/resendPassword",
                        method: 'POST',
                        data: {
                            email: email
                        },
                        async: true,
                        dataType: 'json',
                        success: function(data) {
                            if (data.status == 1) {
                                successFunction.call($(this), data);
                            } else {
                                failFunction.call($(this), data);
                            }
                        },
                        error: function() {
                            errorFunction.call($(this), data);
                       }
                    });
                },
                resendEmail: function(email, successFunction, failFunction, errorFunction)
                {
                    $.ajax({
                        url: "/players/resendEmail",
                        method: 'POST',
                        data: {
                            email: email
                        },
                        async: true,
                        dataType: 'json',
                        success: function(data) {
                            if (data.status == 1) {
                                successFunction.call($(this), data);
                            } else {
                                failFunction.call($(this), data);
                            }
                        },
                        error: function() {
                            errorFunction.call($(this), data);
                       }
                    });
                }

            }


        }

    };


})();