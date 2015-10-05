$(function () {

    /* ========================================================= */
    //                        ENGINE
    /* ========================================================= */

    // Debugger
    D = {

        "Enabled": {
            "info": true,
            "warn": false,
            "error": true,
            "log": false,
            "clean": true
        },

        "init": function () {

            $.ajaxSetup({
                error: function (xhr, status, message) {
                    D.error(['AJAX Error: ',message]);
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

                if (typeof log == 'object' && log.length) {

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

            if ($Button) {
                $Button.data('disabled', false);
                $Button = null;
            }

            D.log(message.join(' '), 'error');

            if(D.Enabled.clean)
                $(".error").remove();

            $(".loading").remove();

            $Error = $('<div class="error"><div><span>' + M.i18n('title-error') + '</span>' + message.join(' ') + '</div></div>');

            if (!$Box || !$Box.length){
                $Box = $('.content-box:visible').length == 1 ? $('.content-box:visible').first() : $('.content-top:visible').first();
            }

            $Box.append($Error);

            if(D.Enabled.clean)
                if ($Errors = $(".error"))
                    setTimeout(function () {
                        $Errors.fadeOut(500);
                        setTimeout(function () {
                            $Errors.remove();
                        }, 500)
                    }, 1000);

            R.empty();
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

            D.log('R.init','info');
            D.init();
            R.empty();

            R.Path = window.location.pathname.split('/');
            R.Path[1] = R.Path[1] || 'blog';

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

                if (!options.box)
                    options.box = $Box;
                else
                    options.box = (typeof options.box !== 'object' ? $('.'+options.box+':visible').first() : options.box);

                if (!options.tab)
                    options.tab = $Tab;
                else
                    options.tab = (typeof options.tab !== 'object' ? $('.'+options.tab+':visible').first() : options.tab);

                R.empty('soft');

                D.log(['render.push:', options.template, options.href, options.json], 'info');

                R.Render.push({
                    'options': {
                        'box': options.box,
                        'tab': options.tab,
                        'callback': options.callback,
                        'this': options.template
                    },
                    'url': options.url,
                    'template': options.template,
                    'href': options.href,
                    'json': options.json
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

                /* Insert into already exist DIV */
                if ($('.template.' + template).length) {

                    D.log(['TMPL already in DOM', template]);
                    R.renderHTML(template, json, options);

                /* Template from cache */
                } else if (R.Templates[template]) {

                    template = R.Templates[template];
                    D.log(['TMPL from Cache', template]);
                    R.renderHTML(template, json, options);

                /* Template from HTML template */
                } else if ($('#tmpl-' + template).length) {

                    template = R.Templates[template] = $('#tmpl-' + template).html();
                    D.log(['TMPL from HTML:', template]);
                    R.renderHTML(template, json, options);

                /* Template from AJAX template */
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

                    D.log('Rendered from HTML');
                    rendered = $($('.template.' + template)[0].outerHTML).html(json);

                } else {

                    D.log('Rendered from Template');
                    Mustache.parse(template);   // optional, speeds up future uses
                    rendered = Mustache.render(template, $.extend({"i18n": M.i18n, "eval": M.eval}, json));

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