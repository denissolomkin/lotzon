$(function () {

    // Render Handler
    R = {

        "queue": [],
        "isRendering": false,

        "init": function () {
        },

        "push": function (options) {

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
                ? (options.box ? $('.' + options.box).first() : null)
                : options.box;
            options.tab = options.tab
                ? (typeof options.tab !== 'object' ? $('[href="' + options.tab + '"]').first() : options.tab)
                : $(this);

            options.init = $.extend({}, options, {
                template: options.template,
                box: options.box && options.box.attr('class').split(' ').join('.'),
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

            R.event.push(options);
            R.queue.push(options);

            if (!R.isRendering)
                R.render();

        },

        "render": function () {

            while (R.queue.length) {

                R.isRendering = true;

                var options = R.queue.shift();

                R.event.start(options);

                if (typeof options.json == 'object') {
                    D.log(['JSON from Object:', options.json]);
                    R.renderTMPL(options);
                } else {
                    R.renderJSON(options);
                }
            }

            R.event.stop();

        },

        "renderJSON": function (options) {

            D.log(['renderJSON:', options.href]);

            if (options.json = R.caching(options.href)) {

                D.log(['JSON from Cache:', options.json]);
                R.renderTMPL(options);

            } else {

                $.getJSON(U.Generate.Json(options.href), function (response) {
                    if (response.status == 1) {

                        options.json = R.caching(options.href, response.res);
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
            } else if (Templates[options.template]) {

                options.template = Templates[options.template];
                D.log(['TMPL from Cache', options.template]);
                R.renderHTML(options);

                /* Template from HTML template */
            } else if ($('#tmpl-' + options.template).length) {

                options.template = Templates[options.template] = $('#tmpl-' + options.template).html();
                D.log(['TMPL from HTML:', options.template]);
                R.renderHTML(options);

                /* Template from AJAX template */
            } else {
                $.get(U.Generate.Tmpl(options.template), function (data) {

                    if (!$(data).not('empty').first().attr('class')) {
                        throw("Format Template Error");
                    } else {
                        options.template = Templates[options.template] = data;
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

                if ($(findClass, options.box).length)
                    $(findClass, options.box).html($(rendered).html()).show();
                else
                    options.box.append(rendered).find(findClass).hide().show();

            } else if (options.replace) {
                $(options.replace).html($(options.replace, rendered).html()).hide().fadeIn(1000);
            }

            if (options.callback) {
                D.log(['callback']);
                options.callback(rendered, findClass);
            }

            if (Callbacks.render[options.init.template]) {
                D.log(['C.callback']);
                Callbacks.render[options.init.template](rendered, findClass);
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
            R.event.complete(options);

        },

        "caching": function (key, data) {

            $.each(key.split('-'), function (i, v) {
                // console.log(v);
            });

            if (key && Cache[key]) {
                return Cache[key];
            } else if (data && (!data['nocache'] && data['cache'] !== false)) {
                return Cache[key] = data;
            } else if (data) {
                return data;
            } else
                return false;
        },

        "event": {

            "push": function (options) {
                D.log(['render.push:', options.template, options.href, options.json], 'info');
            },

            "start": function (options) {

                D.log(['render.run:', options.template, options.href, options.json], 'info');
                if (options.box)
                    $('.modal-loading', options.box).length ? $('.modal-loading', options.box).show() : options.box.append('<div class="modal-loading"><div></div></div>');

            },

            "complete": function (options) {
                if (options.box)
                    $('.modal-loading', options.box).length ? $('.modal-loading', options.box).remove() : '';
            },

            "stop": function () {

                D.log('render.stop');
                R.isRendering = false;

            }
        }

    };

});