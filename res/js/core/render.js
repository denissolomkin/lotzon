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
             template: Name of Template or Parse Href
             href:     Parse Href or Template
             json:     Source Object For Parsing
             after:    Callback Function
             url:      False for Skip U.update
             box:      Container (element or search by class)
             tab:      Menu Item (element or search by href)
             init:     For History PushState and check is exists Callback by Template name
             };
             ---------------------------------------------------- */

            options.template = options.template || U.parse(this.href);
            options.href = options.href || U.parse(this.href || options.template);
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
                    if (typeof options.json == 'object') {
                        D.log(['JSON from Object:', options.json]);
                        R.formatJSON(options);
                    } else {
                        R.renderJSON(options);

                    }
                }, this.timeout)
            }

            R.event.stop();

        },

        "renderJSON": function (options) {

            D.log(['renderJSON:', options.href]);

            if (options.json = Cache.get(options.href)) {

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
                /*
                 $.getJSON(U.generate(options.href), function (response) {
                 if (response.status == 1) {


                 } else {

                 D.error(response.message);

                 }
                 });
                 */
            }

        },

        "formatJSON": function (options) {

            if (typeof options.format === 'function') {
                D.log(['formatJSON:', options.json]);
                if (!options.arguments.length)
                    options.arguments.length = [];
                options.arguments.unshift(options.json);
                options.json = options.format(options.arguments);
            }

            R.renderTMPL(options);

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
            var rendered = null;

            if (typeof options.json != 'object') {

                D.log('Rendered from HTML');
                rendered = $($('.template.' + options.template)[0].outerHTML).html(options.json);

            } else {

                D.log('Rendered from Template');
                // Mustache.parse(options.template);   // optional, speeds up future uses
                rendered = options.template(options.json);

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
                else if (options.box.is(findClass))
                    options.box.html($(rendered).html()).show();
                else
                    options.box.append(rendered).find(findClass).hide().show();

            } else if (options.replace) {
                $(options.replace).html($(options.replace, rendered).html()).hide().fadeIn(1000);
            }

            console.log(options);
            if (options.after) {
                D.log(['callback', typeof options.after]);
                options.after(rendered, findClass);
            }

            if (callback = Callbacks['get'][U.parse(options.init.template, 'tmpl')]) {

                D.log(['C.callback', U.parse(options.init.template, 'tmpl')]);
                callback(rendered, findClass);
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

            var cache = Cache,
                path = key.split('-'),
                needle = path.last();

            /* if receive data for extend cache */
            if (data) {

                if (data.cache !== false) {
                    Cache.extend(data, path);
                    localStorage.setObj('Cache', Cache);
                    console.log('storage:', localStorage.getObj('Cache'));
                }

                cache = data.res;

            } else {

                /* check, if object already in cache */
                $.each(path, function (i, key) {
                    needle = key;
                    return cache = cache && cache.hasOwnProperty(key) && cache[key];
                });
            }

            console.log('needle:', needle);

            /* fix absent mustache each object, replacing with array */
            switch (typeof cache) {
                case 'object':
                    if (!isNumeric(needle) || !cache.length)
                        cache = {'items': format4Mustache(cache)};
                    break;
                case 'string':
                    /* nothing */
                    break;
                case 'boolean':
                    /* nothing */
                    break;
            }

            console.log(Cache, cache);
            return cache;

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