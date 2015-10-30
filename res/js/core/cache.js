$(function () {

    // Cache Engine
    Cache = {

        "key": 'cacheStorage',
        "validity": 'cacheValidity',
        "storage": null,
        "enabledStorage": false,

        "init": function(){
            this.enabledStorage = typeof localStorage !== 'undefined';
            this.storage = this.enabledStorage && this.load() || {};
        },

        "load": function(){
            return this.storage = JSON.parse(localStorage.getItem(this.key));
        },

        "save": function(){
            this.enabledStorage && localStorage.setItem(this.key, JSON.stringify(this.storage));
            return this;
        },

        "get": function (key) {

            var cache = this.storage,
                path = key.split('-'),
                needle;

            while(path.length && cache){
                needle = path.shift();
                cache = cache && cache.hasOwnProperty(needle) && cache[needle];
            }

            console.log(this.storage, cache);
            return cache; // && this.format(needle, cache);

        },

        "set": function (key, data) {

            var path = key.split('-'),
                needle = path.last(),
                source = data.res ;

            /* if receive data for extend cache */

            if (data.cache !== false) {
                this.extend(source, path)
                    .save();
            }

            console.log('storage:', localStorage.getObj('Cache'));
            return this.format(source, needle);

        },

        "extend": function(source, path) {

            while(path.length){

                var temp = {},
                    key = path.pop();
                temp[key] = source;
                source = temp;

            }

            Object.deepExtend(this.storage, source);
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