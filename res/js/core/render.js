(function () {

    // Render Handler
    R = {

        "queue": [],
        "rendering": [],
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

        "stat": function () {

            return {
                total: {
                    timer: new Date().getTime(),
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


            options.template = U.parse(options.template || U.parse(this.href || options.href), 'tmpl');
            options.href = U.parse(options.href || this.href || options.template, 'url');

            console.log("Start:", options);

            if (D.isEnable('stat'))
                options.stat = R.stat();

            if ('nodeType' in this)
                options.target = this;

            var node = U.parse(options.href); //options.template;
            if (!options.node && node) {

                if (options.node = document.getElementById(U.parse(node, 'tmpl'))) {
                    console.log("Try Node 1:", U.parse(node, 'tmpl'));
                } else if (options.node = document.getElementById(node)) {
                    console.log("Try Node 2:", node);
                } else if (options.node = DOM.byId(node)) {
                    console.log("Try Node 3:", node);
                } else {
                    options.node = document.getElementById('content');
                    console.log("Try Node 4: content");
                }

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

            /* disable JSON for "/new" template */
            else if (!options.json && options.href.search(/\/new$/) !== -1) {
                options.json = {};
            }

            /* replace JSON to self for "/list" items */
            else if (options.href.indexOf('list') !== -1) {
                options.href = options.href.replace(/\/list/g, '').replace(/\-list/g, '');
            }

            /* rewrite JSON to user model when "/users" templates */
            else if (/users\/\d+\/\w*$/.test(options.href)) {
                options.href = options.href.replace(/\/\w*$/, '');
            }

            if (options.target || options.state)
                var page = document.getElementById(U.parse(U.parse(options.href), 'tmpl'));

            if (R.rendering.indexOf(options.href) !== -1) {
                console.error("Dublicate:", options);
                return false;
            }

            if (page && page.classList.contains('content-main')) {

                DOM.hide(page.parentNode.children);
                DOM.show(page);
                options.url !== false && (options.url = true);
                R.afterHTML(options);
                U.update(options);

            } else {

                R.event('push', options);
                R.queue.push(options);
                R.rendering.push(options.href);

                if (!R.isRendering)
                    R.render();

            }

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

            if (D.isEnable('stat')) {
                if (!options.stat)
                    options.stat = R.stat();
                options.stat.ajax.timer = new Date().getTime();
            }

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

                            if (D.isEnable('stat'))
                                options.stat.ajax.size = xhr.responseText.length;

                            options.json = Cache.set(options.href, data);
                            if (data.hasOwnProperty('lastItem'))
                                options.lastItem = true;
                            D.log(['Render.json:', options.href, 'JSON from AJAX:', options.json], 'warn');
                            R.sortJSON(options);
                        }

                    },
                    error: function (data) {
                        D.error.call(options, data && (data.message || data.responseJSON && data.responseJSON.message || data.statusText) + '<br>' + U.generate(options.href) || 'OBJECT NOT FOUND');
                    },
                    timeout: R.timeout.ajax
                });
            }

        },

        "sortJSON": function (options) {

            if (typeof options.json === 'object' && isNumeric(Object.keys(options.json)[0])) {
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

        "renderTMPL": function f(options, partial) {

            if (D.isEnable('stat')) {
                if (!options.stat.templates.timer) {
                    options.stat.ajax.timer -= new Date().getTime();
                    options.stat.templates.timer = new Date().getTime();
                }
            }

            var template = partial || options.template;

            /* Template from cache */
            if (Cache.hasTemplate(template)) {

                D.log(['Render.renderTMPL:', template, (!partial ? 'TEMPLATE' : 'PARTIAL') + ' from Cache'], 'render');
                if (!partial) {
                    options.template = Cache.template(template);
                }

                R.partialTMPL(options, partial);

                /* Template from AJAX template */
            } else {

                var xhr = $.ajax({
                    url: U.generate(template, 'tmpl'),
                    method: 'get',
                    dataType: 'text',
                    success: function (data) {

                        D.log(['Render.renderTMPL:', template, (!partial ? 'TEMPLATE' : 'PARTIAL') + ' from AJAX:', options.init.template], 'warn');

                        if (D.isEnable('stat')) {
                            options.stat.templates.size += parseInt(xhr.getResponseHeader('Content-Length')) || data.length;
                            options.stat.templates.count++;
                        }

                        var partials = Cache.partials(data);
                        if (partials && partials.length) {
                            if (!options.partials)
                                options.partials = [];
                            options.partials = options.partials.concat(partials);
                            for (var i = 0; i < partials.length; i++) {
                                R.renderTMPL(options, partials[i]);
                            }
                        }

                        template = Cache.template(template, data);

                        if (!partial) {
                            options.template = template;
                        }

                        R.partialTMPL(options, partial);

                    },
                    error: function (data) {
                        D.error.call(options, data && (data.message || data.responseJSON && data.responseJSON.message || data.statusText) + '<br>' + U.generate(options.href) || 'TEMPLATE NOT FOUND');
                    },
                    timeout: R.timeout.template
                });

            }

        },

        "partialTMPL": function f(options, partial) {

            D.log(['R.partialTMPL: ', partial, options.partials]);
            if (!partial && options.partials) {
                D.log(['Not ready: waiting ' + options.partials.length + ' partials', options.partials]);
            } else if (Cache.hasTemplate(partial || options.init.template)) {

                if (partial) {
                    options.partials.splice(options.partials.indexOf(partial), 1);
                    if (!options.partials.length)
                        delete options.partials;
                    f(options);
                } else {
                    R.renderHTML(options);
                }

            } else if (partial) {
                setTimeout(function () {
                    f(options, partial);
                }, 100);
                D.log(['Not ready, waiting compile: ', partial]);
            }


        },

        "renderHTML": function (options) {

            if (D.isEnable('stat')) {
                if (options.stat.templates.timer)
                    options.stat.templates.timer -= new Date().getTime();
                options.stat.render.timer = new Date().getTime();
            }

            options.rendered = options.template(options.json);
            D.log(['Render.renderHTML:', options.init.template, options.json, 'From Template:', options.rendered], 'render');


            if (D.isEnable('stat')) {
                options.stat.render.timer -= new Date().getTime();
                options.stat.after.timer = new Date().getTime();
            }

            R.inputHTML(options);
        },

        "inputHTML": function (options) {

            console.log("Complete:", options);

            var render = options.rendered = DOM.create(options.rendered),
                node = options.node,
                template = false;

            D.log(['Render.inputHTML into:', node.id], 'render');

            if (node) {

                if ('nodeType' in render) {

                    if (render.id == node.id) {

                        if (compare(render.classList, ['content-box', 'content-box-item', 'content-main'])) {
                            //such as blog-post-vew, games-online & games-chance
                            DOM.hide(node.parentNode && node.parentNode.children);
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
            R.rendering.splice(R.rendering.indexOf(options.href), 1);
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

            if (options.target) {

                var items = options.target.parentNode;
                if (!options.target.classList.contains('content-box-tab')) {
                    items = items.parentNode;
                    if (options.url !== false)
                        options.url = true;
                }
                items = items.getElementsByClassName('active');
                for (var i = 0; i < items.length; i++) {
                    items[i].classList.remove('active');
                }
                options.target.classList.add('active');
            }

            if (options.rendered && options.rendered.classList && options.rendered.classList.contains('content-main')) {
                var boxes = options.rendered.getElementsByClassName('content-box-tabs');
                for (var i = 0; i < boxes.length; i++) {
                    var tab = boxes[i].getElementsByClassName('content-box-tab')[0];
                    tab && tab.click();
                }
                if (options.url !== false)
                    options.url = true;
            }


            D.log(['Render.afterHTML callback:', U.parse(options.init.template, 'tmpl')], 'render');

            U.update(options);
            Content.infiniteScrolling();
            R.event('complete', options);

            if (D.isEnable('stat')) {
                options.stat.after.timer -= new Date().getTime();
                options.stat.total.size = options.stat.ajax.size + options.stat.templates.size;
                options.stat.total.timer -= new Date().getTime();
                D.stat(options);
            }

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