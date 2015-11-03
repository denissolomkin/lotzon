$(function () {

    // Cache Engine
    Cache = {

        "key": 'cacheStorage',
        "validity": 'cacheValidity',
        "storage": {},
        "enabledStorage": false,

        "init": function () {
            this.detectStorage();
            this.storage = this.load();
        },

        "detectStorage": function () {
            this.enabledStorage = typeof localStorage !== 'undefined' && typeof sessionStorage !== 'undefined';
            return this.enabledStorage;
        },

        "load": function (cache) {

            switch (true) {

                case !this.enabledStorage:
                    return {
                        local: {},
                        session: {}
                    };
                    break;

                case cache === 'session':
                    return JSON.parse(sessionStorage.getItem(this.key));
                    break;

                case cache === 'local':
                    return JSON.parse(localStorage.getItem(this.key));
                    break;

                default :
                    return {
                        local: JSON.parse(localStorage.getItem(this.key)) || {},
                        session: JSON.parse(sessionStorage.getItem(this.key)) || {}
                    };
                    break;

            }

        },

        "save": function (cache) {

            switch (true) {

                case !this.enabledStorage:
                    break;

                case cache === 'session':
                    sessionStorage.setItem(this.key, JSON.stringify(this.storage.session))
                    break;

                case cache === 'local':
                    localStorage.setItem(this.key, JSON.stringify(this.storage.local))
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
                    cache = this.storage[storage];

                    while (path.length && cache) {
                        needle = path.shift();
                        cache = cache && cache.hasOwnProperty(needle) && cache[needle];
                    }

                    return cache;
                    break;

                default:

                    return this.get(path, 'local') || this.get(path, 'session');
                    break;
            }

        },

        "set": function (key, data) {

            var path = data.key && (data.key.indexOf('/')!== -1 && data.key.split('/') || data.key.split('-')) || key.split('-'),
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

            return this.format(source, needle);

        },

        "extend": function (data, path, storage) {

            var source = data.res || data;

            if (data.key)
                while (path.length) {

                    var temp = {},
                        key = path.pop();

                    temp[key] = source;
                    source = temp;

                }

            Object.deepExtend(this.storage[storage === 'session' ? storage : 'local'], source);
            return this;

        },

        "format": function (data, needle) {

            return data;

            /* fix absent mustache each object, replacing with array */
            switch (typeof data) {
                case 'object':
                    if (!isNumeric(needle) || !data.length)
                        data = {'items': format4Mustache(data)};
                    break;
                case 'string':
                    /* nothing */
                    break;
                case 'boolean':
                    /* nothing */
                    break;
            }

            return data;

        }

    };

});