$(function () {

    // Render Handler
    R = {

        "queue": [],
        "isRendering": false,
        "timeout": 0,

        "init": function () {
        },

        "push": function (options) {

            /* ----------------------------------------------------
             options = {

             template:  Name of Template or Parse Href
             href:      Parse Href or Template
             json:      Source Object For Parsing
             url:       False for Skip U.update
             box:       Container (element or search by class)
             tab:       Menu Item (element or search by href)

             # auto #
             init:      For History PushState and check is exists Callback by Template name

             # before renderHTML #
             format:    Function for format JSON
             arguments: Arguments for JSON formating

             # after renderHTML #
             after:     Callback Function
             };

             ---------------------------------------------------- */

            options.template = options.template || U.parse(this.href);
            options.href = options.href || this.href || options.template; //U.parse();
            options.box = typeof options.box !== 'object'
                ? (options.box ? $(options.box).first() : null)
                : options.box;
            options.tab = options.tab
                ? (typeof options.tab !== 'object' ? $('[href="' + options.tab + '"]').first() : options.tab)
                : $(this);

            options.init = $.extend({}, options, {
                template: options.template,
                box: options.box && options.box.attr('class') && ('.' + options.box.attr('class').split(' ').join('.')),
                tab: options.tab.attr('href'),
                after: null,
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

                setTimeout(function () {
                    R.json(options);
                }, this.timeout)
            }

            R.event.stop();

        },

        "json": function (options) {

            options.href = U.parse(options.href);

            D.log(['renderJSON:', options.href]);

            if (typeof options.json === 'object') {

                options.json = Cache.set(options.href, options.json);
                D.log(['JSON from Object:', options.json]);
                R.formatJSON(options);

            } else if (options.json = Cache.get(options.href)) {

                D.log(['JSON from Cache:', options.json]);
                R.formatJSON(options);

            } else {

                $.ajax({
                    url: U.generate(options.href),
                    method: 'get',
                    dataType: 'json',
                    statusCode: {

                        404: function (data) {
                            throw(data.message);
                        },

                        200: function (data) {

                            options.json = Cache.set(options.href, data);
                            D.log(['JSON from AJAX:', options.json], 'warn');
                            R.formatJSON(options);

                        },

                        201: function (data) {
                            throw(data.message);
                        },

                        204: function (data) {
                            throw(data.message);
                        },

                        500: function (data) {
                            throw(data.message);
                        }
                    }
                });
            }

        },

        "formatJSON": function (options) {

            if (typeof options.format === 'function') {
                D.log(['formatJSON:', options.json]);
                options.json = options.format(options.json, options.arguments);
            }

            if (options.template)
                R.renderTMPL(options);
            else
                return options;

        },

        "renderTMPL": function (options) {

            options.template = U.parse(options.template, 'tmpl');
            D.log(['renderTMPL:', options.template]);

            /* Insert into already exist DIV */
            if ($('.template.' + options.template).length) {

                D.log(['TMPL already in DOM', options.template]);
                R.renderHTML(options);

                /* Template from cache */
            } else if (Template.has(options.template)) {

                options.template = Template.get(options.template);
                D.log(['TMPL from Cache', options.init.template]);
                R.renderHTML(options);

                /* Template from HTML template */
            } else if ($('#tmpl-' + options.template).length) {

                options.template = Template.set(options.template, $('#tmpl-' + options.template).html());
                D.log(['TMPL from HTML:', options.template]);
                R.renderHTML(options);

                /* Template from AJAX template */
            } else {

                $.get(U.generate(options.template, 'tmpl'), function (data) {

                    if (!$(data).not('empty').first().attr('class')) {
                        throw("Format Template Error: " + options.template);
                    } else {
                        options.template = Template.set(options.template, data);
                        D.log(['TMPL from AJAX:', options.init.template], 'warn');
                        R.renderHTML(options);
                    }

                });
            }

        },

        "renderHTML": function (options) {

            D.log(['renderHTML:', options.init.template, options.json]);

            if (typeof options.template != 'function') {

                options.rendered = $($('.template.' + options.template)[0].outerHTML).html(options.json);
                D.log(['Rendered from HTML:', options.rendered]);

            } else {

                options.rendered = options.template(options.json);
                D.log(['Rendered from Template:', options.rendered]);

            }

            R.inputHTML(options);
        },

        "inputHTML": function (options) {

            options.findClass = '.' + $(options.rendered).not('empty').first().attr('class').replace(/ /g, '.');
            D.log(['inputHTML into:', (options.box && typeof options.box == 'object' ? options.box.attr('class') : options.box)]);

            if (options.box) {

                $(' > div', options.box).hide();

                if ($(options.findClass, options.box).length) {

                    D.log(['Replace Block in Box']);

                    $(options.findClass, options.box).html($(options.rendered).html()).show();

                } else if (options.box.is(options.findClass)) {

                    D.log(['Replace Box']);

                    options.box.html($(options.rendered).html()).show();

                } else {
                    D.log(['Append to Box']);
                    options.box.append(options.rendered).find(options.findClass).hide().show();
                }

            } else if (options.replace) {
                $(options.replace).html($(options.replace, options.rendered).html()).hide().fadeIn(1000);
            }

            R.afterHTML(options);

        },

        "afterHTML": function (options) {

            D.log(['afterHTML class:', options.findClass]);

            if (options.after) {
                D.log(['callback', typeof options.after]);
                options.after(options);
            }

            if (callback = Callbacks['get'][U.parse(options.init.template, 'tmpl')]) {

                D.log(['C.callback', U.parse(options.init.template, 'tmpl')]);
                callback(options);
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
                    $('.modal-loading', options.box).length ? $('.modal-loading', options.box).first().remove() : '';
            },

            "stop": function () {

                D.log('render.stop');
                R.isRendering = false;

            }
        }

    };

});