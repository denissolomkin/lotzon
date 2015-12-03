(function () {

    // Render Handler
    R = {

        "queue": [],
        "isRendering": false,
        "timeout": {
            "ajax": 10000,
            "template": 10000,
            "delay": 0
        },
        "animation": {
            "loading": false
        },

        "init": function () {
        },

        "push": function (options) {

            /* ----------------------------------------------------
             options = {

             template:  Name of Template or Parse Href
             href:      Parse Href or Template
             json:      Source Object For Parsing
             url:       False for Skip U.update
             node:      Container (element or search by id)
             target:    Initiative element

             # auto #
             init:      For History PushState and check is exists Callback by Template name

             # before renderHTML #
             format:    Function for format JSON
             arguments: Arguments for JSON formating

             # after renderHTML #
             after:     Callback Function
             };

             ---------------------------------------------------- */

            console.log(this, options);

            if (options) {
                if (typeof options === 'object') {

                    if ('target' in options) {
                        options.preventDefault();
                        options.stopPropagation();
                    }

                    if ('nodeType' in this)
                        options = {'href': this.getAttribute('href')};

                } else if (typeof options === 'string')
                    options = {'href': options};
            }


            options.template = options.template || U.parse(this.href || options.href);
            options.href = options.href || this.href || options.template;

            var start = new Date().getTime();
            options.stat = {
                total: {
                    timer: start,
                    size: null
                },
                ajax: {
                    timer: null,
                    size: null
                },
                templates: {
                    count: 0,
                    timer: null,
                    size: 0
                },
                render: {
                    timer: null
                },
                after: {
                    timer: null
                }
            };

            if ('nodeType' in this)
                options.target = this;

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

            options.init = Object.deepExtend({}, {
                href: options.href,
                template: options.template
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
                }, this.timeout.delay)
            }

            R.event('stop');

        },

        "json": function (options) {

            options.stat.ajax.timer = new Date().getTime();
            options.href = U.parse(options.href, 'get');

            if (typeof options.json === 'object') {

                options.json = Cache.set(options.href, options.json);
                D.log(['Render.json:', options.href, 'JSON from Object:', options.json], 'render');
                R.sortJSON(options);

            } else if (options.json = Cache.get(options.href)) {

                D.log(['Render.json:', options.href, 'JSON from Cache:', options.json], 'render');
                R.sortJSON(options);

            } else {

                var xhr = $.ajax({
                    url: U.generate(options.href),
                    data: options.query,
                    method: 'get',
                    dataType: 'json',
                    success: function (data) {


                        if ('responseText' in data) {
                            D.error.call(options, 'OBJECT NOT FOUND');
                        } else {
                            options.stat.ajax.size = xhr.responseText.length;
                            options.json = Cache.set(options.href, data);
                            D.log(['Render.json:', options.href, 'JSON from AJAX:', options.json], 'warn');
                            R.sortJSON(options);
                        }

                    },
                    error: function (data) {
                        console.log(data);
                        D.error.call(options, data && (data.message || data.responseJSON && data.responseJSON.message || data.statusText) + '<br>' + U.generate(options.href) || 'OBJECT NOT FOUND');
                    },
                    timeout: R.timeout.ajax
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

            if (!options.stat.templates.timer) {
                options.stat.ajax.timer -= new Date().getTime();
                options.stat.templates.timer = new Date().getTime();
            }

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

                var xhr = $.ajax({
                    url: U.generate(template, 'tmpl'),
                    method: 'get',
                    dataType: 'text',
                    success: function (data) {

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

                        options.stat.templates.size += parseInt(xhr.getResponseHeader('Content-Length')) || data.length;
                        options.stat.templates.count++;
                        R.partialTMPL(options);

                    },
                    error: function (data) {
                        console.log(data);
                        D.error.call(options, data && (data.message || data.responseJSON && data.responseJSON.message || data.statusText) + '<br>' + U.generate(options.href) || 'TEMPLATE NOT FOUND');
                    },
                    timeout: R.timeout.template
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

            if (options.stat.templates.timer)
                options.stat.templates.timer -= new Date().getTime();
            options.stat.render.timer = new Date().getTime();

            options.rendered = options.template(options.json);
            D.log(['Render.renderHTML:', options.init.template, options.json, 'From Template:', options.rendered], 'render');

            options.stat.render.timer -= new Date().getTime();
            options.stat.after.timer = new Date().getTime();

            R.inputHTML(options);
        },

        "inputHTML": function (options) {

            console.log(options);

            var render = options.rendered = DOM.create(options.rendered),
                node = options.node,
                template = false;

            D.log(['Render.inputHTML into:', node.id], 'render');

            if (node) {

                if ('nodeType' in render) {

                    if (render.id == node.id) {

                        if (compare(render.classList, ['content-box', 'content-box-item', 'content-main'])) {
                            //such as blog-post-vew, games-online & games-chance
                            DOM.hide(node.parentNode.children);
                        }

                        if (node.parentNode) {
                            node.parentNode.replaceChild(render, node);
                            DOM.show(node);
                            console.log('replaceChild');
                        }

                    } else {

                        if (compare(render.classList, ['content-main'])) {
                            //such as games & blog & lottery
                            node = document.getElementById('content');
                        }

                        if (compare(render.classList, ['content-box'])) {
                            //such as view
                            node = document.getElementById('content');
                            render.classList.add('slideInRight');
                        } else if (compare(render.classList, ['content-box-item'])) {
                            // for content-box-item
                            node = node.getElementsByClassName('content-box-content')[0];
                        }

                        if (!node)
                            node = document.getElementById('content');

                        if (compare(render.classList, ['content-box-item', 'content-main'])) {
                            //such as games-online & games-chance
                            console.log(node);
                            DOM.hide(node.children);
                        } else if (node.id == 'content') {
                            DOM.hide(node.children);
                        }

                        if (options.init.template.indexOf('-item') !== -1)
                            template = Cache.get(
                                U.parse(options.init.template, 'tmpl')
                                    .replace('-item', '-list'), 'templates');

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

            R.afterHTML(options);

        },

        "afterHTML": function (options) {


            if (options.after) {
                D.log(['Render.after', typeof options.after], 'render');
                options.after(options);
            }

            if (callback = Callbacks['get'][U.parse(options.init.template, 'tmpl')]) {
                D.log(['Callbacks.get', U.parse(options.init.template, 'tmpl')], 'render');
                callback(options);
            }

            if (options.rendered && options.rendered.classList && options.rendered.classList.contains('content-main')) {
                var boxes = options.rendered.getElementsByClassName('content-box-tabs');
                for (var i = 0; i < boxes.length; i++) {
                    var tab = boxes[i].getElementsByClassName('content-box-tab')[0];
                    tab && tab.click();
                }

                if (options.target && options.target.parentNode.tagName === 'LI') {
                    var items = options.target.parentNode.parentNode.getElementsByClassName('active');
                    for (var i = 0; i < items.length; i++) {
                        items[i].classList.remove('active');
                    }
                }

                options.url = true;

            } else if (options.target && options.target.classList) {

                /* for tabs */
                if (options.target.classList.contains('content-box-tab')) {
                    var tabs = options.target.parentNode.getElementsByClassName('active');
                    for (var i = 0; i < tabs.length; i++) {
                        tabs[i].classList.remove('active');
                    }
                } else
                    options.url = true;

            }

            if (options.target)
                options.target.classList.add('active');


            D.log(['Render.afterHTML class:', options.findClass], 'render');

            U.update(options);
            Content.infiniteScrolling();
            R.event('complete', options);

            options.stat.after.timer -= new Date().getTime();
            options.stat.total.size = options.stat.ajax.size + options.stat.templates.size;
            options.stat.total.timer -= new Date().getTime();
            D.stat(options);

        },

        "event": function (event, options) {

            switch (event) {

                case "push":
                    D.log(['Render.push:', options.template, options.href, options.json], 'info');
                    break;

                case "start":

                    D.log(['Render.run:', options.template, options.href, options.json], 'info');
                    if (options.node && R.animation.loading) {
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