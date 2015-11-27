(function () {

    // URL Handler
    U = {

        "href": null,
        "path": {

            "put": "/",
            "post": "/",
            "delete": "/",
            "get": "/",
            "tmpl": "/res/tmpl/"

        },

        "generate": function (url, type) {

            type = type || 'get';

            switch (type) {

                case "post":
                case "delete":
                case "put":
                    url = U.parse(url.replace(document.location.origin, ""), 'url');
                    break;

                case "tmpl":
                    url = url.split('-')[0] + '/' + url + '.html';
                    break;

                default:
                case "get":
                    url = U.parse(url, 'get');
                    url = U.parse(url, 'url');
                    break;

            }

            return U.path[type] + url;
        },

        "parse": function (url, type) {

            switch (type) {

                case "url":
                    return url.replace(/^\//, "").replace(/-/g, '/');
                    break;

                case "anchor":
                    return this.parse(url).replace(/.*\#/, "");
                    break;

                case "get":
                    return this.parse(url);
                    break;

                case "tmpl":
                    return url.replace(/-\d+/g, '-view');
                    break;

                case "undo":
                default:
                    return typeof url == 'object'
                        ? url
                        : url.replace(document.location.origin, "").replace(/^\//, "").replace(/\/|=/g, '-');
                    break;
            }

        },

        "update": function (options) {

            var url = null;

            if (options.url === true) {
                url = typeof options.init.href != 'object' ? U.parse(options.init.href) : options.init.template

                if (url) {


                    url = '/' + U.parse(url,'url');
                    this.href = url;

                    if (url !== window.location.pathname) {
                        console.log(options.init);
                        D.log(['updateURL:', url], 'info');
                        history.pushState(options.init, "Lotzon", url);
                    }

                }
            }
        },

        "isAnchor": function (url) {
            return (url.indexOf('#') !== -1);
        }

    };


    /* ========================================================= */
    /* ========================================================= */


})();