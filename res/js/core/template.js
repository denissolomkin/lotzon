$(function () {

    Handlebars.JavaScriptCompiler.prototype.nameLookup = function(parent, name, type) {
        if (parent === "helpers") {
            if (Handlebars.JavaScriptCompiler.isValidJavaScriptVariableName(name))
                return parent + "." + name;
            else
                return parent + "['" + name + "']";
        }

        if (/^[0-9]+$/.test(name)) {
            return parent + "[" + name + "]";
        } else if (Handlebars.JavaScriptCompiler.isValidJavaScriptVariableName(name)) {
            // ( typeof parent.name === "function" ? parent.name() : parent.name)
            return "(typeof " + parent + "." + name + " === 'function' ? " + parent + "." + name + "() : " + parent + "." + name + ")";
        } else {
            return "(typeof " + parent + "['" + name + "'] === 'function' ? " + parent + "['" + name + "']() : " + parent + "." + name + ")";
        }
    };

    Handlebars.registerHelper('player', function(fn, options) {

        console.log(fn);
        var response = eval("Player." + fn.toString());

        console.log(response);

        return typeof response === 'function' && response() || response;
    });

    Handlebars.registerHelper('i18n', function(key) {
        return M.i18n(key);
    });

    /*
    Handlebars.registerHelper('list', function(items, options) {

        var out = "<ul>";

        for(var i=0, l=items.length; i<l; i++) {
            out = out + "<li>" + options.fn(items[i]) + "</li>";
        }

        return out + "</ul>";
    });

    Handlebars.registerHelper('link', function(text, url) {
        text = Handlebars.Utils.escapeExpression(text);
        url  = Handlebars.Utils.escapeExpression(url);

        var result = '<a href="' + url + '">' + text + '</a>';

        return new Handlebars.SafeString(result);
    });
    */

    // Template Handler
    Template = {

        "key": 'templateStorage',
        "validity": 'templateValidity',
        "storage": {},
        "compiledStorage": {},
        "enabledStorage": false,

        "init": function (initTemplates) {

            this.enabledStorage = (typeof localStorage !== 'undefined');

            this.compile(this.enabledStorage && this.load());
            this.compile(initTemplates);
            this.save();

            return this;

        },

        "load": function () {
            return JSON.parse(localStorage.getItem(this.key));
        },

        "save": function () {
            this.enabledStorage && localStorage.setItem(this.key, JSON.stringify(this.storage));
            return this;
        },

        "has": function (key) {
            return this.compiledStorage.hasOwnProperty(key);
        },

        "get": function (key) {
            return this.compiledStorage.hasOwnProperty(key) && this.compiledStorage[key];
        },

        "set": function (key, template) {

            return this.compile(key, template)
                .save()
                .get(key);

        },

        "compile": function (templatesOrKey, perhapsTemplate) {

            if(!templatesOrKey)
                return false;

            else if (perhapsTemplate){
                this.storage[templatesOrKey] = perhapsTemplate;
                this.compiledStorage[templatesOrKey] = Handlebars.compile(perhapsTemplate);
            }

            else if(typeof templatesOrKey === 'object')
                for (var key in templatesOrKey)
                    if (isNumeric(key))
                        this.compile(templatesOrKey[key]);
                    else{
                        this.storage[key] = templatesOrKey[key];
                        this.compiledStorage[key] = Handlebars.compile(templatesOrKey[key]);
                    }


            return this;

        }

    }

});