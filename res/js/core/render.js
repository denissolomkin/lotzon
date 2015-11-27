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
             node:      Container (element or search by id)
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


            var node = options.template;
            if (!options.node && node) {

                options.node = document.getElementById(U.parse(options.href));

                if (!options.node)
                    options.node = document.getElementById(U.parse(node, 'tmpl'));

                if (0 && !options.node)
                    options.node = document.getElementById(node);


                if (!options.node)
                    options.node = DOM.byId(node);

                if (!options.node)
                    options.node = document.getElementById('content');

            }


            options.tab = options.tab
                ? (typeof options.tab !== 'object' ? $('[href="' + options.tab + '"]').first() : options.tab)
                : 'nodeType' in this ? $(this) : null;

            options.init = $.extend({}, options, {
                template: options.template,
                box: options.box && options.box.attr('class') && ('.' + options.box.attr('class').split(' ').join('.')),
                tab: options.tab && options.tab.attr('href'),
                after: null,
                node: null,
                url: false
            });

            /* substitution JSON with profile if template has "/profile/" */
            if (options.template.search(/profile/) !== -1) {
                options.json = Player;
            }

            /* disable JSON for header and support menu */
            else if (options.template.search(/-/) === -1 || options.template.search(/support/) !== -1) {
                options.json = {};
            }

            /* OLD disable JSON for "/new" template without "?object:id" */
            else if (!options.json && options.href.search(/\/new$/) !== -1) {
                options.json = {};
                /*
                var url = options.href.split('?'),
                    template = options.template.split('?');

                if (url.length > 1)
                    options.href = url[1];
                else
                    options.json = {};

                if (template.length > 1)
                    options.template = template[0];
                    */
            }

            /* OLD fix JSON for "/all" template */
            else if (!options.json && options.href.search(/all/) != -1) {
                options.href = options.href.replace(/\/all/g, '');
                options.init.template = options.template = options.template.replace(/\-all/g, '');
            }

            /* replace JSON to self for "/list" items */
            else if (options.href.indexOf('list') !== -1) {
                options.href = options.href.replace(/\/list/g, '').replace(/\-list/g, '');
            }

            /* rewrite JSON to user model when "/users" templates */
            else if (/users\/\d+\/\w*$/.test(options.href)) {
                options.href = options.href.replace(/\/\w*$/, '');
            }

            R.event('push', options);
            R.queue.push(options);

            if (!R.isRendering)
                R.render();

        },

        "render": function () {

            while (R.queue.length) {

                R.isRendering = true;
                var options = R.queue.shift();

                R.event('start', options);

                setTimeout(function () {
                    R.json(options);
                }, this.timeout)
            }

            R.event('stop');

        },

        "json": function (options) {

            options.href = U.parse(options.href, 'get');

            if (typeof options.json === 'object') {

                options.json = Cache.set(options.href, options.json);
                D.log(['Render.json:', options.href, 'JSON from Object:', options.json], 'render');
                R.sortJSON(options);

            } else if (options.json = Cache.get(options.href)) {

                D.log(['Render.json:', options.href, 'JSON from Cache:', options.json], 'render');
                R.sortJSON(options);

            } else {

                $.ajax({
                    url: U.generate(options.href),
                    data: options.query,
                    method: 'get',
                    dataType: 'json',
                    statusCode: {

                        404: function (data) {
                            D.error.call(options, 'OBJECT NOT FOUND');
                            return true;
                        },

                        200: function (data) {

                            if ('responseText' in data) {
                                D.error('OBJECT NOT FOUND');
                            } else {
                                options.json = Cache.set(options.href, data);
                                D.log(['Render.json:', options.href, 'JSON from AJAX:', options.json], 'warn');
                                R.sortJSON(options);
                            }

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

        "sortJSON": function (options) {

            if (typeof options.json === 'object') {
                options.json = (function (s) {
                    var t = {};
                    Object.keys(s).sort().forEach(function (k) {
                        t[k] = s[k]
                    });
                    return t
                })(options.json);
            }

            D.log(['sortJSON:', options.json]);
            R.formatJSON(options);

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

            var template = '',
                partial = false;

            if (options.partials && options.partials.length)
                partial = template = U.parse(U.parse(options.partials.shift()), 'tmpl');
            else
                template = options.template = U.parse(options.template, 'tmpl');

            /* Template from cache */
            if (Cache.template(template)) {

                D.log(['Render.renderTMPL:', template, (!partial ? 'TEMPLATE' : 'PARTIAL') + ' from Cache', options.init.template], 'render');
                if (!partial)
                    options.template = Cache.template(template);

                R.renderHTML(options);

                /* Template from AJAX template */
            } else {

                $.ajax({
                    url: U.generate(template, 'tmpl'),
                    method: 'get',
                    dataType: 'text',
                    statusCode: {

                        404: function (data) {
                            D.error.call(options, 'TEMPLATE NOT FOUND');
                            return true;
                        },

                        200: function (data) {

                            D.log(['Render.renderTMPL:', template, (!partial ? 'TEMPLATE' : 'PARTIAL') + ' from AJAX:', options.init.template], 'warn');

                            if (partial)
                                Cache.template(template, data);
                            else
                                options.template = Cache.template(template, data);

                            var partials = Cache.partials(template);

                            if (partials && partials.length) {
                                if (!options.partials)
                                    options.partials = [];
                                for (var i = 0; i < partials.length; i++)
                                    options.partials.push(partials[i]);
                            }

                            R.partialTMPL(options);


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

        "partialTMPL": function (options) {

            if (options.partials && options.partials.length)
                R.renderTMPL(options);
            else
                R.renderHTML(options);

        },

        "renderHTML": function (options) {

            options.rendered = options.template(options.json);
            D.log(['Render.renderHTML:', options.init.template, options.json, 'From Template:', options.rendered], 'render');

            R.inputHTML(options);
        },

        "inputHTML": function (options) {

            console.log(options);

            var render = options.rendered = DOM.create(options.rendered),
                node = options.node,
                template = false;

            D.log(['Render.inputHTML into:', node.id], 'render');

            if (node) {

                if (render.length === 1) {

                    options.rendered = render = render[0];

                    if (render.id == node.id) {

                        if (compare(render.classList, ['content-box', 'content-box-item', 'content-main'])) {//such as blog-post-vew, games-online & games-chance
                            DOM.hide(node.parentNode.children);
                        }

                        if(node.parentNode) {
                            node.parentNode.replaceChild(render, node);
                            DOM.show(node);
                            console.log('replaceChild');
                        }

                    } else {

                        if (compare(render.classList, ['content-main'])) {  //such as games & blog & lottery
                            node = document.getElementById('content');
                        }
                        if (compare(render.classList, ['content-box'])) {  //such as view
                            node = document.getElementById('content');
                            render.classList.add('slideInRight');
                        } else if (compare(render.classList, ['content-box-item'])) {
                            node = node.getElementsByClassName('content-box-content')[0]; // for content-box-item
                        }

                        if (compare(render.classList, ['content-box-item', 'content-main'])) {//such as games-online & games-chance
                            DOM.hide(node.children);
                        } else if (node.id == 'content') {
                            DOM.hide(node.children);
                        }

                        if (options.init.template.indexOf('-item') !== -1)
                            template = Cache.get(U.parse(options.init.template, 'tmpl').replace('-item', '-list'), 'templates');

                        if (template && template.indexOf('reverse') !== -1) {

                            DOM.prepend(render, node);
                            console.log('prepend ID');

                        } else {
                            DOM.append(render, node);
                            console.log('append ID');
                        }

                    }

                } else {

                    DOM.append(render, node);
                    console.log('append list');
                }

            } else {
                console.log('node is absent');
            }

            console.log('initNode: ', options.node);
            console.log('appendNode: ', node);
            console.log('renderNode: ', render);

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
            R.event('complete', options);

        },

        "event": function (event, options) {

            switch (event) {

                case "push":
                    D.log(['Render.push:', options.template, options.href, options.json], 'info');
                    break;

                case "start":

                    D.log(['Render.run:', options.template, options.href, options.json], 'info');
                    if (options.node) {

                        DOM.remove('.modal-error', options.node);
                        DOM.append('<div class="modal-loading"><div></div></div>', options.node);
                    }

                    break;

                case "complete":

                    if (options.node) {
                        DOM.remove('.modal-loading', options.node);
                    }

                    break;

                case "stop":
                    D.log('Render.stop', 'render');
                    R.isRendering = false;
                    break;
            }
        }

    };

})();