$(function () {

    // URL Handler
    U = {

        "Path": {

            "Post": "/res/post/",
            "Json": "/res/json/",
            "Ajax": "/res/json/",
            "Tmpl": "/res/tmpl/"

        },

        "Generate": {
            "Post": function (url) {
                return U.Path.Post + U.Parse.Url(url);
            },

            "Ajax": function (url) {
                return U.Path.Ajax + U.Parse.Url(url);
            },

            "Json": function (url) {
                return U.Path.Json + U.Parse.Url(U.Parse.Json(url));
            },

            "Tmpl": function (url) {
                return U.Path.Tmpl + U.Parse.Url(url) + '.html';
            }
        },

        "Parse": {
            "Url": function (url) {
                return url.replace(/^\//, "").replace(/-/g, '/');
            },

            "Tmpl": function (url) {
                return url.replace(/-\d+/g, '-view');
            },

            "Json": function (url) {
                return url.replace(/-view/g, '');
            },

            "Undo": function (url) {
                if (typeof url == 'object') {
                    return url;
                } else {
                    return url.replace(document.location.origin, "").replace(/^\//, "").replace(/\/|=/g, '-');
                }
            }
        },

        "update": function (options) {

            var url = null;

            if (options.url !== false) {
                url = typeof options.href != 'object' ? options.href : options.init.template

                if (url) {

                    url = '/' + U.Parse.Url(url);
                    if (url !== window.location.pathname) {
                        console.log(options.init);
                        D.log(['updateURL:', url], 'info');
                        history.pushState(options.init, "Lotzon", url);
                    }

                }
            }
        },

        "isAnchor": function (url) {
            return (url.indexOf('#') == 0);
        }

    };


    /* ========================================================= */
    /* ========================================================= */


});