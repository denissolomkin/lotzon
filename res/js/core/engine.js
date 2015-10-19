$(function () {

    /* ========================================================= */
    //                        ENGINE
    /* ========================================================= */

    // Debugger
    D = {

        "Enabled": {
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

            if (D.Enabled[type]) {
                var d = new Date();

                var output = '';

                if (log && typeof log == 'object' && log.length) {

                    $.each(log, function (index, obj) {
                        if (obj)
                            output += JSON.stringify(obj).replace(/"/g, "").substring(0, type == "error" ? 1000 : 40) + ' ';
                    });

                } else {
                    output = JSON.stringify(log).replace(/"/g, "").substring(0, type == "error" ? 1000 : 40);
                }

                console[type](d.toLocaleTimeString('ru-RU') + ' ' + output);
            }

        },

        "error": function (message) {

            D.log(message.join(' '), 'error');
            alert(message.join(' '));

            if (D.Enabled.clean)
                $(".error").remove();

            $("div.loading").remove();

            var box = $('.content-box:visible').length == 1 ? $('.content-box:visible').first() : $('.content-top:visible').first(),
                error = $('<div class="error"><div><span>' + M.i18n('title-error') + '</span>' + message.join(' ') + '</div></div>'),
                buttons = null,
                errors = null;

            box.append(error);

            if (buttons = $("button.waiting")){
                buttons.removeClass('waiting');
            }

            if (D.Enabled.clean)
                if (errors = $(".error"))
                    setTimeout(function () {
                        errors.fadeOut(500);
                        setTimeout(function () {
                            errors.remove();
                        }, 500)
                    }, 1000);

            R.stop();
            return false;
        }
    };

    // Render Handler
    R = {

        "Cache": Cache,
        "Templates": Templates,
        "Render": [],
        "Path": [],
        "IsRendering": false,

        "init": function () {

            this.path();

        },

        "path": function () {

            this.Path = window.location.pathname.split('/');
            this.Path[1] = R.Path[1] || 'blog';

        },

        "render": function (options) {

            /* ----------------------------------------------------
             options = {
             template: Name of Template or Parse Href
             href:     Parse Href or Template
             json:     Source Object For Parsing
             callback: Callback Function
             url:      False for Skip U.update
             box:      Container (element or search by class)
             tab:      Menu Item (element or search by href)
             init:     For History PushState and check is exists Callback by Template name
             };
             ---------------------------------------------------- */

            options.template = options.template || U.Parse.Undo(this.href);
            options.href = U.Parse.Undo(this.href || options.template);
            options.box = typeof options.box !== 'object'
                ? $('.' + options.box).first()
                : options.box;
            options.tab = options.tab
                ? (typeof options.tab !== 'object' ? $('[href="' + options.tab + '"]').first() : options.tab)
                : $(this);
            options.init = $.extend({}, options, {
                template: options.template,
                box: options.box.attr('class').split(' ').join('.'),
                tab: options.tab.attr('href'),
                callback: null,
                url: false
            });

            /* substitution JSON with profile if template has "/profile/" */
            if (options.template.search(/profile/) != -1) {
                options.json = Player;
            }

            /* disable JSON for header menu */
            else if (options.template.search(/-/) == -1) {
                options.json = {};
            }

            /* disable JSON for "/new" template without "?object:id" */
            else if (!options.json && options.template.search(/new/) != -1) {

                var url = options.href.split('?'),
                    template = options.template.split('?');

                if (url.length > 1)
                    options.href = url[1];
                else
                    options.json = {};

                if (template.length > 1)
                    options.template = template[0];
            }

            D.log(['render.push:', options.template, options.href, options.json], 'info');
            R.Render.push(options);

            if (!R.IsRendering)
                R.rendering();

        },

        "rendering": function () {

            while (R.Render.length) {

                R.IsRendering = true;

                var options = R.Render.shift();

                R.loading(options);
                D.log(['rendering.run:', options.template, options.href, options.json], 'info');

                if (typeof options.json == 'object') {
                    D.log(['JSON from Object:', options.json]);
                    R.renderTMPL(options);
                } else {
                    R.renderJSON(options);
                }
            }

            R.stop();

        },

        "renderJSON": function (options) {

            D.log(['renderJSON:', options.href]);

            if (options.json = R.cache(options.href)) {

                D.log(['JSON from Cache:', options.json]);
                R.renderTMPL(options);

            } else {

                $.getJSON(U.Generate.Json(options.href), function (response) {
                    if (response.status == 1) {

                        options.json = R.cache(options.href, response.res);
                        D.log(['JSON from AJAX:', options.json], 'warn');
                        R.renderTMPL(options);

                    } else {

                        D.error(response.message);

                    }
                });
            }

        },

        "renderTMPL": function (options) {

            options.template = U.Parse.Tmpl(options.template);
            D.log(['renderTMPL:', options.template]);

            /* Insert into already exist DIV */
            if ($('.template.' + options.template).length) {

                D.log(['TMPL already in DOM', options.template]);
                R.renderHTML(options);

                /* Template from cache */
            } else if (R.Templates[options.template]) {

                options.template = R.Templates[options.template];
                D.log(['TMPL from Cache', options.template]);
                R.renderHTML(options);

                /* Template from HTML template */
            } else if ($('#tmpl-' + options.template).length) {

                options.template = R.Templates[options.template] = $('#tmpl-' + options.template).html();
                D.log(['TMPL from HTML:', options.template]);
                R.renderHTML(options);

                /* Template from AJAX template */
            } else {
                $.get(U.Generate.Tmpl(options.template), function (data) {

                    if (!$(data).not('empty').first().attr('class')) {
                        throw("Format Template Error");
                    } else {
                        options.template = R.Templates[options.template] = data;
                        D.log(['TMPL from AJAX:', options.template], 'warn');
                        R.renderHTML(options);
                    }

                });
            }

        },

        "renderHTML": function (options) {


            D.log(['renderHTML:', options.template, options.json]);
            var rendered = null;

            if (typeof options.json != 'object') {

                D.log('Rendered from HTML');
                rendered = $($('.template.' + options.template)[0].outerHTML).html(options.json);

            } else {

                D.log('Rendered from Template');
                Mustache.parse(options.template);   // optional, speeds up future uses
                rendered = Mustache.render(options.template, $.extend({"i18n": M.i18n, "eval": M.eval}, options.json));

            }

            R.inputHTML(rendered, options);
        },

        "inputHTML": function (rendered, options) {


            D.log(['inputHTML into:', (options.box && typeof options.box == 'object' ? options.box.attr('class') : options.box)]);

            var findClass = '.' + $(rendered).not('empty').first().attr('class').replace(/ /g, '.');

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

            if (C[options.init.template]) {
                D.log(['C.callback']);
                C[options.init.template](rendered, findClass);
            }

            /* parent box functionality after rendering */
            if (options.box) {

                /* if new box has tabs */
                if ($(I.Tabs, options.box).filter(":visible").length) {

                    /* click on unactive tab */
                    if (!$(I.Tabs, options.box).filter(".active:visible").length) {
                        D.log(['clickTab:', $(I.Tabs, options.box).not(".active").filter(":visible").first().attr('href')]);
                        $(I.Tabs, options.box).not(".active").filter(":visible").first().click();
                    }

                }

                /* tab functionality after click on tab */
                if (options.tab) {

                    $('a.active', options.tab.parent().parent()).removeClass('active');

                    if ($(I.Cats, options.tab.parents('.content-box')).filter(":visible").length) {
                        D.log(['clickCat:', $(I.Cats, options.box).first().attr('href')]);
                        $(I.Cats, options.tab.parents('.content-box')).first().click();
                    }

                    options.tab.addClass('active');
                }

            }


            U.update(options);
            R.loaded(options);

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

            $.each(key.split('-'), function (i, v) {
                // console.log(v);
            });

            if (key && R.Cache[key]) {
                return R.Cache[key];
            } else if (data && (!data['nocache'] && data['cache'] !== false)) {
                return R.Cache[key] = data;
            } else if (data) {
                return data;
            } else
                return false;
        },

        "stop": function () {

            D.log('rendering.stop');
            R.IsRendering = false;

        }
    };


    // Multilingual User Interface
    M = {
        "Texts": Texts,
        "i18n": function (key) {
            return key ? (M.Texts[key] ? M.Texts[key] : key) : (function (key) {
                return M.i18n(key);
            });
        },

        "eval": function (key) {
            return key ? eval(key) : (function (key) {
                return M.eval(key);
            });
        }
    };

    // URL Handler
    U = {

        "Path": {

            "Post": "/res/post/",
            "Json": "/res/json/",
            "Ajax": "/res/json/",
            "Tmpl": "/res/tmpl/"

        },

        "Generate": {
            "Post": function (url) {
                return U.Path.Post + U.Parse.Url(url);
            },

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
                return url.replace(/^\//, "").replace(/-/g, '/');
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
                    return url.replace(document.location.origin, "").replace(/^\//, "").replace(/\/|=/g, '-');
                }
            }
        },

        "update": function (options) {

            if (options.url !== false) {
                url = typeof options.href != 'object' ? options.href : options.init.template;
                if (url) {
                    console.log(options.init);
                    D.log(['updateURL:', url], 'info');
                    history.pushState(options.init, "Lotzon", '/' + U.Parse.Url(url));
                }
            }
        },

        "isAnchor": function (url) {
            return (url.indexOf('#') == 0);
        }

    };


    /* ========================================================= */
    /* ========================================================= */


});