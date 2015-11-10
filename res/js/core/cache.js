$(function () {

    // Cache Engine
    Cache = {

        "storage": {},
        "compiledStorage": {},

        "templates": 'templatesStorage',
        "languages": 'languagesStorage',
        "local": 'localStorage',
        "session": 'sessionStorage',
        "cache": 'cacheStorage',
        "validity": 'cacheValidity',

        "isEnabled": null,
        "selectedLanguage": null,

        "init": function () {

            D.log(['Cache.init'], 'cache');
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

            for (var key in this)
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
            D.log(['Cache.localize:', this.selectedLanguage, this.storage[this.languages].hasOwnProperty(this.selectedLanguage)], 'cache');

            include('/res/js/libs/moment-locale/'+this.selectedLanguage.toLowerCase()+'.js');

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
                    storage[this.templates]
                        = storage[this.languages]
                        = storage[this.validity]
                        = storage[this.local]
                        = storage[this.session]
                        = {};
                    this.storage = storage;
                    return this;
                    break;

                case this.isEnabled:
                    var storage = {};
                    storage[this.templates] = JSON.parse(localStorage.getItem(this.templates)) || {};
                    storage[this.languages] = JSON.parse(localStorage.getItem(this.languages)) || {};
                    storage[this.validity] = JSON.parse(localStorage.getItem(this.validity)) || {};
                    storage[this.local] = JSON.parse(localStorage.getItem(this.cache)) || {};
                    storage[this.session] = JSON.parse(sessionStorage.getItem(this.cache)) || {};
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
                    sessionStorage.setItem(this.cache, JSON.stringify(this.storage[this.session]));
                    break;

                case cache === this.local:
                    localStorage.setItem(this.cache, JSON.stringify(this.storage[this.local]));
                    break;

                case cache === this.templates:
                    localStorage.setItem(this.templates, JSON.stringify(this.storage[this.templates]));
                    break;

                case cache === this.languages:
                    localStorage.setItem(this.languages, JSON.stringify(this.storage[this.languages]));
                    break;

                case cache === this.validity:
                    localStorage.setItem(this.validity, JSON.stringify(this.storage[this.validity]));
                    break;

                case !cache:
                    for (var key in this)
                        if (typeof this[key] !== 'function' && this.storage[this[key]])
                            this.save(this[key]);
                    break;
            }

            return this;
        },

        "get": function (path, storage) {


            var cache,
                needle;

            switch (true) {

                case typeof storage !== 'undefined':

                    path = path.split('-');
                    cache = this.storage[this[storage]];

                    while (path.length && cache) {
                        needle = path.shift();
                        cache = cache && cache.hasOwnProperty(needle) && cache[needle];
                    }

                    D.log(['Cache.get:', path, storage, cache.toString()], 'cache');
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
                storages = ['localStorage','sessionStorage'];

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

        "extend": function (data, path, storage) {


            var source = data.res || data;

            D.log(['Cache.extend', storage, path, source], 'cache');

            switch (true) {
                case storage === 'session':
                    storage = this.session;
                    break;
                case storage === null:
                case storage === 'local':
                case storage:
                case isNumeric(storage):
                    storage = this.local;
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

        "template": function (key, template) {

            D.log(['Cache.template:', key, template], 'cache');

            if (template) {

                var div = document.createElement("div");
                div.innerHTML = template;
                if(div.childNodes[0].nodeType == 8) {
                    eval("var options = "+div.childNodes[0].data);
                    if (options && typeof options === 'object' && Object.size(options) && options.aliases){
                        for (alias in options.aliases) {
                            if (options.aliases.hasOwnProperty(alias)) {
                                this.compile(options.aliases[alias], template)
                            }
                        }
                    }
                }

                return this.compile(key, template)
                    .save(this.templates)
                    .template(key);

            } else {
                return this.compiledStorage.hasOwnProperty(key) && this.compiledStorage[key];
            }
        },

        "compile": function (templatesOrKey, perhapsTemplate) {


            if (!templatesOrKey)
                templatesOrKey = this.storage[this.templates];

            D.log(['Cache.compile:', templatesOrKey, perhapsTemplate], 'cache');

            if (perhapsTemplate) {

                this.storage[this.templates][templatesOrKey] = perhapsTemplate;
                this.compiledStorage[templatesOrKey] = Handlebars.compile(perhapsTemplate);

            } else if (typeof templatesOrKey === 'object') {

                for (var key in templatesOrKey) {
                    if (isNumeric(key)) {
                        this.compile(templatesOrKey[key]);
                    } else {
                        this.storage[this.templates][key] = templatesOrKey[key];
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

                return this.extend(language, null, this.languages)
                    .save(this.languages)
                    .language(key);

            } else {
                return this.storage[this.languages] && this.storage[this.languages][key];
            }
        },

        "i18n": function (key) {

            if(typeof key === 'object') {
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