(function () {

    // URL Handler
    U = {

        "init": function(){
            document.location.origin = document.location.origin || document.location.protocol+'//'+document.location.host;
        },
        "href": null,
        "isReloadPage": false,
        "path": {

            "put": "/",
            "post": "/",
            "delete": "/",
            "get": "/",
            "tmpl": "/res/tmpl/"

        },

        "generate": function (url, type) {

            type = type && type.toLowerCase() || 'get';

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

            return (U.path[type] ? U.path[type] : '') + url;
        },

        "parse": function (url, type) {

            if(!url)
                return;

            switch (type) {

                case "url":
                    return url.replace(document.location.origin, "/").replace(/^\//, "").replace(/-/g, '/');
                    break;

                case "anchor":
                    return this.parse(url).replace(/.*\#/, "");
                    break;

                case "get":
                    return this.parse(url);
                    break;

                case "tmpl":
                    return this.parse(url, 'href').replace(/-\d+/g, '-view');
                    break;

                case "href":
                    return url.indexOf('?') !== -1
                        ? url.slice(0, url.indexOf('?'))
                        : url;
                    break;

                case "undo":
                default:
                    return typeof url == 'object'
                        ? url
                        : url.replace(document.location.origin, "").replace(/^\//, "").replace(/\//g, '-');//.replace(/\/|=/g, '-');
                    break;
            }

        },

        "update": function (options) {

            var url = null;

            if (options.url === true) {
                url = typeof options.init.href != 'object' ? U.parse(options.init.href) : options.init.template;

                if (url && (url = '/' + U.parse(url,'url'))) {
                    this.href = url;

                    if (url !== window.location.pathname) {
                        if(this.isReloadPage) {
                            document.location.href = url;
                        } else {
                            var oldUrl = window.location.href;
                            D.log(['updateURL:', url], 'info');
                            $("html, body").animate({scrollTop: 0}, 'slow');
                            Navigation.menu.hide();
                            if(!options.tab) {
                                history && history.pushState(options.init, "Lotzon", url);

                                Banner.update();
                                Config.hasOwnProperty('yandexMetrika')
                                && Config.yandexMetrika
                                && window['yaCounter' + Config.yandexMetrika]
                                && window['yaCounter' + Config.yandexMetrika].hit(document.location.protocol + '//' + document.location.host + url, {
                                    title  : 'Lotzon',
                                    referer: oldUrl
                                });
                            }
                        }
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