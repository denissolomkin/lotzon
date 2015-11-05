/* LISTENERS */
Listeners = {

    options: {
        draggable: '.m .card',
        droppable: '.Durak .table'
    },

    init: function() {
        var tables = document.querySelectorAll(this.options.droppable);
        for (var i = 0; i < tables.length; i++) {
            var table = tables[i];
            table.className += ' droppable';
        }
        var cards = document.querySelectorAll(this.options.draggable);
        for (var i = 0; i < cards.length; i++) {
            var card = cards[i];
            card.addEventListener('mouseenter', Mouse.enter, false);
            card.addEventListener('mouseleave', Mouse.leave, false);
            card.addEventListener('mousedown', Mouse.start, false);
            card.addEventListener('touchstart', Touch.start, false);
            card.addEventListener('touchmove', Touch.move, false);
            card.addEventListener('touchend', Touch.end, false);
            card.className += ' draggable';
        }
    },

    clear: function() {
        var draggableCards = document.getElementsByClassName('draggable');
        for (var i = 0; i < draggableCards.length; i++) {
            var card = draggableCards[i];
            card.removeEventListener('mouseenter', Mouse.enter);
            card.removeEventListener('mouseleave', Mouse.leave);
            card.removeEventListener('mousedown', Mouse.start);
            card.removeEventListener('touchstart', Touch.start);
            card.removeEventListener('touchmove', Touch.move);
            card.removeEventListener('touchend', Touch.end);
            card.className = card.className.replace(' draggable', '');
        }
        var droppableTables = document.getElementsByClassName('droppable');
        for (var i = 0; i < droppableTables.length; i++) {
            var table = droppableTables[i];
            table.className = table.className.replace(' droppable', '');
        }
    }
};

/* DRAG */
Drag = {
    position: null,
    options: null,
    start: function() {
        Drag.status('start');
        Drag.position = {
            top   : Drag.options.event.clientY,
            left  : Drag.options.event.clientX,
            fixX  : Drag.options.target.offsetLeft - Drag.options.event.clientX,
            fixY  : Drag.options.target.offsetTop - Drag.options.event.clientY,
            startX: Drag.options.target.style.left,
            startY: Drag.options.target.style.top
        };

        Drag.options.target.style.zIndex = 10;
    },

    move: function() {
        Drag.status('move');

        Drag.options.target.style.left = Drag.options.event.clientX + Drag.position.fixX + 'px';
        Drag.options.target.style.top  = Drag.options.event.clientY + Drag.position.fixY + 'px';
    },

    drop: function() {
        Drag.status('drop');

        if (!Drag.check()) {
            Drag.rollback();
            Drag.empty();
        } else {
            WebSocketAjaxClient();
        }
    },

    check: function() {
        var droppableTables = document.getElementsByClassName('droppable');
        for (var i = 0; i < droppableTables.length; i++) {
            var table = droppableTables[i].getBoundingClientRect();
            var dragEndposition = Drag.options.target.getBoundingClientRect();
            if (dragEndposition.bottom < table.bottom && dragEndposition.bottom > table.top && dragEndposition.right > table.left && dragEndposition.left < table.right) {
                return true;
            }
        }
        return false;
    },

    rollback: function() {
        if (Drag.options) {
            Drag.status('rollback');
            Drag.options.target.className += ' transition';
            Drag.options.target.style.zIndex = 0;
            Drag.options.target.style.top = Drag.position.startY;
            Drag.options.target.style.left = Drag.position.startX;

        }
    },

    status: function(status) {
        if (typeof statusDiv != 'undefined')
            statusDiv.innerHTML = 'Type: ' + Drag.options.type + '<br>Status: ' + status;
    },

    empty: function() {
        Drag.options = Drag.position = null;
    }
};

