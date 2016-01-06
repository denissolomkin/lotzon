(function () {

    // Cache Engine
    Cache = {

        "storage"        : {},
        "compiledStorage": {},

        "storages": {
            "templates": 'templatesStorage',
            "languages": 'languagesStorage',
            "local"    : 'localStorage',
            "session"  : 'sessionStorage',
            "validity" : 'cacheValidity'
        },

        "isEnabled"       : null,
        "selectedLanguage": null,

        "init": function (init, key) {

            if (init) {

                if (init.player)
                    Player.init(init.player);

                if (init.tickets)
                    Tickets.init(init.tickets);

                if (init.delete)
                    this.remove(init.delete);

                if (init.callback) {
                    eval(init.callback);
                }

                if (init.badges && Object.size(init.badges)) {

                    R.push({
                        template: 'badges-list',
                        json    : init.badges.filter(function (el) {
                            return !document.getElementById('badges-' + el.key + '-' + (el.id ? el.id : ''));
                        })
                    });
                }

                if (key) {
                    return this.set(key, init);
                } else if (init.res) {
                    this.update(init.res);
                }

            } else {
                D.log('Cache.init', 'cache');
                return this.detect() // set is enabled storage
                    .load() // load from storage to memory
                    .compile() // compile templates
                    .localize(); // loading required language
            }
        },

        "drop": function (session) {

            this.detect();

            D.log(['Cache.drop'], 'cache');

            if (this.isEnabled) {

                if (!session) {
                    localStorage.clear();
                } else {
                    localStorage.setItem(this.storages.session, null);
                }

                sessionStorage.clear();
            }

            for (var key in this.storages)
                if (typeof this[key] !== 'function' && this.storage[this[key]] && (!session || key === this.storages.session)) {
                    this.storage[this[key]] = {};
                }

            return this.init();
        },

        "detect": function () {

            if (this.isEnabled === null)
                this.isEnabled = typeof localStorage !== 'undefined' && typeof sessionStorage !== 'undefined';
            D.log(['Cache.detect:', this.isEnabled], 'cache');

            return this;
        },

        "localize": function () {

            this.selectedLanguage = Player.language.current;
            D.log(['Cache.localize:', this.selectedLanguage, this.storage[this.storages.languages].hasOwnProperty(this.selectedLanguage)], 'cache');

            include('/res/js/libs/moment-locale/' + this.selectedLanguage.toLowerCase() + '.js');

            if (!this.language(this.selectedLanguage)) {

                $.ajax({
                    url     : '/res/languages/' + this.selectedLanguage,
                    method  : 'get',
                    dataType: 'json',
                    success : function (data) {
                        D.log(['Cache.localize DONE:', Cache.selectedLanguage, data], 'cache');
                        Cache.language(Cache.selectedLanguage, data);
                        Cache.ready();
                    },
                    error   : function (data) {
                        D.error('LANGUAGE ERROR: ' + data.message);
                    }
                });

            } else
                this.ready();

            return this;
        },

        "ready": function () {
            Navigation.load();
        },

        "load": function () {

            D.log(['Cache.load'], 'cache');

            var storage = {};

            switch (true) {

                case !this.isEnabled:
                    storage[this.storages.templates]
                        = storage[this.storages.languages]
                        = storage[this.storages.validity]
                        = storage[this.storages.local]
                        = storage[this.storages.session]
                        = {};
                    break;

                case this.isEnabled:
                    storage[this.storages.templates] = JSON.parse(localStorage.getItem(this.storages.templates)) || {};
                    storage[this.storages.languages] = JSON.parse(localStorage.getItem(this.storages.languages)) || {};
                    storage[this.storages.validity] = JSON.parse(localStorage.getItem(this.storages.validity)) || {};
                    storage[this.storages.local] = JSON.parse(localStorage.getItem(this.storages.local)) || {};
                    storage[this.storages.session] = JSON.parse(localStorage.getItem(this.storages.session)) || {};
                    break;

            }

            this.storage = storage;
            return this;

        },

        "save": function (cache) {

            D.log(['Cache.save:', cache], 'cache');

            try {

                switch (true) {

                    case !this.isEnabled:
                        break;

                    case cache === this.storages['session']:
                        localStorage.setItem(this.storages.session, JSON.stringify(this.storage[this.storages.session]));
                        break;

                    case cache === this.storages['local']:
                        localStorage.setItem(this.storages.local, JSON.stringify(this.storage[this.storages.local]));
                        break;

                    case cache === this.storages['templates']:
                        localStorage.setItem(this.storages.templates, JSON.stringify(this.storage[this.storages.templates]));
                        break;

                    case cache === this.storages['languages']:
                        localStorage.setItem(this.storages.languages, JSON.stringify(this.storage[this.storages.languages]));
                        break;

                    case cache === this.storages['validity']:
                        localStorage.setItem(this.storages.validity, JSON.stringify(this.storage[this.storages.validity]));
                        break;

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

        "get": function (path, storage) {


            var cache,
                needle;

            switch (true) {

                case typeof storage !== 'undefined':

                    cache = this.storage[this.storages[storage]];

                    if (storage === 'templates') {
                        cache = cache.hasOwnProperty(path) && cache[path];
                    } else {

                        path = this.split(path);

                        do {
                            needle = path.shift();
                            cache = needle && cache && cache.hasOwnProperty(needle) && cache[needle];
                        } while (path.length && cache)
                    }

                    D.log(['Cache.get:', path, storage, cache && cache.toString()], 'cache');
                    return cache;
                    break;

                default:

                    D.log(['Cache.get:', path, storage], 'cache');
                    return this.get(path, 'local') || this.get(path, 'session');
                    break;
            }

        },

        "set": function (key, data) {

            var path = data.key ? this.split(data.key) : this.split(key),
                needle = path.last(),
                storage = data.cache || false,
                source = data.res || data;

            this.validate(path.join('-'), true);

            if (data.player)
                Player.init(data.player);

            /* if receive data for extend cache */
            if (storage) {

                switch (true) {

                    case storage === 'session':
                        storage = this.storages['session'];
                        break;

                    case storage === 'local':
                    case storage === 'true':
                    case storage === true:
                    case isNumeric(storage):
                        storage = this.storages['local'];
                        break;

                    default:
                        storage = false;
                        break;

                }

                if (storage)
                    this.extend(data, path, storage)
                        .save(storage);
            }


            if (!data.key && data.res)
                while (path.length && source) {
                    needle = path.shift();
                    source = source && source.hasOwnProperty(needle) && source[needle];
                }


            return source || data.res || data;

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

        "remove": function (object, key) {

            if (object && (Object.size(object) || typeof object !== 'object')) {

                if (Array.isArray(object)) {

                    for (var i = 0; i < object.length; i++) {

                        var keys = key && key.slice() || [];
                        if (typeof object[i] !== 'object') {
                            keys.push(object[i]);
                            object[i] = null;
                        }

                        this.remove(object[i], keys);
                    }

                } else if (typeof object === 'object') {

                    for (var prop in object) {
                        if (object.hasOwnProperty(prop)) {

                            var keys = key && key.slice() || [];
                            keys.push(prop);
                            this.remove(object[prop], keys);

                        }
                    }

                } else {

                    var keys = key && key.slice() || [];
                    keys.push(object);
                    this.remove(null, keys);
                }

            } else {

                D.log('Cache.remove:' + key.join('-'));
                DOM.remove(document.getElementById(key.join('-')));

                if (this.delete(this.storages.local, key))
                    this.save(this.storages.local);
                else if (this.delete(this.storages.session, key))
                    this.save(this.storages.session);

            }
        },

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
                return this.delete(obj, this.split(path));
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

        split: function (path) {
            if (!path)
                return [];
            else if (!isArray(path)) {
                path = path.indexOf('.') !== -1 && path.split('.')
                || path.indexOf('-') !== -1 && path.split('-')
                || path.split('/');
            }
            return path.filter(Boolean);
        },

        "deleteById": function () {

        },

        "update": function (object, key) {

            if (object && typeof object === 'object') {

                if (!key || !object.hasOwnProperty('id')) {

                    for (prop in object) {
                        var keys = key && key.slice() || [];
                        keys.push(prop);
                        this.update(object[prop], keys);
                    }

                } else if (object.hasOwnProperty('id')) {

                    key = key.join('-');
                    if (node = DOM.byId(key, 1)) {
                        R.push({
                            href: key.replace(/-\d+$/g, '-item'),
                            node: node,
                            json: object
                        });
                    }
                }
            }
        },

        "extend": function (data, path, storage) {

            D.log(['Cache.extend', storage, path, source], 'cache');

            var source = data.res || data;

            if (data.key)
                while (path.length) {

                    var temp = {},
                        key = path.pop();

                    temp[key] = source;
                    source = temp;

                }

            Object.deepExtend(this.storage[storage], source);
            return this;

        },

        "isValid": function () {

        },

        "validate": function (key, forUpdate) {

            if (key) {

                if (forUpdate) {
                    this.storage[this.storages['validity']][key] = Livedate.fn.now();
                    return this.save('validity');
                } else {
                    return this.storage[this.storages['validity']][key];
                }
            }

        },

        "partials": function (template) {

            matches = [];
            template.replace(
                /(?:partial\b\s.)([\w]+[\-\w*]*)/igm,
                function (m, p) {
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

        "language": function (key, language) {

            D.log(['Cache.language:',
                key,
                language], 'i18n');

            if (language) {

                return this.extend(language, null, this.storages.languages)
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