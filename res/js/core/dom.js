(function () {

    DOM = {

        getId: function (el) {

            if (typeof el === 'string' || !(typeof el === 'object' && "nodeType" in el)) {
                el = DOM.create(el).children()[0];
            }

            return el.id;
        },

        append: function (str, el) {

            this.insert(str, el);

        },

        prepend: function (str, el) {

            this.insert(str, el, true);

        },

        insert: function (str, el, prepend) {

            if (typeof str === 'string')
                str = DOM.create(str);

            if (str) {

                if (str.length && !("nodeType" in str)) {
                    while (str.length > 0) {
                        !prepend
                            ? el.appendChild(str[0])
                            : el.insertBefore(str[0], el.firstChild);
                    }
                } else {
                    !prepend
                        ? el.appendChild(str)
                        : el.insertBefore(str, el.firstChild);
                }
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

        byId: function (id, level) {

            var node = document.getElementById(id),
                level = typeof level !== 'undefined' && level || false,
                l = 0;

            while (!node && id && id.match(/(?:\w+)(?:-\w+)+$/) && (!level || l < level)) {
                id = id.replace(/-(\w+)$/, "");
                node = document.getElementById(id) || document.getElementById(id+'-list');
                l++;
            }

            return node;
        },

        show: function f(el, parent) {

            if (typeof el === 'object' && "nodeType" in el) {
                el.style.display = "block";
            } else
                this.all(f, el, parent)

        },

        scroll: function f(el) {

            if (typeof el === 'object' && "nodeType" in el) {

                if (!!el && el.scrollIntoView) {
                    el.scrollIntoView(false);
                } else {
                    window.scroll(0, DOM.position(el));
                }
            } else
                this.all(f, el)
        },

        cursor: function f(el, parent) {

            if (typeof el === 'object' && "nodeType" in el) {
                console.log(el);
                var rng, sel;
                if (document.createRange) {
                    rng = document.createRange();
                    rng.selectNodeContents(el);
                    rng.collapse(false); // схлопываем в конечную точку
                    sel = window.getSelection();
                    sel.removeAllRanges();
                    sel.addRange(rng);
                } else { // для IE нужно использовать TextRange
                    var rng = document.body.createTextRange();
                    rng.moveToElementText(el);
                    rng.collapseToEnd();
                    rng.select();
                }

            } else
                this.all(f, el, parent)
        },

        position: function (el) {
            var curtop = 0;
            if (el.offsetParent) {
                do {
                    curtop += el.offsetTop;
                } while (el = el.offsetParent);
                return [curtop];
            }
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
                el.style.display = "block";

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