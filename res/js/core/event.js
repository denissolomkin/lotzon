(function () {

    Event = {

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
            var target = event.target || event.srcElement;

            loop:
                while (target !== null) {
                    for (priority in Event.handlers[event.type]) {
                        for (el in Event.handlers[event.type][priority]) {
                            switch (el[0]) {

                                case ".":
                                    if (target.classList && target.classList.contains(el.replace(".", ''))) {
                                        Event.handlers[event.type][priority][el].call(target, event);
                                    }
                                    break;

                                case "#":
                                    if (target.id === el.replace("#", '')) {
                                        Event.handlers[event.type][priority][el].call(target, event);
                                    }
                                    break;

                                default:
                                    if (target.tagName && target.tagName.toUpperCase() === el.toUpperCase()) {
                                        Event.handlers[event.type][priority][el].call(target, event);
                                    }
                                    break;
                            }

                            if (event.cancelBubble) {
                                break loop;
                            }
                        }
                    }

                    target = target.parentNode;

                }
        },

        on: function (event, el, func, priority) {

            if (this.detect === null) {
                D.error('First init Event');
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
                for(type in event){
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
            this.handlers[event][priority][el] = func;
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