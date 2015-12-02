(function () {

    extend = function (Child, Parent) {
        var F = function () {
        };
        F.prototype = Parent.prototype;
        Child.prototype = new F();
        Child.prototype.constructor = Child;
        Child.superclass = Parent.prototype;
    };

    requestAnimationFrame =
        typeof requestAnimationFrame === 'function' && requestAnimationFrame ||
        typeof webkitRequestAnimationFrame === 'function' && webkitRequestAnimationFrame ||
        typeof mozRequestAnimationFrame === 'function' && mozRequestAnimationFrame ||
        typeof oRequestAnimationFrame === 'function' && oRequestAnimationFrame ||
        typeof msRequestAnimationFrame === 'function' && msRequestAnimationFrame ||
        function (/* function */ draw) {
            window.setTimeout(draw, 1000 / 60);
        };

    fadeOut = function f(el) {

        if (typeof el === 'string')
            el = document.querySelectorAll(el);

        if (el && el.hasOwnProperty('length')) {
            for (i in el)
                el.hasOwnProperty(i) && f(el[i]);

        } else if (el && typeof el === 'object') {

            console.log(el);

            el.style.opacity = 1;

            (function fade() {
                if ((el.style.opacity -= .1) < 0) {
                    el.style.display = "none";
                } else {
                    requestAnimationFrame(fade);
                }
            })();
        }
    };

    fadeIn = function f(el, display) {

        if (typeof el === 'string')
            el = document.querySelectorAll(el);

        if (el && el.hasOwnProperty('length')) {
            for (i in el) {
                el.hasOwnProperty(i) && f(el[i]);
            }

        } else if (el && typeof el === 'object') {

            el.style.opacity = 0;
            el.style.display = display || "block";

            (function fade() {
                var val = parseFloat(el.style.opacity);
                if (!((val += .1) > 1)) {
                    el.style.opacity = val;
                    requestAnimationFrame(fade);
                }
            })();
        }
    };

    compare = function(array1, array2){

        var check = false;

        if(!array1 || !array2)
            return check;

        for( var i = 0; i < array1.length && !check ; i++ ) {
            check = array2.indexOf(array1[i]) !== -1;
        }

        return check;

    };

    visible = function (el, parent) {

        var visibleElements = [];

        if (typeof el === 'string')
            el = [parent && parent.querySelectorAll(el) || document.querySelectorAll(el)];

        else if (typeof el === 'object' && el.length)
            for (i in el)
                if (el.hasOwnProperty(i))
                    el[i] = parent && parent.querySelectorAll(el[i]) || document.querySelectorAll(el[i])

        for(var y=0;y<el.length;y++)
            for (i in el[y])
                if (el[y].hasOwnProperty(i) && isVisible(el[y][i]))
                    visibleElements.push(el[y][i]);

        return visibleElements;
    };

    isVisible = function (obj) {

        if (obj == document)
            return true;

        if (!obj || !obj.parentNode || obj.style.opacity === '0' || obj.style.visibility === 'hidden' || obj.style.display === 'none' || obj.clientWidth == 0) {
            return false;
        }

        return isVisible(obj.parentNode);
    };

    onScreen = function (el) {
        var rect = el.getBoundingClientRect(),
            vpH = (window.innerHeight || document.documentElement.clientHeight),
            st = document.body.scrollTop,
            y = document.body.scrollTop + rect.top;

        return (y < (vpH + st)) && (y > (st - rect.height));
    };

    isEmpty = function (value) {

        if (!value && value !== 0) {
            return true;
        } else if (Array.isArray(value) && value.length === 0) {
            return true;
        } else if (typeof value === 'object') {
            return Object.getOwnPropertyNames(value).length === 0;
        } else {
            return false;
        }
    };

    isNumeric = function (mixed_var) {
        //   example 1: isNumeric(186.31); returns 1: true
        //   example 2: isNumeric('Kevin van Zonneveld'); returns 2: false
        //   example 3: isNumeric(' +186.31e2'); returns 3: true
        //   example 4: isNumeric(''); returns 4: false
        //   example 5: isNumeric([]); returns 5: false
        //   example 6: isNumeric('1 '); returns 6: false
        var whitespace =
            " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
        return (typeof mixed_var === 'number' || (typeof mixed_var === 'string' && whitespace.indexOf(mixed_var.slice(-1)) === -
            1)) && mixed_var !== '' && !isNaN(mixed_var);
    };

    isBot = /Googlebot|Yandex/.test(navigator.userAgent);
    isFile = window.location.protocol == 'file:';
    isMobile = /android|meego|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);


    objToStr = function (data) {
        return Object.keys(data).map(function (key) {
            return key + '=' + data[key];
        }).join('&');
    };

    xhrRequest = function (method, url, data, callback, error) {

        var xhr, scriptOk = false;

        if (isIE && isIE == 9) {

            xhr = document.createElement("script");
            xhr.src = url + '?' + data + '&callback=';

            if (callback) {
                xhr.src += 'reCall';
                window.reCall = function (result) {
                    scriptOk = true;
                    callback(result);
                };

                xhr.onreadystatechange = function () {

                    if (canReplace.indexOf(document.readyState) >= 0) {
                        this.onreadystatechange = null;
                        setTimeout(function () {
                            if (!scriptOk && error) {
                                error();
                            }
                        }, 0);
                    }
                };
            }

            document.head.appendChild(xhr);

        } else {

            xhr = ("onload" in new XMLHttpRequest()) ? new XMLHttpRequest() : new XDomainRequest();

            if (error) {
                xhr.onabort = function () {
                    error('onAborted', xhr.status, xhr.readyState);
                };
                xhr.onerror = function () {
                    error('onError', xhr.status, xhr.readyState);
                };
            }

            xhr.open(method, url, true);

            xhr.onreadystatechange = function () {

                if (xhr.readyState == 4 && xhr.status == 200 && callback) {
                    if (xhr.responseText) {
                        callback(JSON.parse(xhr.responseText)); // Another callback here
                    } else if (error) {
                        error('responseText empty', xhr.status, xhr.readyState);
                    }
                } else if (error && (xhr.status == 500 )) {
                    error('500 error', xhr.status, xhr.readyState);
                }
            };

            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.send(data);
        }
    };

    include = function (src) {
        var s = document.createElement('script');
        s.setAttribute('src', src);
        document.body.appendChild(s);
    };


})();