(function () {

    EventListener = {

        detect: null,
        body: null,
        handlers: {},

        init: function () {
            this.body = document.querySelector("body");
            this.detect = this.body.addEventListener !== undefined;
            return this;
        },

        catch: function (event) {

            event = event || window.event;
            var target = event.target || event.srcElement,
                current = target;

            loop:
                for (priority in EventListener.handlers[event.type]) {
                    for (str in EventListener.handlers[event.type][priority]) {
                        if (typeof EventListener.handlers[event.type][priority][str] === 'function') {

                            while (current !== null) {

                                if (EventListener.checkNode(str, current, target))
                                    EventListener.handlers[event.type][priority][str].call(current, event);

                                if (event.cancelBubble)
                                    break loop;

                                current = current.parentNode;
                            }

                            current = target;

                        } else {

                        }
                    }


                }
        },

        checkNode: function (str, current, target) {

            var check = false,
                contains = ['[', '(', ':'],
                isSimple = true;

            while (contains.length && isSimple)
                isSimple = str.indexOf(contains.shift()) === -1 && str.indexOf('.',1) === -1;


            if (isSimple) {
                switch (str[0]) {

                    case ".":
                        if (current.classList && current.classList.contains(str.replace(".", '')))
                            check = true;
                        break;

                    case "#":
                        if (current.id === str.replace("#", ''))
                            check = true;
                        break;

                    default:
                        if (current.tagName && current.tagName.toUpperCase() === str.toUpperCase())
                            check = true;
                        break;
                }
            } else {

                var elements = current.parentNode && current.parentNode.querySelectorAll(str) || [];
                for (var i = 0; i < elements.length; i++) {
                    if (elements[i].contains(target)) {
                        check = true;
                        break;
                    }
                }
            }

            return check;
        },

        on: function (event, el, func, priority) {

            if (this.detect === null) {
                D.error('First init EventListener');
                return false;
            }

            priority = priority || 10;

            if (!this.handlers[event]) {
                this.handlers[event] = {};
                if (this.detect)
                    this.body.addEventListener(event, this.catch, true);
                else
                    this.body.addEvent("on" + event, this.catch);
            }

            // { event: [ ... ] }
            if (typeof event === 'object') {
                for (type in event) {
                    this.on(type, event[type]);
                }

                // event, string, func, [...]
            } else if (typeof event === 'string') {

                // event, string, func, [...]
                if (typeof el === 'string')
                    this.attach(event, el, func, priority);
                else
                    for (var i = 0; i < el.length; i++) { // event, []

                        // event, [string, string], func, [...]
                        if (typeof el[i] === 'string') {
                            this.attach(event, el[i], func, priority);

                            // event, [ {string: func, [...]} , ... ]
                        } else if (el[i].toString() === '[object Object]') {
                            this.attach(event, Object.keys(el[i])[0], el[i][Object.keys(el[i])[0]], el[i][1] ? el[i][1] : priority);

                            // event, [ [string, func, [...]] ]
                        } else if (typeof el[i][0] === 'string') {
                            this.attach(event, el[i][0], el[i][1], el[i][2] ? el[i][2] : priority);

                            // event, [ [ [string, string], func, [...]] ]
                        } else {
                            for (var y = 0; y < el[i][0].length; y++) {
                                this.attach(event, el[i][0][y], el[i][1], el[i][2] ? el[i][2] : priority);
                            }
                        }

                    }
            }


        },

        attach: function (event, el, func, priority) {

            if (!this.handlers[event][priority])
                this.handlers[event][priority] = {};

            if (el.indexOf(' ') === -1) {
                this.handlers[event][priority][el] = func;
            } else {
                this.handlers[event][priority][el] = {
                    selectors: el.split(' '),
                    function: func
                };
            }

        },

        off: function (event, el) {
            if (arguments.length === 2) {
                for (priority in this.handlers[event])
                    if (this.handlers[event][priority][el]) {
                        delete this.handlers[event][priority][el];
                        if (!this.handlers[event][priority].length)
                            delete this.handlers[event][priority];
                        if (!this.handlers[event].length)
                            delete this.handlers[event];
                    }
            } else {
                for (event in this.handlers) {
                    this.off(event, el);
                }
            }

            return this;
        }


    };

})();