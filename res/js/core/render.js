(function () {

    // Render Handler
    R = {

        "queue"      : [],
        "rendering"  : [],
        "isRendering": false,
        "timeout"    : {
            "ajax"    : 30 * 1000,
            "template": 30 * 1000,
            "delay"   : 0
        },
        "animation"  : {
            "loading": false
        },

        "init": function () {
        },

        "stat": function () {

            return {
                total    : {
                    timer: new Date().getTime(),
                    size : null
                },
                ajax     : {
                    timer: null,
                    size : null
                },
                templates: {
                    count: 0,
                    timer: null,
                    size : 0
                },
                render   : {
                    timer: null
                },
                after    : {
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
                        if(options.target.getAttribute('target') == '_blank' || this.getAttribute('target') == '_blank' )
                            return true;
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

            console.log("Start:", options.href, options.template);

            if(!options.template) {
                D.error(options);
                return;
            }

            /* patch for browser extensions */
            if(/users\/\d+\/\w*$/.test(options.href)){
                options.href = options.href.replace('users', 'user');
                options.template = options.template.replace('users', 'user');
            }

            if (D.isEnable('stat'))
                options.stat = R.stat();

            if ('nodeType' in this)
                options.target = this;

            R.initNode(options);

            options.init = Object.deepExtend({}, {
                href    : options.href,
                template: options.template
            });

            var isNotTab = !options.hasOwnProperty('target') || !options.target.classList.contains('content-box-tab'),
                isNotExcludedPage = !options.template.match('-moment|-random|popup-|support-|ticket-|reports-|balance-|menu-|-list|-new|-item|-replyform|-complain|-field|-like|-notifications'),
                isNotTopPage = (options.template.search(/-/) !== -1 && options.template.match(/-/g).length >= 1),
                isNotViewPage = !/\d+$/.test(options.href.split('?')[0]),
                isNotNodeTemplate = options.node.id !== options.init.template && options.node.id !== options.href.replace(/\/\w*$/,'').replace('/','-'),
                isNodeUnvisible = !DOM.isVisible(options.node);

            if(isNotExcludedPage && isNotTopPage && isNotViewPage && isNotTab && (isNotNodeTemplate || isNodeUnvisible)) {
                options.node = false;
                options.tab = options.href;
                options.href = options.init.href = options.href.replace(/\/\w*$/,'');
                options.template = options.init.template = options.template.replace(/\-\w*$/,'');
                R.initNode(options);
            }

            /* substitution JSON with profile if template has "/profile/" or "/balance/" */
            if ((options.template.search(/profile-/) !== -1 || options.template.search(/balance-/) !== -1 || options.template.search(/support/) !== -1) && (options.template.search(/-/) == -1 || options.template.match(/-/g).length < 2)) {
                options.json = Player;
            }

            /* disable JSON for header, popup and support menu */
            else if (!options.json && (options.template.search(/-/) === -1 || options.template.search(/popup/) !== -1)) {
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

            /* transport search line to query */
            else if(options.href.indexOf('?') !== -1) {
                var search = options.href.split('?');
                options.href = search[0];
                options.query = options.query || {};
                Object.deepExtend(options.query, JSON.parse('{"' + decodeURI(search[1].replace(/&/g, "\",\"").replace(/=/g, "\":\"")) + '"}'));
            }

            /* rewrite JSON to user model when "/users" templates */
            else if (!options.json && /user\/\d+\/\w*s$/.test(options.href)) {
                options.href = options.href.replace(/\/\w*s$/, '/card');
            }

            if (R.rendering.indexOf(options.href) !== -1) {
                console.error("Dublicate:", options);
                return false;
            }

            if(!DOM.visible('.on-top').length || options.href.indexOf('popup') !== -1) {

                if (options.target || options.state)
                    var page = document.getElementById(U.parse(U.parse(options.href), 'tmpl'));

                if (page && page.classList.contains('content-main') && !page.classList.contains('content-box')) {

                    DOM.hide(page.parentNode.children, 'pop-box');
                    DOM.show(page);
                    if(options.url !== false)
                        options.url = true;
                    R.afterHTML(options);
                    U.update(options);

                } else {

                    R.event('push', options);
                    R.queue.push(options);
                    R.rendering.push(options.href);

                    if (!R.isRendering)
                        R.render();

                }
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

            if (typeof options === 'string') {
                options = {href: options};
            }

            if (D.isEnable('stat')) {
                if (!options.stat)
                    options.stat = R.stat();
                options.stat.ajax.timer = new Date().getTime();
            }

            if (typeof options.json === 'object') {

                options.json = Cache.init(options);
                D.log(['Render.json:', options.href, 'JSON from Object:', options.json], 'render');
                R.sortJSON(options);

            } else if (options.json = Cache.get(options)) {

                D.log(['Render.json:', options.href, 'JSON from Cache:', options.json], 'render');
                R.sortJSON(options);

            } else {

                var xhr = $.ajax({
                    url     : U.generate(options.href),
                    data    : options.query,
                    method  : 'get',
                    dataType: 'json',
                    success : function (data) {

                        if ('responseText' in data) {
                            D.error.call(options, 'OBJECT NOT FOUND');
                        } else {

                            if (D.isEnable('stat'))
                                options.stat.ajax.size = xhr.responseText.length;

                            options.response = data;
                            options.json = Cache.init(options);

                            if (data.hasOwnProperty('lastItem'))
                                options.lastItem = data.lastItem;

                            D.log(['Render.json:', options.href, 'JSON from AJAX:', options.json], 'warn');
                            R.sortJSON(options);
                        }

                    },
                    error   : function (data) {
                        D.error.call(options, [data && (data.message || data.responseJSON && data.responseJSON.message || data.statusText) || 'OBJECT NOT FOUND', options.href, data.status]);
                    },
                    timeout : R.timeout.ajax
                });
            }

        },

        "sortJSON": function (options) {

            if (options.json && typeof options.json === 'object' && isNumeric(Object.keys(options.json)[0])) {
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

        "renderTMPL": function (options, partial) {

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
                    url     : U.generate(template, 'tmpl') + '?' + Config.siteVersion,
                    method  : 'get',
                    dataType: 'text',
                    success : function (data) {

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
                    error   : function (data) {
                        D.error.call(options, [data && (data.message || data.responseJSON && data.responseJSON.message || data.statusText) || 'TEMPLATE NOT FOUND', options.href, data.status]);
                    },
                    timeout : R.timeout.template
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

            D.log(["Complete:", options], 'render');

            var render = options.rendered = DOM.create(options.rendered),
                node = options.node,
                template = false,
                onTop = DOM.visible('.on-top').length;

            D.log(['Render.inputHTML into:', node.id], 'render');

            if (node) {

                if ('nodeType' in render) {

                    if (options.lastItem && U.parse(options.init.template) === render.id) {
                        var infiniteScrolling = render.querySelector('button[name="submit"]');
                        if (infiniteScrolling) {
                            infiniteScrolling.parentNode.removeChild(infiniteScrolling);
                        }
                    }

                    if (render.id == node.id) {

                        if (!render.classList.contains('pop-box')) {
                            if (Tools.compareArrays(render.classList, ['content-box', 'content-box-item', 'content-main'])) {
                                //such as blog-post-view, games-online & games-chance
                                !onTop && DOM.hide(node.parentNode && node.parentNode.children, 'pop-box');
                            }
                        } else {
                            options.url = false;
                        }

                        if (node.parentNode) {
                            node.parentNode.replaceChild(render, node);
                            !onTop && DOM.show(node);
                            console.log('replaceChild');
                        }

                    } else {

                        if (Tools.compareArrays(render.classList, ['content-main'])) {
                            //such as games & blog & lottery
                            node = document.getElementById('content');
                        }

                        if (Tools.compareArrays(render.classList, ['content-box'])) {
                            //such as view
                            node = document.getElementById('content');
                            render.classList.add('slideInRight');
                        } else if (Tools.compareArrays(render.classList, ['content-box-item'])) {
                            // for content-box-item
                            node = node.getElementsByClassName('content-box-content')[0];
                        }

                        if (!node)
                            node = document.getElementById('content');

                        if (!render.classList.contains('pop-box')) {
                            if (Tools.compareArrays(render.classList, ['content-box-item', 'content-main'])) {
                                //such as games-online & games-chance
                                !onTop && DOM.hide(node.children, 'pop-box');
                            } else if (node.id == 'content') {
                                !onTop && DOM.hide(node.children, 'pop-box');
                            }
                        } else {
                            options.url = false;
                        }

                        onTop && !render.classList.contains('pop-box') && DOM.hide(render, 'pop-box');

                        if (options.init.template.indexOf('-item') !== -1)
                            template =
                                Cache.get(
                                    U.parse(options.init.template, 'tmpl')
                                        .replace('-item', '-list'), 'templates')
                                || Cache.get(
                                    U.parse(options.init.template, 'tmpl')
                                        .replace('-item', ''), 'templates');

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
            R.event('complete', options);

            if (options.after) {
                D.log(['Render.after', typeof options.after], 'render');
                options.after(options);
            }

            if (callback = Callbacks['get'][U.parse(options.init.template, 'tmpl')]) {
                D.log(['Callbacks.get', U.parse(options.init.template, 'tmpl')], 'render');
                callback(options);
            }

            if (options.target && options.target.parentNode) {

                // console.debug('!!!!!render call!!',options.target.parentNode, '!!!!!\t\t', $(options.target).closest('header').length );
                var items = options.target.parentNode;

                // FIX! 4 multiple active header nav
                if( $(items).closest('header, #menu-navigation-mobile').length ){
                    $('header .active[href], #menu-navigation-mobile .active[href]').removeClass('active');
                }

                if (!options.target.classList.contains('content-box-tab')) {
                    items = items.parentNode;
                }

                if (options.url !== false)
                    options.url = true;

                items = items && items.getElementsByClassName('active') || [];
                for (var i = 0; i < items.length; i++) {
                    items[i].classList.remove('active');
                }
                options.target.classList.add('active');

            }

            //console.error(options.tab, options.rendered, options.node);

            if (options.rendered && options.rendered.classList && options.rendered.classList.contains('content-main')
                || (!options.rendered && options.tab && options.node && options.node.classList && options.node.classList.contains('content-main'))) {
                var boxes = options.rendered && options.rendered.getElementsByClassName('content-box-tabs') || options.node.getElementsByClassName('content-box-tabs');
                for (var i = 0; i < boxes.length; i++) {
                    var tab = options.tab && boxes[i].querySelector('.content-box-tab[href="/'+options.tab+'"]') || boxes[i].getElementsByClassName('content-box-tab')[0];
                    tab && DOM.click(tab);
                }
                if (options.url !== false)
                    options.url = true;
            }

            // garbage collector
            var contentBoxes = document.getElementById('content').children;
            if (contentBoxes && contentBoxes.length)
                for (var index = 0; index < contentBoxes.length; index ++)
                    if (contentBoxes[index].classList.contains('content-box') && contentBoxes[index].style.display === 'none')
                        DOM.remove(contentBoxes[index]);

            D.log(['Render.afterHTML callback:', U.parse(options.init.template, 'tmpl')], 'render');
            U.update(options);

            Content.infiniteScrolling();

            if (D.isEnable('stat')) {
                options.stat.after.timer -= new Date().getTime();
                options.stat.total.size = options.stat.ajax.size + options.stat.templates.size;
                options.stat.total.timer -= new Date().getTime();
                D.stat(options);
            }

        },

        "initNode": function (options) {

            var node = U.parse(options.href); //options.template;
            if (!options.node && node) {
                if (options.node = document.getElementById(options.template)) { // U.parse(node, 'tmpl')
                } else if (options.node = document.getElementById(node)) {
                } else if (options.node = DOM.byId(node, true)) {
                } else if (!/\d+$/.test(options.href.split('?')[0]) && (options.node = DOM.byId(U.parse(node,'tmpl'), true))) {
                } else if (options.node = document.getElementById('content')) { }
            }

            return options;

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

                    if (options.hasOwnProperty('node') && options.node) {
                        DOM.remove('.modal-loading', options.node);
                    }

                    if(options.hasOwnProperty('href')) {
                        console.log('R.rendering:', JSON.stringify(R.rendering));
                        R.rendering.splice(R.rendering.indexOf(options.href), 1);
                    }

                    break;

                case "stop":
                    D.log('Render.stop', 'render');
                    R.isRendering = false;
                    break;

                case "error":
                    D.log('Render.error', 'render');
                    R.rendering = [];
                    R.event('stop');
                    break;
            }
        }

    };

})();