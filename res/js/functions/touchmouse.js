
    /* LISTENERS */
    Listeners = {

        options: {
            draggable: '.m .card',
            droppable: '.Durak .table'
        },

        init: function () {

	

            var tables = document.querySelectorAll(this.options.droppable);
            for (var i = 0; i < tables.length; i++) {
                var table = tables[i];
                table.className += ' droppable';
            }

            var cards = document.querySelectorAll(this.options.draggable);
            for (var i = 0; i < cards.length; i++) {
                var card = cards[i];
                card.addEventListener('mousedown', Mouse.start, false)
                card.addEventListener('touchstart', Touch.start, false)
                card.addEventListener('touchmove', Touch.move, false)
                card.addEventListener('touchend', Drag.drop, false)
                card.className += ' draggable';
            }
        },

        clear: function () {
            var draggableCards = document.getElementsByClassName('draggable');
            for (var i = 0; i < draggableCards.length; i++) {
                var card = draggableCards[i];
                card.removeEventListener('mousedown', Mouse.start)
                card.removeEventListener('touchstart', Touch.start)
                card.removeEventListener('touchmove', Touch.move)
                card.removeEventListener('touchend', Drag.drop)
                card.className = card.className.replace(' draggable', '');
            }

            var droppableTables = document.getElementsByClassName('droppable');
            for (var i = 0; i < droppableTables.length; i++) {
                var table = droppableTables[i];
                table.className = table.className.replace(' droppable', '');
            }
        }
    }

    /* DRAG */
    Drag = {
        position: null,
        options: null,
        start: function () {
            Drag.status('start')
            Drag.position = {
            	top: Drag.options.event.clientY,
            	left: Drag.options.event.clientX,
                fixX: Drag.options.target.offsetLeft - Drag.options.event.clientX,
                fixY: Drag.options.target.offsetTop - Drag.options.event.clientY,
                startX: Drag.options.target.style.left,
                startY: Drag.options.target.style.top
            }

        },

        move: function () {
            Drag.status('move')

          
	        if ((Drag.position.left - Drag.options.event.clientX) > 40 &&  (Drag.position.top - Drag.options.event.clientY) < 10 || (Drag.options.event.clientX  - Drag.position.left) > 40 && (Drag.position.top - Drag.options.event.clientY) < 10){
	        	console.log('Drag.options.target', Drag.options.target);
	        	Drag.rollback();
	        	Drag.options.target =  document.elementFromPoint(Drag.options.event.clientX, Drag.options.event.clientY);

	        	console.log('Drag.options.target2', Drag.options.target);
	        	Drag.start();
	        	
	        }
 
	        else  {
				$(Drag.options.target).css({
					left: Drag.options.event.clientX + Drag.position.fixX,
					top: Drag.options.event.clientY + Drag.position.fixY
				});

	        }


        },

        drop: function () {
            Drag.status('drop')

            if (Drag.options.type == 'mouse') {
                document.removeEventListener('mouseup', Drag.drop)
                document.removeEventListener('mousemove', Mouse.move)
            }

            if(!Drag.check()) {
                Drag.rollback();
                Drag.empty();
            }
            else {
            	 // console.log("true1");
			WebSocketAjaxClient();
            }
           
        },

        check: function () {

            var droppableTables = document.getElementsByClassName('droppable');
            for (var i = 0; i < droppableTables.length; i++) {
                var table = droppableTables[i].getBoundingClientRect();

				var dragEndposition = Drag.options.target.getBoundingClientRect();

				if (dragEndposition.bottom < table.bottom 
					&& dragEndposition.bottom > table.top  
					&& dragEndposition.right > table.left
					&& dragEndposition.left < table.right
					) 
				{

					 Listeners.clear();

					 

				}


            }

            return false;
        },

        rollback: function () {
            if (Drag.options) {
                Drag.status('rollback')
                Drag.options.target.className += ' transition'
                Drag.options.target.style.zIndex = 0;
                Drag.options.target.style.top = Drag.position.startY
                Drag.options.target.style.left = Drag.position.startX
                
            }
        },

        status: function (status) {
            if(typeof statusDiv != 'undefined')
            	statusDiv.innerHTML = 'Type: ' + Drag.options.type + '<br>Status: ' + status
        },

        empty: function () {
            Drag.options = Drag.position = null;
        }
    }

    /* MOUSE */
    Mouse = {

        start: function (e) {
            document.addEventListener('mousemove', Mouse.move, false)
            document.addEventListener('mouseup', Drag.drop, false)
            Drag.rollback();
            Drag.options = {
                type: 'mouse',
                event: e,
                target: e.target
            }
            Drag.start()
        },

        move: function (e) {
            Drag.options.event = e;
            Drag.move()
        }
    }

    /* TOUCH */
    Touch = {

        start: function touch(e) {
            Drag.rollback();
            Drag.options = {
                type: 'touch',
                event: e.targetTouches[0],
                target: e.targetTouches[0].target
            }
            Drag.start()
            // e.preventDefault()
        },


        move: function (e) {
            Drag.options.event = e.targetTouches[0];
            Drag.options.target = e.target;
            Drag.move()
        }
    }

    function eachCardLeft(showedCardsWidth, Top) {

        var Left = cards[i].style.left;
        if (myCount>6) {
            var Left = (parseInt(Left.slice(0,Left.length - 2),10) + showedCardsWidth).toFixed();
        }
        else {
            var Left = parseInt(Left.slice(0,Left.length - 2),10).toFixed();
        }
        cards[i].style.left =  Left + 'px';
        cards[i].style.top =  Top;

    }

    var mouseEnter =  function(e) {


        var target = e.target;
        target.className += ' select'

        var next = target.nextElementSibling;

        var selectedIndex = $('.players .m .card').index(target);
        var nextIndex = $('.players .m .card').index(next);
        cards = document.querySelectorAll('.players .m .card');
        for ( i=0; i < cards.length; i++) {
            if ( i < selectedIndex) {
                eachCardLeft(0,0);
            }
            if (i==selectedIndex) {
                eachCardLeft(0, '-20px');
            }
            else if (nextIndex > 0  && i == nextIndex) {

                eachCardLeft(cardWidth*scale*0.7, 0);
            }
            else if (nextIndex > 0 && i > nextIndex) {

                eachCardLeft(cardWidth*2*scale*0.7, 0);

            }
        }


    }

    var mouseLeave =  function(e) {
        var target = e.target;
        // console.log(target);

        target.className = target.className.replace(' select', '')
        marginsDraw ();
        // $('.players .m .card').removeClass('select');
    }


    var targetTouch;
    var touchStartPosition;

    var touchstart = function(e) {
        event.preventDefault();
        touchStartPosition = event.touches[0];
        targetTouch = document.elementFromPoint(touchStartPosition.pageX,touchStartPosition.pageY);
        if(targetTouch.nextElementSibling != null && targetTouch.nextElementSibling != undefined  )
        var next = targetTouch.nextElementSibling;
        var selectedIndex = $('.players .m .card').index(targetTouch);
        var nextIndex = $('.players .m .card').index(next);
        cards = document.querySelectorAll('.players .m .card');
        for (i=0; i < cards.length; i++) {

            if ( i < selectedIndex) {
                eachCardLeft(0,0);
            }
            if (i==selectedIndex) {
                eachCardLeft(0, '-20px');
            }
            else if (nextIndex > 0  && i == nextIndex) {
                eachCardLeft(cardWidth*scale*0.7, 0);
            }
            else if (nextIndex > 0 && i > nextIndex) {
                eachCardLeft(cardWidth*2*scale*0.7, 0);

            }
        }


    }

    var touchMove = function(e) {

        var touch= event.touches[0];

        if (touchStartPosition.pageX-touch.pageX > 10 || touch.pageX - touchStartPosition.pageX > 10) {
            cards = document.querySelectorAll('.players .m .card');

            for (var i = 0; i < cards.length; i++) {
                if (touch.pageX > cards[i].getBoundingClientRect().left
                && touch.pageX < cards[i].getBoundingClientRect().right
                && touch.pageY > cards[i].getBoundingClientRect().top
                && touch.pageY < cards[i].getBoundingClientRect().bottom){
                    event.preventDefault();
                    marginsDraw();
                    touchstart();
                }

            }


        }

    };

    var touchend = function(e) {
        event.preventDefault();
        // target.className = target.className.replace(' select', '')
        marginsDraw ();

    }
    marginsDraw = function () {

        marginLeftValue =
            (myCount > 6
                ? (deltaWidth > 0
                ? (0)
                : (durakSpaceWidth - newAllwidth) / 2 )
                : (durakSpaceWidth - allCardWidth + myCount * cardsLess6) / 2)

        $(cardsBlock).each(function (indx) {
            a = (myCount > 6
                ? marginLeftValue + indx * (newWidth - ((newAllwidth - durakSpaceWidth) / myCount)) * 0.9
                : marginLeftValue + indx * indexLess6);
            $(this).css({'left': a + 'px', 'top': 0})
        });

    }



