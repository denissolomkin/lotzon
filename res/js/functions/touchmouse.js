(function () {

    /*

     mousedown - click
     mouseup - unclick
     mouseenter - hover
     mouseleave - unhover

     */

    /* LISTENERS */
    Listeners = {

        options: {
            draggable: '.m .card',
            droppable: '.Durak .table'
        },

        init: function () {

            var tables = document.querySelectorAll(this.options.droppable),
                cards = document.querySelectorAll(this.options.draggable);

            for (var i = 0; i < tables.length; i++) {
                tables[i].classList.add('droppable');
            }

            for (var i = 0; i < cards.length; i++) {
                var card = cards[i];
                card.addEventListener('mouseenter', Mouse.enter, false);
                card.addEventListener('mousedown', Mouse.down, false);
                card.addEventListener('touchstart', Touch.start, false);
                card.addEventListener('touchmove', Touch.move, false);
                card.addEventListener('touchend', Touch.end, false);
                card.classList.add('draggable');
            }
        },

        clear: function () {

            var draggableCards = document.getElementsByClassName('draggable'),
                droppableTables = document.getElementsByClassName('droppable');

            for (var i = 0; i < draggableCards.length; i++) {
                var card = draggableCards[i];
                card.removeEventListener('mouseenter', Mouse.enter);
                card.removeEventListener('mouseleave', Mouse.leave);
                card.removeEventListener('mousedown', Mouse.down);
                card.removeEventListener('touchstart', Touch.start);
                card.removeEventListener('touchmove', Touch.move);
                card.removeEventListener('touchend', Touch.end);
                card.classList.remove('draggable');
            }

            for (var i = 0; i < droppableTables.length; i++) {
                droppableTables[i].classList.remove('droppable');
            }
        }
    };

    /* DRAG */
    Drag = {

        position: {},
        options : {},

        start: function () {

            Drag.options.target.classList.add('small-card');
            Drag.options.target.classList.remove('transition'); // remove transition for drag quickly
            Drag.options.target.style.top = (Number(parseFloat(Drag.options.target.style.top)) + 20) + 'px';

            var cardBounding = Drag.options.target.getBoundingClientRect();

            Drag.position = {
                top   : Drag.options.event.clientY,
                left  : Drag.options.event.clientX,
                fixX  : Drag.options.target.offsetLeft - (cardBounding.left + cardBounding.width / 2), //Drag.options.target.offsetLeft - Drag.options.event.clientX,
                fixY  : Drag.options.target.offsetTop - (cardBounding.top + cardBounding.height / 2), //Drag.options.target.offsetTop - Drag.options.event.clientY,
                startX: Drag.options.target.style.left,
                startY: Drag.options.target.style.top
            };

            Drag.status('start');
        },

        move: function () {

            Drag.status('move');
            Drag.options.target.style.left = Drag.options.event.clientX + Drag.position.fixX + 'px';
            Drag.options.target.style.top = Drag.options.event.clientY + Drag.position.fixY + 'px';

            /*
             Drag.options.target.style.left = parseFloat(Drag.position.startX) + Drag.options.event.clientX - Drag.position.fixX  + 'px';
             Drag.options.target.style.top = parseFloat(Drag.position.startY) + Drag.options.event.clientY - Drag.position.fixY  + 'px';
             */

        },

        drop: function () {

            Drag.status('drop');

            var table = Drag.check();

            if (!table) {
                Drag.rollback();
            } else {
                Drag.freeze();
                WebSocketAjaxClient(null, {
                    action: 'move',
                    table : table.getAttribute('data-table'),
                    cell  : Drag.options.target.getAttribute('data-card')
                });
            }

            return table;
        },

        freeze: function () {

            Drag.status('fix');
            Drag.options.target.classList.add('freezed');
            Drag.options.target.classList.add('transition');

            /*
             Drag.options.target.style.left = Drag.options.event.clientX + Drag.position.fixX + 'px';
             Drag.options.target.style.top = Drag.options.event.clientY + Drag.position.fixY + 'px';
             */

        },

        check: function () {

            if (Drag.options.target) {

                var droppableTables = document.getElementsByClassName('droppable');
                for (var i = 0; i < droppableTables.length; i++) {

                    var table = droppableTables[i].getBoundingClientRect(),
                        dragEndposition = Drag.options.target.getBoundingClientRect(),
                        center = {
                            x: parseInt((dragEndposition.right + dragEndposition.left) / 2),
                            y: parseInt((dragEndposition.top + dragEndposition.bottom) / 2)
                        };

                    if (table.bottom > center.y
                        && table.top < center.y
                        && table.right > center.x
                        && table.left < center.x) {
                        return droppableTables[i];
                    }
                }
            }

            return false;
        },

        rollback: function () {

            Cards.removeClassFromAll('small-card');
            Cards.removeClassFromAll('select');

            if (Drag.options.target) {
                Drag.status('rollback');
                console.log('Drag.rollback:', Drag.options.target);
                Drag.options.target.classList.add('transition');
                Drag.options.target.style.top = Drag.position.startY;
                Drag.options.target.style.left = Drag.position.startX;
            }

            Cards.marginsDraw();
            Drag.empty();
        },

        status: function (status) {


            if (typeof statusDiv === 'undefined') {

                DOM.insert('<div id="statusDiv" style="z-index:10000;position: fixed;bottom:0;right:50%;background: white;"></div>', document.getElementsByTagName('body')[0]);
                statusDiv = document.getElementById('statusDiv');

            } else {
                // console.log('Type: ' + Drag.options.type + ' Status: ' + status);
            }

            statusDiv.innerHTML =
                'Type: ' + Drag.options.type +
                '<br>Status: ' + status;
            /* +
             '<br>Client: ' + Drag.options.event.clientX + 'x' + Drag.options.event.clientY +
             '<br>Fix: ' + Drag.position.fixX + 'x' + Drag.position.fixY +
             '<br>Size: ' + Drag.options.target.offsetWidth + 'x' + Drag.options.target.offsetHeight +
             '<br>Offset: ' + Drag.options.target.offsetLeft + 'x' + Drag.options.target.offsetTop +
             '<br>Style: ' + Drag.options.target.style.left + 'x' + Drag.options.target.style.top;
             */
        },

        empty: function () {
            Drag.options = Drag.position = {};
        }
    };

    /* MOUSE */
    Mouse = {

        down: function (e) {

            e.preventDefault();

            // if right mouse button - exit
            if (e.buttons !== 1) {
                return false;
            }

            var target = e.target;
            target.removeEventListener('mouseleave', Mouse.leave); // prevent transition if leave

            // add listeners for drag logic
            document.addEventListener('mousemove', Mouse.move, false);
            document.addEventListener('mouseup', Mouse.up, false);

            // remove mouseenter/mouseleave events from all cards
            var draggableCards = document.getElementsByClassName('draggable');
            for (var i = 0; i < draggableCards.length; i++) {
                draggableCards[i].removeEventListener('mouseenter', Mouse.enter);
            }

            if (Drag.options && Drag.options.target !== target)
                Drag.rollback();

            Drag.options = {
                type  : 'mouse',
                event : e,
                target: e.target
            };

            Drag.start();
        },

        move: function (e) {

            Drag.options.event = e;
            Drag.move();

        },

        up: function (e) {

            e.preventDefault();

            console.log('Mouse.up');

            // e.target.classList.remove('select');

            // remove mouseup/mousemove card
            document.removeEventListener('mouseup', Mouse.up);
            document.removeEventListener('mousemove', Mouse.move);

            // add mouseenter/mouseleave events to all cards
            var cards = document.querySelectorAll(Listeners.options.draggable);
            for (var i = 0; i < cards.length; i++) {
                cards[i].addEventListener('mouseenter', Mouse.enter, false);
            }

            Drag.options.event = e;
            Drag.drop.call(this)
        },

        enter: function (e) {

            console.log('Mouse.enter');

            var target = e.target;

            target.addEventListener('mouseleave', Mouse.leave, false);

            if (Drag.options && Drag.options.target !== e.target)
                Drag.rollback();

            // if mouse enter not from other card - add transition
            if (e.relatedTarget && !e.relatedTarget.classList.contains('card')) {
                var draggableCards = document.getElementsByClassName('draggable');
                for (var i = 0; i < draggableCards.length; i++) {
                    if (!draggableCards[i].classList.contains('transition'))
                        draggableCards[i].classList.add('transition');
                }
            }

            // if mouse enter from card - redraw cards
            else {
                // remove mouseenter/mouseleave events from all cards
                Cards.marginsDraw();
                // Cards.removeClassFromAll('transition');
            }

            Cards.eachCardLeft(target);
            // target.classList.add('select');
        },

        leave: function (e) {

            console.log('Mouse.leave');

            var target = e.target;
            target.removeEventListener('mouseleave', Mouse.leave);

            // for transition at leaving card when leave from cards
            if (e.relatedTarget && !e.relatedTarget.classList.contains('card')) {
                // target.classList.add('transition');
            }

            // if leave to card remove .transition
            else {
                // target.classList.remove('transition');
            }

            Cards.marginsDraw();

            // Cards.removeClassFromAll('select');
        }
    };

    /* TOUCH */
    Touch = {

        startPosition: {},
        moved        : false,
        moveTarget   : null,

        start: function (e) {

            e.preventDefault();
            Cards.removeClassFromAll('transition');

            // hack for iOS
            var newPos = {
                pageX: e.touches[0].pageX,
                pageY: e.touches[0].pageY
            };

            Touch.startPosition = newPos;
            Touch.moved = false;
            Touch.moveTarget = e.target;

            e.target.classList.add('select');

            Cards.eachCardLeft(e.target);
        },

        move: function (e) {

            e.preventDefault();

            var newPosition = e.touches[0],
                touchTarget = document.elementFromPoint(newPosition.pageX, newPosition.pageY);

            /* slide right-left */
            if (touchTarget && !Touch.moved && Touch.moveTarget != touchTarget && touchTarget.classList.contains('card')) {



                //Cards.removeClassFromAll('select');
                Cards.marginsDraw();
                Cards.removeClassFromAll('transition');

                // hack for iOS
                var newPos = {
                    pageX: newPosition.pageX,
                    pageY: newPosition.pageY
                };


                // set new target for drag
                Touch.startPosition = newPos;
                Touch.moved = false;
                Touch.moveTarget = touchTarget;

                Cards.removeClassFromAll('select');
                touchTarget.classList.add('select');
                Cards.eachCardLeft(touchTarget);

            } else

            /* move card */
            if (Touch.moved || Touch.startPosition.pageY > newPosition.pageY + 20) {

                Drag.options = {
                    type  : 'touch',
                    event : e.targetTouches[0],
                    target: Touch.moveTarget
                };

                if (!Touch.moved) {
                    Drag.start();
                    Touch.moved = true;
                }

                Drag.move();

            }
        },

        end: function (e) {

            e.preventDefault();

            if (Touch.moved) {

                Drag.options.event = e.changedTouches[0];
                // Drag.options.target = Touch.moveTarget;
                Drag.drop.call(this);

            } else {
                Cards.marginsDraw();
            }

            Cards.removeClassFromAll('select');
            //

        }

    };


})();