(function () {

    // Cache Engine
    Cache = {

        "default": {
            limit: 10,
            order: "ASC"
        },
        "storage": {},
        "compiledStorage": {},
        "path2": {
            "languages": '/res/languages/',
            "momentLocale": '/res/js/libs/moment-locale/'
        },

        "storages": {
            "templates": 'templatesStorage',
            "languages": 'languagesStorage',
            "local": 'localStorage',
            "session": 'sessionStorage',
            "model": 'modelStorage',
            "validity": {
                "cache": 'cacheValidity',
                "templates": 'templatesValidity',
                "languages": 'languagesValidity'
            }
        },

        "isEnabled": null,
        "selectedLanguage": null,

        /*
         * Common Cache engine
         * */

        /* check is localStorage enable */
        "detect": function () {

            if (this.isEnabled === null)
                this.isEnabled = typeof localStorage !== 'undefined';
            D.log(['Cache.detect:', this.isEnabled], 'cache');

            return this;
        },

        /* point for enter to cache */
        "init": function (init, options) {

            if (init) {

                if (init.player) {
                    Player.init(init.player);
                }

                if (init.tickets)
                    Tickets.init(init.tickets);

                if (init.delete)
                    this.remove(init.delete);

                if (init.drop)
                    this.remove(init.drop, null, true);

                if (init.update)
                    this.update(init.update);

                if (init.callback) {
                    eval(init.callback);
                }

                if (init.hasOwnProperty('badges') && Object.size(init.badges)) {
                    Content.badge.init(init.badges);
                }

                /* for response by R.json */
                if (init.response) {
                    return this.init(init.response, init);

                /* for R.push() with JSON == object */
                } else if (init.json) {
                    return this.set(init);

                /* for 2nd lap after init.response */
                } else if (options) {
                    options.json = init;
                    return this.set(options);


                    /* for ??? */
                } else if (init.res) {
                    this.push(init.res, init.hasOwnProperty('cache') && init.cache);
                }

            } else {
                D.log('Cache.init', 'cache');
                return this.detect() // set is enabled storage
                    .load() // load from storage to memory
                    .compile() // compile templates
                    .localize(); // loading required language
            }
        },

        /* desc: callback after localize ready */
        "ready": function () {
            if(Navigation) {
                Navigation.load();
                error && D.error(error);
            } else {
                setTimeout(Cache.ready, 200);
            }
        },

        /* desc: load storages from localStorage to Memory */
        "load": function () {

            D.log(['Cache.load'], 'cache');

            var storage = {};

            switch (true) {

                case !this.isEnabled:
                    storage[this.storages.templates]
                        = storage[this.storages.languages]
                        = storage[this.storages.local]
                        = storage[this.storages.session]
                        = storage[this.storages.model]
                        = storage[this.storages.validity.cache]
                        = storage[this.storages.validity.templates]
                        = storage[this.storages.validity.languages]
                        = {};
                    break;

                case this.isEnabled:
                    storage[this.storages.templates] = JSON.parse(localStorage.getItem(this.storages.templates)) || {};
                    storage[this.storages.languages] = JSON.parse(localStorage.getItem(this.storages.languages)) || {};
                    storage[this.storages.local] = JSON.parse(localStorage.getItem(this.storages.local)) || {};
                    storage[this.storages.session] = JSON.parse(localStorage.getItem(this.storages.session)) || {};
                    storage[this.storages.model] = JSON.parse(localStorage.getItem(this.storages.model)) || {};
                    storage[this.storages.validity.cache] = JSON.parse(localStorage.getItem(this.storages.validity.cache)) || {};
                    storage[this.storages.validity.templates] = JSON.parse(localStorage.getItem(this.storages.validity.templates)) || {};
                    storage[this.storages.validity.languages] = JSON.parse(localStorage.getItem(this.storages.validity.languages)) || {};
                    break;

            }

            this.storage = storage;
            return this;

        },

        /* desc: clear only session data or all localStorage and Memory*/
        "drop": function (session) {

            this.detect();

            D.log(['Cache.drop'], 'cache');

            if (this.isEnabled) {

                if (!session) {
                    localStorage.clear();
                } else {
                    localStorage.setItem(this.storages.session, null);
                }
            }

            for (var key in this.storages)
                if (typeof this[key] !== 'function' && this.storage[this[key]] && (!session || key === this.storages.session)) {
                    this.storage[this[key]] = {};
                }

            return this.init();
        },

        "stat": function () {

            var total = 0,
                cache = {},
                storages = ['localStorage', 'sessionStorage'];

            for (var s in storages) {
                for (var x in window[storages[s]]) {

                    (!this.storage.hasOwnProperty(x))
                    continue;

                    var amount = (localStorage[x].length * 2) / 1024 / 1024;
                    total += amount;
                    cache[storages[s]][x] = amount.toFixed(2) + " MB";
                }
            }

            cache['total'] = total.toFixed(2) + " MB";
            return cache;
        },

        /* save storages from Memory to localStorage */
        "save": function (cache) {

            D.log(['Cache.save:', cache], 'cache');

            try {

                switch (true) {

                    case !this.isEnabled:
                        break;

                    case cache === this.storages['session']:
                        localStorage.setItem(this.storages['session'], JSON.stringify(this.storage[this.storages['session']]));
                        break;

                    case cache === this.storages['local']:
                        localStorage.setItem(this.storages['local'], JSON.stringify(this.storage[this.storages['local']]));
                        break;

                    case cache === this.storages['templates']:
                        localStorage.setItem(this.storages['templates'], JSON.stringify(this.storage[this.storages['templates']]));
                        break;

                    case cache === this.storages['languages']:
                        localStorage.setItem(this.storages['languages'], JSON.stringify(this.storage[this.storages['languages']]));
                        break;

                    case cache === this.storages['model']:
                        localStorage.setItem(this.storages['model'], JSON.stringify(this.storage[this.storages['model']]));
                        break;

                    case cache === this.storages['validity']['cache']:
                        localStorage.setItem(this.storages['validity']['cache'], JSON.stringify(this.storage[this.storages['validity']['cache']]));
                        break;

                    case cache === this.storages['validity']['templates']:
                        localStorage.setItem(this.storages['validity']['templates'], JSON.stringify(this.storage[this.storages['validity']['templates']]));
                        break;

                    case cache === this.storages['validity']['languages']:
                        localStorage.setItem(this.storages['validity']['languages'], JSON.stringify(this.storage[this.storages['validity']['languages']]));
                        break;

                    /* todo save all validity */
                    case !cache:
                        for (var key in this.storages)
                            if (typeof this.storages[key] !== 'function' && this.storage[this.storages[key]])
                                this.save(this.storages[key]);
                        break;
                }

            } catch (err) {
                D.error('Cache сrach');
                Cache.isEnabled = false;
            }

            return this;
        },


        /*
         * Work with JSON
         * */

        "set": function (options) {

            // key, data, storage

            var path = options.json.key ? this.splitPath(options.json.key) : this.splitPath(options.href),
                needle = path.last(),
                storage = options.json.cache || options.storage || false,
                source = (options.json.hasOwnProperty('res') ? options.json.res : options.json);

            this.validate(path.join('-'), true);

            if (!options.json.key && options.json.res){
                options.json.key = path.join('-');
                while (path.length && source) {
                    needle = path.shift();
                    source = source && source.hasOwnProperty(needle) && source[needle];
                }
                source && delete options.json.key;
            }

            /* if receive data for extend cache */
            if ((!source || Object.size(source)) && (storage = this.checkStorage(storage))) {

                if (options.hasOwnProperty('query'))
                    this.model(U.parse(options.href), {
                        limit: parseInt(options.query.limit) || this.default.limit,
                        order: options.query.order || this.default.order
                    });

                this.extend(options.json, storage)
                    .save(storage);
            }

            return source || (options.json.hasOwnProperty('res') ? options.json.res : options.json);

        },

        /* check if JSON need to be save to storage
         * return: storage */
        "checkStorage": function(storage, path) {

            switch (true) {

                case storage === 'session':
                case storage === this.storages['session']:
                    storage = this.storages['session'];
                    break;

                case storage === 'local':
                case storage === this.storages['local']:
                case storage === 'true':
                case storage === true:
                case isNumeric(storage):
                    storage = this.storages['local'];
                    break;

                case storage && path && typeof storage === 'object':
                    path = path.split('-');
                    while (path.length) {
                        if (storage.hasOwnProperty(path.join('-')))
                            return (this.checkStorage(storage[path.join('-')]));
                        path.pop();
                    }
                    storage = false;
                    break;

                default:
                    storage = false;
                    break;

            }

            return storage;
        },

        /* try find JSON from storages
         * return: id, storage, needle  */
        "find": function (path) {
            return this.get(path, null, true);
        },

        /* try get JSON
         * return: json*/
        "get": function (path, storage, isFind) {


            switch (true) {

                case storage === 'templates':

                    return this.storage[this.storages[storage]].hasOwnProperty(path) && this.storage[this.storages[storage]][path];
                    break;

                case storage && typeof storage !== 'undefined':

                    var cache = this.getFromStorage(path, storage, isFind);
                    D.log(['Cache.get:', path, storage, cache && cache.toString()], 'cache');
                    return cache;
                    break;

                default:

                    D.log(['Cache.get:', path, storage], 'cache');
                    return this.get(path, 'local', isFind) || this.get(path, 'session', isFind);
                    break;
            }

        },

        /* try get JSON from storages
         * return: json*/
        "getFromStorage": function (path, storage, isFind) {


            var cache = this.storage[this.storages[storage]],
                needle = '',
                list = {},
                keys = [];

            if (typeof path === 'object') {
                keys = path.hasOwnProperty('href') ? this.splitPath(path.href) : path.slice();
            } else {
                keys = this.splitPath(path);
            }

            do {
                needle = keys.shift();
                cache = needle && cache && cache.hasOwnProperty(needle)
                    && (isNumeric(needle) && !keys.length
                        ? (cache[needle].hasOwnProperty('id') && cache[needle]['id'] == needle && cache[needle])
                        : cache[needle]);
                list = cache || list;
            } while (keys.length && cache);

            if (!cache && isNumeric(needle) && list) {

                for (var index in list) {
                    if (list.hasOwnProperty(index) && list[index].hasOwnProperty('id') && list[index]['id'] == needle) {
                        cache = list[index];
                        needle = index;
                        break;
                    }
                }

            } else if (cache && !isNumeric(needle) && Object.size(cache)) { // ???? && cache[Object.keys(cache)[0]].hasOwnProperty('id')

                var offset = path.query && path.query.offset || 0;

                if (Object.size(list) > offset) {

                    var count = 0,
                        filters = {},
                        keys = Object.keys(list),
                        model = this.model(U.parse(path.href || path));

                    model.order = model.order || path.query && path.query.order || this.default.order;
                    model.limit = model.limit || path.query && path.query.limit || this.default.limit;

                    cache = {};

                    if (path.query)
                        for (var filter in path.query)
                            if (['limit', 'order', 'offset', 'before_id', 'after_id'].indexOf(filter) == -1)
                                filters[filter] = path.query[filter];

                    switch (model.order) {

                        default:
                        case 'ASC':

                            var index = 0;

                            do {

                                if (this.match(list[keys[index]], filters) && ++count)
                                    if (count > offset)
                                        cache[keys[index]] = list[keys[index]];

                                index++;

                            } while (Object.size(cache) < model.limit && index < keys.length);

                            break;

                        case 'DESC':

                            var index = keys.length;

                            do {

                                index--;

                                if (this.match(list[keys[index]], filters) && ++count)
                                    if (count > offset)
                                        cache[keys[index]] = list[keys[index]];

                            } while (Object.size(cache) < model.limit && index > 0);

                            break;
                    }

                } else
                    cache = false;

                if (cache && !Object.size(cache))
                    cache = false;

            } else {
                cache = false;
            }

            return isFind && cache
                ? {
                id: needle,
                storage: storage,
                object: cache
            }
                : cache;
        },

        /* order, limit options for stored objects */
        "model": function (path, data) {

            if (path)
                if (data) {
                    this.storage[this.storages['model']][path] = data;
                    return this.save(this.storages['model']);
                } else
                    return this.storage[this.storages['model']].hasOwnProperty(path) ? this.storage[this.storages['model']][path] : {};

            return {};
        },

        "match": function (object, filters) {

            if (Object.size(filters))
                for (var filter in filters)
                    if (!object.hasOwnProperty(filter) || object[filter] != filters[filter]) {
                        return false;
                    }
            return true;
        },

        /* try to remove objects by path from storages and DOM */
        "remove": function (object, key, onlyDrop) {

            if (object && (Object.size(object) || typeof object !== 'object')) {

                if (Array.isArray(object)) {

                    for (var i = 0; i < object.length; i++) {

                        var keys = key && key.slice() || [];
                        if (typeof object[i] !== 'object') {
                            keys.push(object[i]);
                            object[i] = null;
                        }

                        this.remove(object[i], keys, onlyDrop);
                    }

                } else if (typeof object === 'object') {

                    for (var prop in object) {
                        if (object.hasOwnProperty(prop)) {

                            var keys = key && key.slice() || [];
                            keys.push(prop);
                            this.remove(object[prop], keys, onlyDrop);

                        }
                    }

                } else {

                    var keys = key && key.slice() || [];
                    keys.push(object);
                    this.remove(null, keys, onlyDrop);
                }

            } else {

                D.log('Cache.remove:' + key.join('-'));

                if(!onlyDrop)
                    DOM.remove(document.getElementById(key.join('-')));

                var find = this.find(key);

                if (find) {
                    key = key.slice(0, -1);
                    key.push(find['id']);
                    if (this.delete(this.storages[find.storage], key)) {
                        this.save(this.storages[find.storage]);
                    }
                }

            }
        },

        /* try to delete object from storage */
        "delete": function (obj, path) {

            if (isString(obj))
                obj = this.storage[obj];

            if (isNumber(path)) {
                path = [path];
            }

            if (isEmpty(obj)) {
                return void 0;
            }

            if (isEmpty(path)) {
                return obj;
            }

            if (isString(path)) {
                return this.delete(obj, this.splitPath(path));
            }

            var currentPath = getKey(path[0]);
            var oldVal = obj[currentPath];

            if (path.length === 1) {
                if (oldVal !== void 0) {
                    if (isArray(obj)) {
                        obj.splice(currentPath, 1);
                    } else {
                        delete obj[currentPath];
                    }
                }
            } else {
                if (obj[currentPath] !== void 0) {
                    this.delete(obj[currentPath], path.slice(1));
                    return true;
                }
            }

            return obj;
        },

        splitPath: function (path) {
            if (!path)
                return [];
            else if (!isArray(path)) {
                path = path.indexOf('.') !== -1 && path.split('.')
                    || path.indexOf('-') !== -1 && path.split('-')
                    || path.split('/');
            }
            return path.filter(Boolean);
        },

        /* update part of object*/
        "update": function (object) {
            return this.push(object, false, true)
        },

        /* push new object to storage */
        "push": function (object, storage, forUpdate, key) {

            if (object && typeof object === 'object') {

                if (!key || !object.hasOwnProperty('id')) {

                    for (prop in object) {
                        if (object.hasOwnProperty(prop)) {
                            var keys = key && key.slice() || [];
                            keys.push(prop);
                            this.validate(keys.join('-'), true);
                            this.push(object[prop], storage, forUpdate, keys);
                        }
                    }

                } else if (object.hasOwnProperty('id')) {

                    /* replace for real id*/
                    key = key.join('-');
                    var cache = null;

                    /* update Object in Storage */
                    if (forUpdate) {
                        cache = this.find(key.replace(/-\d+$/g, '-' + object.id));
                        if (cache) {
                            Object.deepExtend(cache.object, object);
                            object = cache.object;
                            this.set({
                                href: key.replace(/-\d+$/g, '-' + cache.id),
                                json: {res: object},
                                storage: cache.storage
                            });
                        } else {
                            return false;
                        }
                    }

                    /* append to DOM */
                    if (node = DOM.byId(key.replace(/-\d+$/g, '-' + object.id), 1)) {
                        R.push({
                            href: key.replace(/-\d+$/g, '-item'),
                            node: node,
                            json: object
                        });
                    }

                    /* save Object to Storage*/
                    if (storage){
                        if((cache = this.checkStorage(storage, key))){
                            this.set({
                                href: key,
                                json: {res: object},
                                storage: cache
                            });
                        }
                    }

                }
            }
        },

        /* extend storage by object */
        "extend": function (data, storage) {

            var source = data.hasOwnProperty('res') ? data.res : data;

            if (data.key) {
                var path = this.splitPath(data.key);
                while (path.length) {

                    var temp = {},
                        key = path.pop();

                    temp[key] = source;
                    source = temp;

                }
            }

            D.log(['Cache.extend', storage, path, data], 'cache');

            if (source)
                Object.deepExtend(this.storage[storage], source);

            return this;

        },

        "validate": function (key, forUpdateOrStorage, forUpdate) {

            if (key) {
                if (typeof forUpdateOrStorage === 'boolean') {
                    if (forUpdateOrStorage) {
                        this.storage[this.storages['validity']['cache']][key] = Livedate.fn.now();
                        return this.save(this.storages['validity']['cache']);
                    }
                } else {
                    forUpdateOrStorage = forUpdateOrStorage || 'cache';
                    return this.storage[this.storages['validity'][forUpdateOrStorage]][key];
                }
            }

        },

        /*
         * Work with Templates
         * */

        "partials": function (template) {

            var matches = [];
            template.replace(
                /(?:partial\b\s.)([\w]+[\-\w*]*)/igm,
                function (m, p) {
                    if (matches.indexOf(p) < 0)
                        matches.push(p);
                }
            );
            return matches;

        },

        "hasTemplate": function (key) {
            return this.compiledStorage.hasOwnProperty(key);
        },

        "template": function (key, template) {

            D.log(['Cache.template:', key, template], 'cache');

            if (template) {
                return this.compile(key, template)
                    .save(this.storages.templates)
                    .template(key);

            } else {
                return this.compiledStorage.hasOwnProperty(key) && this.compiledStorage[key];
            }
        },

        "compile": function (templatesOrKey, perhapsTemplate) {


            if (!templatesOrKey)
                templatesOrKey = this.storage[this.storages.templates];

            D.log(['Cache.compile:', templatesOrKey, perhapsTemplate], 'cache');

            if (perhapsTemplate) {

                this.storage[this.storages.templates][templatesOrKey] = perhapsTemplate;
                this.compiledStorage[templatesOrKey] = Handlebars.compile(perhapsTemplate);

            } else if (typeof templatesOrKey === 'object') {

                for (var key in templatesOrKey) {
                    if (isNumeric(key)) {
                        this.compile(templatesOrKey[key]);
                    } else {
                        this.storage[this.storages.templates][key] = templatesOrKey[key];
                        this.compiledStorage[key] = Handlebars.compile(templatesOrKey[key]);
                    }
                }

            }


            return this;

        },

        /*
         * Work with language
         * */

        /* desc: load language dictionary */
        "localize": function () {

            this.selectedLanguage = Player.language.current;
            D.log(['Cache.localize:', this.selectedLanguage, this.storage[this.storages.languages].hasOwnProperty(this.selectedLanguage)], 'cache');

            include(this.path2.momentLocale + this.selectedLanguage.toLowerCase() + '.js');

            if (!this.language(this.selectedLanguage)) {

                $.ajax({
                    url: this.path2.languages + this.selectedLanguage,
                    method: 'get',
                    dataType: 'json',
                    success: function (data) {
                        D.log(['Cache.localize DONE:', Cache.selectedLanguage, data], 'cache');
                        Cache.language(Cache.selectedLanguage, data);
                        Cache.ready();
                    },
                    error: function (data) {
                        D.error('LANGUAGE ERROR: ' + data.message);
                    }
                });

            } else
                this.ready();

            return this;
        },

        "language": function (key, language) {

            D.log(['Cache.language:',
                key,
                language], 'i18n');

            if (language) {

                return this.extend(language, this.storages.languages)
                    .save(this.storages.languages)
                    .language(key);

            } else {
                return this.storage[this.storages.languages] && this.storage[this.storages.languages][key];
            }
        },

        "i18n": function (key) {

            var lang = this.language(Player.language.current);

            if (typeof key === 'object') {
                var args = [];
                for (var i = 0; i <= key.length; i++)
                    typeof key[i] === 'string' && key[i] !== 'i18n' && args.push(key[i]);
                key = args.join('-');
            }

            D.log(['Cache.i18n:', key], 'i18n');
            key = key && key.replace(/\/|=/g, '-');
            return lang && lang[key] || key;
        }
    };

    i18n = function (key) {
        return Cache.i18n(key);
    };

})
();