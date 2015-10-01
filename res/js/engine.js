$(function () {

    /* ========================================================= */
    //                        ENGINE
    /* ========================================================= */

    // Debugger
    D = {

        "Enable": false,

        "init": function(){

            $.ajaxSetup({
                error: function (xhr, status, message) {
                    throw('An AJAX error occured: ' + status + "\nError: " + message);
                }
            });

            window.onerror = function (message, url, line, col, error) {

                /* err(message, url);
                 return; */

                D.log([message, url, line],'error');

                if ($Button) {
                    $Button.data('disabled', false);
                    $Button = null;
                }

                $(".error").remove();
                $(".loading").remove();

                $Error = $('<div class="error"><div><span>' + M.i18n('title-error') + '</span>' + message + '</div></div>');


                if (!$Box)
                    $Box = $('.content-box').length == 1 ? $('.content-box') : $('.content-top');

                $Box.append($Error);

                if ($Errors = $(".error"))
                    setTimeout(function () {
                        $Errors.fadeOut(500);
                        setTimeout(function () {
                            $Errors.remove();
                        }, 500)
                    }, 1000);

                R.empty();

                return true;
            }
        },

        "log": function (log, type) {

            if (D.Enable) {

                type = type || 'log';
                var d = new Date();

                var output = '';

                if (typeof log == 'object' && log.length) {

                    $.each(log, function (index, obj) {
                        if (obj)
                            output += JSON.stringify(obj).replace(/"/g, "").substring(0, "type" == "error" ? 100 : 40) + ' ';
                    });

                } else {
                    output = JSON.stringify(log).replace(/"/g, "").substring(0, "type" == "error" ? 100 : 40);
                }


                console[type](d.toLocaleTimeString('ru-RU') + ' ' + output);
            }

        },

        "error": function (message, trace) {

            if ($Button) {
                $Button.data('disabled', false);
                $Button = null;
            }

            $(".error").remove();
            $(".loading").remove();

            $Error = $('<div class="error"><div><span>' + M.i18n('title-error') + '</span>' + message + '</div></div>');


            if (!$Box)
                $Box = $('.content-box').length == 1 ? $('.content-box') : $('.content-top');

            $Box.append($Error);

            if ($Errors = $(".error"))
                setTimeout(function () {
                    $Errors.fadeOut(500);
                    setTimeout(function () {
                        $Errors.remove();
                    }, 500)
                }, 1000);

            R.empty();
            return false;
        }
    };

    // Render Handler
    R = {

        "Cache": {},
        "Templates": {},
        "Render": [],
        "Path": [],
        "IsRendering": false,

        "init": function () {

            D.log(['init']);
            D.init();
            R.empty();

            R.Path = window.location.pathname.split('/');
            R.Path[1] = R.Path[1] || 'blog';
            $('[href="/' + R.Path[1] + '"]').click();

        },

        "render": function (options) {

            try {
                if (!options) options = {};

                if (!options.template)
                    options.template = U.Parse.Undo($Template || $Href || $Tab.attr('href'));
                if (!options.href)
                    options.href = U.Parse.Undo($Href || options.template);
                if (!options.json)
                    options.json = $JSON || false;
                if (!options.callback)
                    options.callback = $Callback;

                R.empty('soft');

                D.log(['render.push:', options.template, options.href, options.json], 'info');

                R.Render.push({
                    'options': {
                        'box':      $Box,
                        'tab':      $Tab,
                        'callback': options.callback,
                        'this':     options.template
                    },
                    'url':      options.url,
                    'template': options.template,
                    'href':     options.href,
                    'json':     options.json
                });

                if (!R.IsRendering)
                    R.rendering();

            } catch (error) {
                throw(error);
            }
        },

        "rendering": function () {

            try {
                while (R.Render.length) {

                    R.IsRendering = true;

                    var render = R.Render.shift();

                    R.loading(render.options);

                    D.log(['rendering.run:', render.template, render.href, render.json], 'info');

                    if (render.url !== false)
                        U.Update(typeof render.href != 'object' ? render.href : render.template);

                    if (typeof render.json == 'object') {
                        D.log(['JSON from Object:', render.json]);
                        R.renderTMPL(render.template, render.json, render.options);
                    } else {
                        R.renderJSON(render.href, render.template, render.options);
                    }
                }

                R.stop();

            } catch (error) {
                throw(error);
            }
        },

        "renderJSON": function (href, template, options) {

            try {
                var json = null;
                D.log(['renderJSON:', href]);

                if (json = R.cache(href)) {

                    D.log(['JSON from Cache:', json]);
                    R.renderTMPL(template, json, options);

                } else {

                    $.getJSON(U.Generate.Json(href), function (response) {
                        if (response.status == 1) {

                            json = R.cache(href, response.res);
                            D.log(['JSON from AJAX:', json], 'warn');
                            R.renderTMPL(template, json, options);

                        } else {

                            D.error(response.message);

                        }
                    });
                }

            } catch (error) {
                throw(error);
            }
        },

        "renderTMPL": function (template, json, options) {

            try {
                template = U.Parse.Tmpl(template);
                D.log(['renderTMPL:', template]);

                if ($('.template.' + template).length) {

                    D.log(['TMPL already in DOM', template]);
                    R.renderHTML(template, json, options);

                } else if (R.Templates[template]) {

                    template = R.Templates[template];
                    D.log(['TMPL from Cache', template]);
                    R.renderHTML(template, json, options);

                } else if ($('#tmpl-' + template).length) {

                    template = R.Templates[template] = $('#tmpl-' + template).html();
                    D.log(['TMPL from HTML:', template]);
                    R.renderHTML(template, json, options);

                } else {
                    $.get(U.Generate.Tmpl(template), function (data) {

                        if (!$(data).attr('class')) {
                            throw("Format Template Error");
                        } else {
                            template = R.Templates[template] = data;
                            D.log(['TMPL from AJAX:', template], 'warn');
                            R.renderHTML(template, json, options);
                        }

                    });
                }
            } catch (error) {
                throw(error);
            }
        },

        "renderHTML": function (template, json, options) {

            try {

                D.log(['renderHTML:', template, json]);
                var rendered = null;

                if (typeof json != 'object') {

                    D.log('Rendered with HTML');
                    rendered = $($('.template.' + template)[0].outerHTML).html(json);

                } else {

                    D.log('Rendered with Template');
                    Mustache.parse(template);   // optional, speeds up future uses
                    rendered = Mustache.render(template, $.extend({"i18n": M.i18n}, json));

                }

                R.inputHTML(rendered, options);
            } catch (error) {
                throw(error);
            }
        },

        "inputHTML": function (rendered, options) {

            try {

                D.log(['inputHTML into:', (typeof options.box == 'object' ? options.box.attr('class') : options.box)]);
                var findClass = '.' + $(rendered).attr('class').replace(/ /g, '.');

                if (options.box) {

                    $(' > div', options.box).hide();

                    if ($(findClass, options.box).length) {

                        $(findClass, options.box).html($(rendered).html()).show();

                    } else {

                        options.box.append(rendered).find(findClass).hide().show();

                    }

                }

                if (options.callback) {
                    D.log(['callback']);
                    options.callback(rendered, findClass);
                }

                if (C[options.this]) {
                    D.log(['C.callback']);
                    C[options.this](rendered, findClass);
                }

                /* tickets functionality */
                if ($('.ticket-items', $(rendered)).length && !$('.ticket-items li.active').length)
                    renderTicket();

                /* parent box functionality after rendering */
                if (options.box) {

                    /* if new box has tabs */
                    if ($($Tabs, options.box).filter(":visible").length) {

                        /* click on unactive tab */

                        if (!$($Tabs, options.box).filter(".active:visible").length) {
                            D.log(['clickTab:', $($Tabs, options.box).not(".active").filter(":visible").first().attr('href')]);
                            $($Tabs, options.box).not(".active").filter(":visible").first().click();
                        }

                    }

                    /* tab functionality after click on tab */
                    if (options.tab) {

                        $('.active', options.tab.parent().parent()).removeClass('active');

                        if ($($Cats, options.tab.parents('.content-box')).filter(":visible").length) {
                            D.log(['clickCat:', $($Cats, options.box).first().attr('href')]);
                            $($Cats, options.tab.parents('.content-box')).first().click();
                        }

                        options.tab.addClass('active');
                    }

                }

                R.loaded(options);
                R.empty();

            } catch (error) {
                throw(error);
            }
        },

        "loading": function (options) {
            if (options.box)
                $('.loading', options.box).length ? $('.loading', options.box).show() : options.box.append('<div class="loading"><div></div></div>');
        },

        "loaded": function (options) {
            if (options.box)
                $('.loading', options.box).length ? $('.loading', options.box).remove() : '';
        },

        "cache": function (key, data) {

            $.each(key.split('-'), function(i,v){
                console.log(v);
            });

            if (key && R.Cache[key]) {
                return R.Cache[key];
            } else if (data && !data['nocache']) {
                return R.Cache[key] = data;
            } else if (data) {
                return data;
            } else
                return false;
        },

        "empty": function (mode) {

            D.log('empty.' + (mode ? mode : 'hard'));
            $Template = $Href = $JSON = $Callback = null;
            if (!mode) $Tab = $Box = $Button = null;

        },

        "stop": function () {

            D.log('rendering.stop');
            R.IsRendering = false;

        }
    };


    // Multilingual User Interface
    M = {

        "i18n": function (key) {
            return key ? (M.Texts[key] ? M.Texts[key] : key) : (function (key) {
                return M.i18n(key);
            });
        },

        "Texts": {
            "title-ticket": "Билет",
            "message-autocomplete": "АВТОЗАПОЛНЕНИЕ",
            "message-done-and-approved": "<b>ПОДТВЕРЖДЕН И ПРИНЯТ К РОЗЫГРЫШУ</b>",
            "message-numbers-yet": "ЕЩЕ <b></b> НОМЕРОВ",
            "message-favorite": "<b>ЛЮБИМАЯ КОМБИНАЦИЯ</b><span>настраивается в кабинете</span>",
            "button-add-ticket": "Подтвердить",
            "title-prizes-draw": "Розыгрыш призов",
            "title-limited-quantity": "Ограниченное количество",
            "title-pieces": "шт.",
            "title-games-online": "Игры Онлайн",
            "title-games-chance": "Шансы",
            "title-games-rating": "Рейтинг",
            "title-error": "Ошибка",
        }
    };

    // URL Handler
    U = {

        "Path": {

            "Json": "/res/json/",
            "Ajax": "/res/json/",
            "Tmpl": "/res/tmpl/"

        },

        "Generate": {
            "Ajax": function (url) {
                return U.Path.Ajax + U.Parse.Url(url);
            },

            "Json": function (url) {
                return U.Path.Json + U.Parse.Url(U.Parse.Json(url));
            },

            "Tmpl": function (url) {
                return U.Path.Tmpl + U.Parse.Url(url) + '.html';
            }
        },

        "Parse": {
            "Url": function (url) {
                return url.replace(/-/g, '/');
            },

            "Tmpl": function (url) {
                return url.replace(/-\d+/g, '-view');
            },

            "Json": function (url) {
                return url.replace(/-view/g, '');
            },

            "Undo": function (url) {
                if (typeof url == 'object') {
                    return url;
                } else {
                    url = url.replace(/^\//, "");
                    return url.replace(/\//g, '-');
                }
            }
        },

        "Update": function (url) {
            if (url) {
                D.log(['updateURL:', url]);
                var stateObj = {foo: "bar"};
                history.pushState(stateObj, "page 2", '/' + U.Parse.Url(url));
            }
        },

        "isAnchor": function (url) {
            return (url.indexOf('#') == 0);
        }

    };


    /* ========================================================= */
    /* ========================================================= */


});