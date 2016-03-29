(function () {

    DOM = {

        isNode: function(node){
            return ('nodeType' in node);
        },

        getId: function (el) {

            if (typeof el === 'string' || !(typeof el === 'object' && "nodeType" in el)) {
                el = this.create(el).children()[0];
            }

            return el.id;
        },

        change: function(el){
            DOM.event(el, 'change');
        },

        click: function(el){
            var ev = document.createEvent("HTMLEvents");
            ev.initEvent("click", true, true);
            el.dispatchEvent(ev);
        },

        event: function(node, eventName) {
            // Make sure we use the ownerDocument from the provided node to avoid cross-window problems
            var doc;
            if (node.ownerDocument) {
                doc = node.ownerDocument;
            } else if (node.nodeType == 9) {
                // the node may be the document itself, nodeType 9 = DOCUMENT_NODE
                doc = node;
            } else {
                throw new Error("Invalid node passed to fireEvent: " + node.id);
            }

            if (node.dispatchEvent) {
                // Gecko-style approach (now the standard) takes more work
                var eventClass = "";

                // Different events have different event classes.
                // If this switch statement can't map an eventName to an eventClass,
                // the event firing is going to fail.
                switch (eventName) {
                    case "click": // Dispatching of 'click' appears to not work correctly in Safari. Use 'mousedown' or 'mouseup' instead.
                    case "mousedown":
                    case "mouseup":
                        eventClass = "MouseEvents";
                        break;

                    case "focus":
                    case "change":
                    case "blur":
                    case "select":
                        eventClass = "HTMLEvents";
                        break;

                    default:
                        throw "fireEvent: Couldn't find an event class for event '" + eventName + "'.";
                        break;
                }
                var event = doc.createEvent(eventClass);

                var bubbles = eventName == "change" ? false : true;
                event.initEvent(eventName, bubbles, true); // All events created as bubbling and cancelable.

                event.synthetic = true; // allow detection of synthetic events
                node.dispatchEvent(event, true);
            } else if (node.fireEvent) {
                // IE-old school style
                var event = doc.createEventObject();
                event.synthetic = true; // allow detection of synthetic events
                node.fireEvent("on" + eventName, event);
            }
        },

        class: function f(css, node, add) {

            if(!node)
                return;

            if (typeof node === 'object' && 'nodeType' in node){
                add ? node.classList.add(css) : node.classList.remove(css);
            } else if (Object.size(node))
                for (var el in node)
                    f(css, node[el], add);

        },

        addClass: function (css, node) {
            this.class(css, node, true);
        },

        removeClass: function (css, node) {
            this.class(css, node);
        },

        up: function (str, el) {

            switch (str[0]) {

                case ".":
                    str = str.substring(1);
                    while (el && el.classList && !el.classList.contains(str) && el.parentNode)
                        el = el.parentNode;
                    return el.classList && el.classList.contains(str) && el;
                    break;

                case "#":
                    str = str.substring(1);
                    while (el && el.id !== str && el.parentNode)
                        el = el.parentNode;
                    break;

                default:
                    str = str.toUpperCase();
                    while (el && el.nodeName !== str && el.parentNode)
                        el = el.parentNode;
                    break;
            }

            return el;

        },

        append: function (str, el) {
            this.insert(str, el);
        },

        prepend: function (str, el) {
            this.insert(str, el, true);
        },

        insert: function f(str, el, prepend) {

            if (typeof str === 'string')
                str = this.create(str);

            if (typeof el === 'string')
                el = this.byId(el);

            if(!("nodeType" in el)){
                console.error('parent is not node in DOM.insert()');
                return false;
            }

            if (str) {
                if (str.length && !("nodeType" in str)) {
                    while (str.length > 0) {
                        !prepend
                            ? el.appendChild(str[0])
                            : el.insertBefore(str[0], el.firstChild);
                    }
                } else if ("nodeType" in str) {
                    !prepend
                        ? el.appendChild(str)
                        : el.insertBefore(str, el.firstChild);

                }
            }

        },

        create: function (str) {

            var div = document.createElement('div');
            div.innerHTML = str;
            return div.children.length === 1
                ? div.children[0]
                : div.children;
        },

        all: function (f, el, parent) {

            if (typeof el === 'string') {
                el = parent && parent.querySelectorAll(el) || document.querySelectorAll(el);
            }

            if (el && el.length) {
                for (var i = 0; i < el.length; i++) {
                    f(el[i]);
                }
            }
        },

        byId: function (id, level) {

            var node = document.getElementById(id),
                level = typeof level !== 'undefined' && level || false,
                l = 0;

            while (!node && id && id.match(/(?:\w+)(?:-\w+)+$/) && (level || l < level)) {
                id = id.replace(/-(\w+)$/, "");
                node = document.getElementById(id + '-list') || document.getElementById(id);
                l++;
            }

            return node;
        },

        show: function f(el, parent) {

            if (el && typeof el === 'object' && "nodeType" in el) {
                el.style.display = "block";
            } else
                this.all(f, el, parent)

        },

        scroll: function f(el) {

            if (el && typeof el === 'object' && "nodeType" in el) {

                if (!!el && el.scrollIntoView) {
                    el.scrollIntoView(false);
                } else {
                    window.scroll(0, DOM.position(el));
                }
            } else
                this.all(f, el)
        },

        cursor: function f(el, parent) {

            if (el && typeof el === 'object' && "nodeType" in el) {
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

            if (el && typeof el === 'object' && "nodeType" in el) {
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

            if (el && typeof el === 'object' && "nodeType" in el) {

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

            if (el && typeof el === 'object' && "nodeType" in el) {

                el.style.opacity = 0;
                el.style.display = null;

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

            if (el && typeof el === 'object' && "nodeType" in el) {
                if(el.parentNode)
                    el.parentNode.removeChild(el);
            } else {
                this.all(f, el, parent)
            }
        },

        toggle: function f(el, parent) {

            if (el && typeof el === 'object' && "nodeType" in el) {
                DOM.isVisible(el) ? DOM.fadeOut(el) : DOM.fadeIn(el);
            } else
                this.all(f, el, parent)
        },

        visible: function (el, parent) {

            var visibleElements = [];

            if (typeof el === 'string')
                el = [parent && parent.querySelectorAll(el) || document.querySelectorAll(el)];

            else if (typeof el === 'object' && el.length)
                for (var i = 0; i < el.length; i++)
                    el[i] = parent && parent.querySelectorAll(el[i]) || document.querySelectorAll(el[i])

            for (var y = 0; y < el.length; y++)
                for (var i = 0; i < el[y].length; i++)
                    if (el[y].hasOwnProperty(i) && DOM.isVisible(el[y][i]))
                        visibleElements.push(el[y][i]);

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