/* MOUSE */
Mouse = {

    start: function(e) {
        // remove transition for drag quickly
        e.target.className = e.target.className.replace(' transition', '');
        // add listeners for drag logic
        document.addEventListener('mousemove', Mouse.move, false);
        document.addEventListener('mouseup', Mouse.up, false);
        // remove mouseenter/mouseleave events from all cards
        var draggableCards = document.getElementsByClassName('draggable');
        for (var i = 0; i < draggableCards.length; i++) {
            var card = draggableCards[i];
            card.removeEventListener('mouseenter', Mouse.enter);
            card.removeEventListener('mouseleave', Mouse.leave);
        }

        Drag.rollback();
        Drag.options = {
            type: 'mouse',
            event: e,
            target: e.target
        };
        Drag.start();
    },

    move: function(e) {
        Drag.options.event = e;
        Drag.move();
    },

    up: function(e) {
        // remove mouseup/mousemove card
        document.removeEventListener('mouseup', Mouse.up);
        document.removeEventListener('mousemove', Mouse.move);
        // add mouseenter/mouseleave events to all cards
        var cards = document.querySelectorAll(Listeners.options.draggable);
        for (var i = 0; i < cards.length; i++) {
            var card = cards[i];
            card.addEventListener('mouseenter', Mouse.enter, false);
            card.addEventListener('mouseleave', Mouse.leave, false);
        }

        Drag.options.event = e;
        Drag.drop();
    },

    enter: function(e) {
        var target = e.target;

        removeClassFromAll('transition');

        // if mouse enter not from other card - add transition
        if ((e.relatedTarget !== undefined )&&(e.relatedTarget !== null)) {
            if (!(e.relatedTarget.classList.contains('card')))
                target.className += ' transition';
        }
        target.className += ' select';

        eachCardLeft(target);
    },

    leave: function(e) {
        var target = e.target;

        // if leave to card remove .transition
        if ((e.relatedTarget !== undefined )&&(e.relatedTarget !== null)) {
            if (e.relatedTarget.classList.contains('card')) {
                target.className = target.className.replace(' transition', '');
            } else {
                // for transition at leaving card when leave from cards
                if (!(target.classList.contains('transition'))) {
                    target.className += ' transition';
                }
            }
        }

        removeClassFromAll('select');

        Cards.marginsDraw();
    }
};

/* TOUCH */
Touch = {

    startPosition: {},
    moved: false,
    moveTarget: null,

    start: function(e) {
        e.preventDefault();

        removeClassFromAll('transition');

        var target = e.target;

        // hack for iOS
        var newPos = {
            pageX: e.touches[0].pageX,
            pageY: e.touches[0].pageY
        };

        Touch.startPosition = newPos;
        Touch.moved         = false;
        Touch.moveTarget    = target;

        target.className += ' select';

        eachCardLeft(target);
    },

    move: function(e) {
        e.preventDefault();

        var target = e.target;
        var newPosition = e.touches[0];
        touchTarget = document.elementFromPoint(newPosition.pageX, newPosition.pageY);

        if ((Touch.moveTarget!=touchTarget)&&(touchTarget.classList.contains('card'))&&(!(Touch.moved))) {
            removeClassFromAll('select');
            Cards.marginsDraw();
            removeClassFromAll('transition');

            // hack for iOS
            var newPos = {
                pageX: newPosition.pageX,
                pageY: newPosition.pageY
            };

            // set new target for drag
            Touch.startPosition = newPos;
            Touch.moved         = false;
            Touch.moveTarget    = touchTarget;

            touchTarget.className += ' select';

            eachCardLeft(touchTarget);
        } else {
            if ((Touch.moved)||((Touch.startPosition.pageY - newPosition.pageY) > 20)) {
                if (!Touch.moved) {
                    Drag.options = {
                        type: 'touch',
                        event: e.targetTouches[0],
                        target: Touch.moveTarget
                    };
                    Drag.start();
                    Touch.moved = true;
                }
                Drag.options.event  = e.targetTouches[0];
                Drag.options.target = Touch.moveTarget;
                Drag.move();
            }

        }
    },

    end: function(e) {
        e.preventDefault();

        if (Touch.moved) {
            Drag.options.event  = e.targetTouches[0];
            Drag.options.target = Touch.moveTarget;
            Drag.drop();
        }

        removeClassFromAll('select');
        Cards.marginsDraw();
    }

};

function removeClassFromAll(classname) {
    var classList = document.getElementsByClassName(classname);
    for (i = 0; i < classList.length; i++) {
        classList[i].className = classList[i].className.replace(' '+classname, '');
    }
}

function eachCardLeft(target) {
    var next = target.nextElementSibling;
    var selectedIndex = $('.players .m .card').index(target);
    var nextIndex = $('.players .m .card').index(next);
    cards = document.querySelectorAll('.players .m .card');
    for (i = 0; i < cards.length; i++) {
        if (i < selectedIndex) {
            showedCardsWidth = 0;
            newTop = 0;
        } else if (i == selectedIndex) {
            showedCardsWidth = 0;
            newTop = -20;
        } else if (nextIndex > 0 && i == nextIndex) {
            showedCardsWidth = cardWidth * scale * 0.5;
            newTop = 0;
        } else if (nextIndex > 0 && i > nextIndex) {
            showedCardsWidth = cardWidth * 2 * scale * 0.5;
            newTop = 0;
        }
        newLeft = cards[i].style.left;
        oldTop  = cards[i].style.top;
        oldTop  = Number(oldTop.replace("px",""));
        if (myCount > 6) {
            var newLeft = (parseInt(newLeft.slice(0, newLeft.length - 2), 10) + showedCardsWidth).toFixed();
        } else {
            var newLeft = parseInt(newLeft.slice(0, newLeft.length - 2), 10).toFixed();
        }
        cards[i].style.left = newLeft + 'px';
        cards[i].style.top = Number(oldTop + newTop) + 'px';
    }
}