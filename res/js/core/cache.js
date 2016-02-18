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
            "validity" : {
                "cache"    : 'cacheValidity',
                "templates": 'templatesValidity',
                "languages": 'languagesValidity'
            }
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

                if (init.update)
                    this.update(init.update);

                if (init.callback) {
                    eval(init.callback);
                }

                if (init.hasOwnProperty('badges') && Object.size(init.badges)) {

                    var badges = ['notifications', 'messages'];

                    for (var i = 0; i < badges.length; i++) {
                        if (init.badges.hasOwnProperty(badges[i]) && Object.size(init.badges[badges[i]])) {
                            R.push({
                                template: 'badges-' + badges[i] + '-list',
                                json    : init.badges[badges[i]].filter(function (el) {
                                    return !document.getElementById('badges-' + badges[i] + '-' + el.key + (el.id ? '-' + el.id : ''));
                                })
                            });
                        }
                    }
                }

                if (key) {
                    return this.set(key, init);
                } else if (init.res) {
                    this.push(init.res);
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
            }

            for (var key in this.storages)
                if (typeof this[key] !== 'function' && this.storage[this[key]] && (!session || key === this.storages.session)) {
                    this.storage[this[key]] = {};
                }

            return this.init();
        },

        "detect": function () {

            if (this.isEnabled === null)
                this.isEnabled = typeof localStorage !== 'undefined';
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
                        = storage[this.storages.local]
                        = storage[this.storages.session]
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
                    storage[this.storages.validity.cache] = JSON.parse(localStorage.getItem(this.storages.validity.cache)) || {};
                    storage[this.storages.validity.templates] = JSON.parse(localStorage.getItem(this.storages.validity.templates)) || {};
                    storage[this.storages.validity.languages] = JSON.parse(localStorage.getItem(this.storages.validity.languages)) || {};
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

        "find": function (path) {
            return this.get(path, null, true);
        },

        "get": function (path, storage, isFind) {

            var cache,
                needle;

            switch (true) {

                case storage && typeof storage !== 'undefined':

                    cache = this.storage[this.storages[storage]];

                    if (storage === 'templates') {
                        cache = cache.hasOwnProperty(path) && cache[path];
                    } else {

                        if (typeof path === 'object') {
                            var keys = this.split(path.href),
                                offset = path.query && path.query.offset || 0;
                        } else
                            var keys = this.split(path);

                        var list = null;
                        do {
                            needle = keys.shift();
                            cache = needle && cache && cache.hasOwnProperty(needle)
                            && (isNumeric(needle)
                                ? (cache[needle].hasOwnProperty('id') && cache[needle]['id'] == needle && cache[needle])
                                : cache[needle]);
                            list = cache || list;
                        } while (keys.length && cache)

                        if (!cache && isNumeric(needle) && list) {

                            for (var index in list) {
                                if (list.hasOwnProperty(index) && list[index].hasOwnProperty('id') && list[index]['id'] == needle) {
                                    cache = list[index];
                                    needle = index;
                                    break;
                                }
                            }

                        } else if (cache && !isNumeric(needle)) {

                            if (Object.size(list) > offset) {

                                cache = {};
                                var i = 0,
                                    limit = 10;

                                for (var index in list) {
                                    if (list.hasOwnProperty(index)) {
                                        if (i >= offset) {
                                            cache[index] = list[index];
                                        }
                                        i++;
                                        if (Object.size(cache) >= limit)
                                            break;
                                    }
                                }

                            } else
                                cache = false;

                            if (cache && !Object.size(cache))
                                cache = false;
                        }
                    }

                    D.log(['Cache.get:', path, storage, cache && cache.toString()], 'cache');
                    return isFind && cache
                        ? {
                        id     : needle,
                        storage: storage,
                        object : cache
                    }
                        : cache;
                    break;

                default:

                    D.log(['Cache.get:', path, storage], 'cache');
                    return this.get(path, 'local', isFind) || this.get(path, 'session', isFind);
                    break;
            }

        },

        "set": function (key, data) {

            var path = data.key ? this.split(data.key) : this.split(key),
                needle = path.last(),
                storage = data.cache || false,
                source = (data.hasOwnProperty('res') ? data.res : data);

            this.validate(path.join('-'), true);

            if (data.player)
                Player.init(data.player);


            if (!data.key && data.res)
                while (path.length && source) {
                    needle = path.shift();
                    source = source && source.hasOwnProperty(needle) && source[needle];
                }

            /* if receive data for extend cache */
            if (source && storage) {

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
                    this.extend(data, storage)
                        .save(storage);
            }

            return source || (data.hasOwnProperty('res') ? data.res : data);

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

                var find = this.find(key);

                if (find)
                    if (this.delete(this.storages[find.storage], key))
                        this.save(this.storages[find.storage]);

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

        "update": function (object) {
            return this.push(object, null, true)
        },

        "push": function (object, key, forUpdate) {

            if (object && typeof object === 'object') {

                if (!key || !object.hasOwnProperty('id')) {

                    for (prop in object) {
                        if (object.hasOwnProperty(prop)) {
                            var keys = key && key.slice() || [];
                            keys.push(prop);
                            this.validate(keys.join('-'), true);
                            this.push(object[prop], keys, forUpdate);
                        }
                    }

                } else if (object.hasOwnProperty('id')) {

                    key = key.join('-');

                    if (forUpdate) {
                        var cache = this.find(key);
                        if (cache) {
                            Object.deepExtend(cache.object, object);
                            object = cache.object;
                            this.set(null, {
                                key  : key.replace(/-\d+$/g, '-' + cache.id),
                                res  : object,
                                cache: cache.storage
                            });
                        } else {
                            return;
                        }
                    }

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

        "extend": function (data, storage) {

            var source = data.res || data;

            if (data.key) {
                var path = this.split(data.key);
                while (path.length) {

                    var temp = {},
                        key = path.pop();

                    temp[key] = source;
                    source = temp;

                }
            }

            D.log(['Cache.extend', storage, path, source], 'cache');

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

        "partials": function (template) {

            var matches = [];
            template.replace(
                /(?:partial\b\s.)([\w]+[\-\w*]*)/igm,
                function (m, p) {
                    if(matches.indexOf(p) < 0)
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