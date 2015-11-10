$(function () {

    // Cache Engine
    Cache = {

        "storage": {},
        "compiledStorage": {},

        "storages": {
            "templates": 'templatesStorage',
            "languages": 'languagesStorage',
            "local": 'localStorage',
            "session": 'sessionStorage',
            "cache": 'cacheStorage',
            "validity": 'cacheValidity'
        },

        "templates": 'templatesStorage',
        "languages": 'languagesStorage',
        "local": 'localStorage',
        "session": 'sessionStorage',
        "cache": 'cacheStorage',
        "validity": 'cacheValidity',

        "isEnabled": null,
        "selectedLanguage": null,

        "init": function () {

            D.log('Cache.init', 'cache');
            this.detect() // set is enabled storage
                .load() // compile templates
                .compile() // compile templates
                .localize(); // loading required language
        },

        "drop": function () {

            this.detect();

            D.log(['Cache.drop'], 'cache');

            if (this.isEnabled) {
                localStorage.clear();
                sessionStorage.clear();
            }

            for (var key in this.storages)
                if (typeof this[key] !== 'function' && this.storage[this[key]]) {
                    this.storage[this[key]] = {};
                }

            return this;
        },

        "detect": function () {

            this.isEnabled = typeof localStorage !== 'undefined' && typeof sessionStorage !== 'undefined'
            D.log(['Cache.detect:', this.isEnabled], 'cache');

            return this;
        },

        "localize": function () {

            this.selectedLanguage = Player.language.current;
            D.log(['Cache.localize:', this.selectedLanguage, this.storage[this.storages.languages].hasOwnProperty(this.selectedLanguage)], 'cache');

            include('/res/js/libs/moment-locale/' + this.selectedLanguage.toLowerCase() + '.js');

            if (!this.language(this.selectedLanguage)) {

                $.ajax({
                    url: '/res/languages/' + this.selectedLanguage,
                    method: 'get',
                    dataType: 'json',
                    statusCode: {

                        404: function (data) {
                            throw(data.message);
                        },

                        200: function (data) {
                            D.log(['Cache.localize DONE:', Cache.selectedLanguage, data], 'cache');
                            Cache.language(Cache.selectedLanguage, data);
                            Cache.ready();
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

            } else
                this.ready();
        },

        "ready": function () {
            Navigation.load();
        },

        "load": function () {

            D.log(['Cache.load'], 'cache');

            switch (true) {

                case !this.isEnabled:
                    var storage = {};
                    storage[this.storages.templates]
                        = storage[this.storages.languages]
                        = storage[this.storages.validity]
                        = storage[this.storages.local]
                        = storage[this.storages.session]
                        = {};
                    this.storage = storage;
                    return this;
                    break;

                case this.isEnabled:
                    var storage = {};
                    storage[this.storages.templates] = JSON.parse(localStorage.getItem(this.templates)) || {};
                    storage[this.storages.languages] = JSON.parse(localStorage.getItem(this.languages)) || {};
                    storage[this.storages.validity] = JSON.parse(localStorage.getItem(this.validity)) || {};
                    storage[this.storages.local] = JSON.parse(localStorage.getItem(this.cache)) || {};
                    storage[this.storages.session] = JSON.parse(sessionStorage.getItem(this.cache)) || {};
                    this.storage = storage;
                    return this;
                    break;

            }

        },

        "save": function (cache) {

            D.log(['Cache.save:', cache], 'cache');

            switch (true) {

                case !this.isEnabled:
                    break;

                case cache === this.session:
                    sessionStorage.setItem(this.storages.cache, JSON.stringify(this.storage[this.storages.session]));
                    break;

                case cache === this.local:
                    localStorage.setItem(this.storages.cache, JSON.stringify(this.storage[this.storages.local]));
                    break;

                case cache === this.templates:
                    localStorage.setItem(this.storages.templates, JSON.stringify(this.storage[this.storages.templates]));
                    break;

                case cache === this.languages:
                    localStorage.setItem(this.storages.languages, JSON.stringify(this.storage[this.storages.languages]));
                    break;

                case cache === this.validity:
                    localStorage.setItem(this.storages.validity, JSON.stringify(this.storage[this.storages.validity]));
                    break;

                case !cache:
                    for (var key in this.storages)
                        if (typeof this.storages[key] !== 'function' && this.storage[this.storages[key]])
                            this.save(this.storages[key]);
                    break;
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

                        path = path.split('-');

                        while (path.length && cache) {
                            needle = path.shift();
                            cache = cache && cache.hasOwnProperty(needle) && cache[needle];
                        }
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

            var path = data.key && (data.key.indexOf('/') !== -1 && data.key.split('/') || data.key.split('-')) || key.split('-'),
                needle = path.last(),
                cache = data.cache || false,
                source = data.res || data;

            /* if receive data for extend cache */
            if (cache) {
                this.extend(data, path, cache)
                    .save(cache);
            }

            if (!data.key && data.res)
                while (path.length && source) {
                    needle = path.shift();
                    source = source && source.hasOwnProperty(needle) && source[needle];
                }

            return source || data.res || data;

        },

        "size": function () {

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


            var source = data.res || data;

            D.log(['Cache.extend', storage, path, source], 'cache');

            switch (true) {
                case storage === 'session':
                    storage = this.storages.session;
                    break;
                case storage === null:
                case storage === 'local':
                case storage:
                case isNumeric(storage):
                    storage = this.storages.local;
                    break;
                default:
                    storage = storage;
                    break;

            }

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

        "partials": function (key) {

            matches = [];
            this.storage[this.storages.templates][key].replace(
                /(?:partial\b\s.)([\w]+[\-\w*]*)/igm,
                function (m, p1) {
                    matches.push(p1);
                }
            );
            return matches;

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

            if (typeof key === 'object') {
                var args = [];
                for (var i = 0; i <= key.length; i++)
                    typeof key[i] === 'string' && key[i] !== 'i18n' && args.push(key[i]);
                key = args.join('-');
            }

            D.log(['Cache.i18n:', key], 'i18n');

            var lang = this.language(Player.language.current);
            key = key && key.replace(/\/|=/g, '-');
            return lang && lang[key] || key;
        }
    };

});