(function () {

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

            console.log(options);

            if (typeof options !== 'object')
                options = {'href': options};

            options.template = options.template || U.parse(this.href || options.href);
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

            /* disable JSON for header and support menu */
            else if (options.template.search(/-/) == -1 || options.template.search(/support/) !== -1) {
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

            /* fix JSON for "/all" template */
            else if (!options.json && options.href.search(/all/) != -1) {

                options.href = options.href.replace(/\/all/g, '');
                options.init.template = options.template = options.template.replace(/\-all/g, '');
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

            if (typeof options.json === 'object') {

                options.json = Cache.set(options.href, options.json);
                D.log(['Render.json:', options.href, 'JSON from Object:', options.json], 'render');
                R.formatJSON(options);

            } else if (options.json = Cache.get(options.href)) {

                D.log(['Render.json:', options.href, 'JSON from Cache:', options.json], 'render');
                R.formatJSON(options);

            } else {

                $.ajax({
                    url: U.generate(options.href),
                    data: options.query,
                    method: 'get',
                    dataType: 'json',
                    statusCode: {

                        404: function (data) {
                            throw(data.message);
                        },

                        200: function (data) {

                            options.json = Cache.set(options.href, data);
                            D.log(['Render.json:', options.href, 'JSON from AJAX:', options.json], 'warn');
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

            /* Insert into already exist DIV */
            if ($('.template.' + options.template).length) {

                D.log(['Render.renderTMPL:', options.template, 'TMPL already in DOM', options.template], 'render');
                R.renderHTML(options);

                /* Template from cache */
            } else if (Cache.template(options.template)) {

                D.log(['Render.renderTMPL:', options.template, 'TMPL from Cache', options.init.template], 'render');
                options.template = Cache.template(options.template);
                R.renderHTML(options);

                /* Template from HTML template */
            } else if ($('#tmpl-' + options.template).length) {

                D.log(['Render.renderTMPL:', options.template, 'TMPL from HTML:', options.template], 'render');
                options.template = Cache.template(options.template, $('#tmpl-' + options.template).html());
                R.renderHTML(options);

                /* Template from AJAX template */
            } else {

                $.get(U.generate(options.template, 'tmpl'), function (data) {

                    if (!$(data).not('empty').first().attr('class')) {
                        throw("Format Template Error: " + options.template);
                    } else {

                        D.log(['Render.renderTMPL:', options.template, 'TMPL from AJAX:', options.init.template], 'warn');
                        options.template = Cache.template(options.template, data);
                        R.renderHTML(options);
                    }

                });
            }

        },

        "renderHTML": function (options) {

            if (typeof options.template != 'function') {

                options.rendered = $($('.template.' + options.template)[0].outerHTML).html(options.json);
                D.log(['Render.renderHTML:', options.init.template, options.json, 'From HTML:', options.rendered], 'render');

            } else {

                options.rendered = options.template(options.json);
                D.log(['Render.renderHTML:', options.init.template, options.json, 'From Template:', options.rendered], 'render');

            }

            R.inputHTML(options);
        },

        "inputHTML": function (options) {

            options.findClass = '.' + $(options.rendered).not('empty').first().attr('class').replace(/ /g, '.');

            if (options.replace) {


                if (options.replace.indexOf('.render-list') !== -1) {

                    D.log(['Render.inputHTML into:', (options.box && typeof options.box == 'object' ? options.box.attr('class') : options.box), 'Replacing .render-list'], 'render');

                    var isSimilar = $(options.rendered).is(options.replace),
                        appendHTML = isSimilar
                            ? $(options.rendered).html()
                            : $(options.replace, options.rendered).html(),
                        appendPlace = isSimilar
                            ? options.replace
                            : options.findClass + ' ' + options.replace;

                    $(appendHTML).appendTo(appendPlace).hide().fadeIn();

                } else if ($(options.rendered).is(options.replace)) {

                    D.log(['Render.inputHTML into:', (options.box && typeof options.box == 'object' ? options.box.attr('class') : options.box), 'Replacing self'], 'render');
                    $(options.replace).replaceWith($(options.rendered));

                } else {

                    D.log(['Render.inputHTML into:', (options.box && typeof options.box == 'object' ? options.box.attr('class') : options.box), 'Replacing '+options.replace], 'render');
                    $(options.replace).html($(options.replace, options.rendered).html()).hide().fadeIn(1000);
                }

            } else if (options.box) {

                $(' > div', options.box).hide();

                if ($(options.findClass, options.box).length) {

                    D.log(['Render.inputHTML into:', (options.box && typeof options.box == 'object' ? options.box.attr('class') : options.box), 'Replace Block in Box'], 'render');

                    $(options.findClass, options.box)
                        .html($(options.rendered).html()).show()
                        .parents().show();

                    // content-box-item with content-box-item-top
                    if($(options.rendered).is('.content-box-item-content')) {
                        $(' > div', options.box).show();
                    }

                } else if (options.box.is(options.findClass)) {

                    D.log(['Render.inputHTML into:', (options.box && typeof options.box == 'object' ? options.box.attr('class') : options.box), 'Box = Rendered'], 'render');
                    options.box.html($(options.rendered).html()).find(' > div').show();

                } else {

                    D.log(['Render.inputHTML into:', (options.box && typeof options.box == 'object' ? options.box.attr('class') : options.box), 'Append to Box'], 'render');
                    options.box.append(options.rendered).find(options.findClass).hide().fadeIn();
                }

            } else if ($(options.findClass).length) {

                D.log(['Render.inputHTML into:', (options.findClass), 'Replace Block by Finding'], 'render');
                $(options.findClass).html($(options.rendered).html()).show();
            }

            U.update(options);
            R.afterHTML(options);

        },

        "afterHTML": function (options) {

            D.log(['Render.afterHTML class:', options.findClass], 'render');

            if (options.after) {
                D.log(['Render.after', typeof options.after], 'render');
                options.after(options);
            }

            if (callback = Callbacks['get'][U.parse(options.init.template, 'tmpl')]) {
                D.log(['Callbacks.get', U.parse(options.init.template, 'tmpl')], 'render');
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

                /* rendered box functionality after rendering */
            } else if (!$(I.Cats, $(options.findClass)).filter(".active").length) {
                if ($(I.Cats, $(options.findClass)).filter(":visible").length) {
                    D.log(['clickCat:', $(I.Cats, $(options.findClass)).first().attr('href')]);
                    $(I.Cats, $(options.findClass)).first().click();
                }
            }

            /* tab functionality after click on tab */
            if (options.tab) {

                $('a.active', options.tab.parent().parent()).removeClass('active');
                options.tab.addClass('active');

                if (!$(I.Cats, options.tab.parents('.content-box')).filter(".active").length)
                    if ($(I.Cats, options.tab.parents('.content-box')).filter(":visible").length) {
                        D.log(['clickCat:', $(I.Cats, options.box).first().attr('href')]);
                        $(I.Cats, options.tab.parents('.content-box')).first().click();
                    }

            }

            Content.infiniteScrolling();
            R.event.complete(options);

        },

        "event": {

            "push": function (options) {
                D.log(['Render.push:', options.template, options.href, options.json], 'info');
            },

            "start": function (options) {

                D.log(['Render.run:', options.template, options.href, options.json], 'info');
                if (options.box)
                    $('.modal-loading', options.box).length ? $('.modal-loading', options.box).show() : options.box.append('<div class="modal-loading"><div></div></div>');

            },

            "complete": function (options) {
                if (options.box)
                    $('.modal-loading', options.box).length ? $('.modal-loading', options.box).first().remove() : '';
            },

            "stop": function () {

                D.log('Render.stop', 'render');
                R.isRendering = false;

            }
        }

    };

})();