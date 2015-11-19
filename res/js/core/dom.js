(function () {

    DOM = {

        getId: function (el) {

            if (!(typeof el === 'object' && "nodeType" in el)) {
                el = DOM.create(el).children()[0];
            }

            return el.id;
        },

        append: function (str, el) {

            if (typeof str === 'string')
                str = DOM.create(str);

            if (str) {
                if (str.length)
                    while (str.length > 0) {
                        el.appendChild(str[0]);
                    }
                else
                    el.appendChild(str);
            }

        },

        create: function (str) {
            var div = document.createElement('div');
            div.innerHTML = str;
            return div.children;
        },

        all: function (f, el, parent) {

            if (typeof el === 'string') {
                el = parent && parent.querySelectorAll(el) || document.querySelectorAll(el);
            }

            if (el && el.length) {
                for (i in el) {
                    el.hasOwnProperty(i) && f(el[i]);
                }
            }
        },

        show: function f(el, parent) {

            if (typeof el === 'object' && "nodeType" in el) {
                el.style.display = "block";
            } else
                this.all(f, el, parent)

        },

        hide: function f(el, parent) {

            if (typeof el === 'object' && "nodeType" in el) {
                el.style.display = "none";
            } else
                this.all(f, el, parent)

        },

        is: function (node, query) {

            var div = document.createElement('div');
            div.appendChild = node;
            return div.querySelectorAll(query);

            var regexId = new RegExp(query.join('|'));
            var regexTag = new RegExp(query.join('|'));
            var regexClass = new RegExp(query.join('|'));

            return regexClass.test(node.classList.toString()) || regexId.test(node.id) || regexTag.test(node.tagName)
        },

        fadeOut: function f(el, parent) {

            if (typeof el === 'object' && "nodeType" in el) {

                el.style.opacity = 1;

                (function fade() {
                    if ((el.style.opacity -= .1) < 0) {
                        el.style.display = "none";
                    } else {
                        requestAnimationFrame(fade);
                    }
                })();

            } else
                this.all(f, el, parent)

        },

        fadeIn: function f(el, parent) {

            if (typeof el === 'object' && "nodeType" in el) {

                el.style.opacity = 0;
                el.style.display = display || "block";

                (function fade() {
                    var val = parseFloat(el.style.opacity);
                    if (!((val += .1) > 1)) {
                        el.style.opacity = val;
                        requestAnimationFrame(fade);
                    }
                })();

            } else
                this.all(f, el, parent)
        },

        remove: function f(el, parent) {

            if (typeof el === 'object' && "nodeType" in el) {
                el.remove();
            } else
                this.all(f, el, parent)
        },

        toggle: function f(el, parent) {

            if (typeof el === 'object' && "nodeType" in el) {
                DOM.isVisible(el) ? DOM.fadeOut(el) : DOM.fadeIn(el);
            } else
                this.all(f, el, parent)
        },

        visible: function (el, parent) {

            var visibleElements = [];

            if (typeof el === 'string')
                el = parent && parent.querySelectorAll(el) || document.querySelectorAll(el);

            for (i in el)
                if (el.hasOwnProperty(i) && DOM.isVisible(el[i]))
                    visibleElements.push(el[i]);

            return visibleElements;
        },

        isVisible: function (obj) {

            if (obj == document)
                return true;

            if (!obj || !obj.parentNode || obj.style.opacity === '0' || obj.style.visibility === 'hidden' || obj.style.display === 'none' || obj.clientWidth == 0) {
                return false;
            }

            return DOM.isVisible(obj.parentNode);
        },

        onScreen: function (el) {
            var rect = el.getBoundingClientRect(),
                vpH = (window.innerHeight || document.documentElement.clientHeight),
                st = document.body.scrollTop,
                y = document.body.scrollTop + rect.top;

            return (y < (vpH + st)) && (y > (st - rect.height));
        }
    };

})();