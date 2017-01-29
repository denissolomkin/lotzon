(function () {

    Tickets = {

        "init": function(init){

            if('timeToLottery' in init && 'timeToLottery' in this) {
                Slider.countdown(init.timeToLottery);
                Tickets.countdown(init.timeToLottery);
            }

            if(init.hasOwnProperty('lastLotteryId') && this.lastLotteryId != init.lastLotteryId)
                delete this.randomTicket;

            D.log('Tickets.init', 'func');
            Object.deepExtend(this, init);

            if(!this.hasOwnProperty('randomTicket'))
                this.randomTicket = this.getUnfilled();

            if('filledTickets' in init) {
                Ticket.render();
            }

        },
        "getSevenTimer": function(){
            // console.debug('!!!',Player,Player.dates.captcha)
            if(Player.is && Player.is.unauthorized) {return;}
            if(!Player && !Player.dates.captcha) {return;}

            $('#lottery-ticket-item .steps .timer').countdown({
                // until: new Date(new Date((new Date).getTime()+50000)),
                until: new Date((+Player.dates.captcha + (24*3600))* 1000),
                compact: true, 
                format: 'H:M:S',
                onExpiry: function(){
                    Tickets.update();
                }
            });
        },
        "getSevenTicket": function(form){
            // alert('seven');
            // action="/tickets/captcha" method="post"
            var code = $(form).find('#moneycaptcha_code').val();
            if(!code){ return; }

            // console.debug('______>>', form.action, code);

            Form.post.call(form,{
                            href: form.action,
                            data: {'moneycaptcha_code':code},
                            after: function(data) {
                                document.querySelector('#moca').style.height = "";
                                console.debug(data);

                                }
                            });
            // Form.post(e);
            //up tickets
            // Tickets.update();
        },
        "getSevenSteps": function (){
            
            console.debug(this);
            var btn = $(this),
                goto = $(this).attr('data-goto');
            btn.closest('.step').addClass('hidden');
            $('.steps '+goto).removeClass('hidden');
            
            // if(goto == '#st3'){
            //     $('#lottery-ticket').addClass('fix-seven');
            // }else{
            //     $('#lottery-ticket').removeClass('fix-seven');
            // }

        },

        "isDone": function (ticketId) {
            ticketId = ticketId || this.selectedTab;
            return (this.filledTickets && this.filledTickets[ticketId] && typeof this.filledTickets[ticketId] === 'object' && this.filledTickets[ticketId].length && this.filledTickets[ticketId].length == this.requiredBalls);
        },

        "isComplete": function () {
            return false && this.countFilled() === this.totalTickets;
        },

        "isAvailable": function (ticketId) {
            ticketId = ticketId || this.selectedTab;
            return this.filledTickets[ticketId] !== false;
        },

        "isGold": function (numTicket) {
            return numTicket ? numTicket == this.totalTickets : this.selectedTab == this.totalTickets;
        },

        "countFilled": function () {

            var count = 0;
            for (i = 1; i <= this.totalTickets; i++)
                if(this.filledTickets[i] && typeof this.filledTickets[i] === 'object')
                    count++;
            return count;
        },

        "getUnfilled": function () {

            var unfilled = [];

            for (i = 1; i <= this.totalTickets; i++)
                if(this.filledTickets.hasOwnProperty(i) && this.filledTickets[i] !== false && !this.filledTickets[i])
                    unfilled.push(i);

            return unfilled.length && unfilled[Math.floor(Math.random()*unfilled.length)];
        },

        "renderTickets": function () {

            var tickets = [];
            for (var n = 0; n <= this.filledTickets.length; n++) {
                var ticket = {
                    index: n,
                    balls: []
                };

                for (var b = 0; b <= this.filledTickets[n].length; b++) {
                    ticket.balls.push(this.filledTickets[n][b]);
                }

                tickets.push(ticket);

            }

            return tickets;
        },

        "update": function () {
            //fix height
            // $('#lottery-ticket').removeClass('fix-seven');

            R.json('/lottery/tickets');
        },

        "renderBalls": function () {

            var balls = [];
            for (i = 1; i <= this.totalBalls; i++) {
                balls.push({
                    num: i,
                    select: this.filledTickets[this.selectedTab] && this.filledTickets[this.selectedTab].indexOf(i) !== -1
                });
            }

            return balls;
        },
        "upadteTabs": function(){
            var tabs = document.querySelectorAll('#lottery-ticket-tabs > li');
            if(!tabs) return;

            for (var i = tabs.length - 1; i >= 0; i--) {

                if(Tickets.filledTickets[i+1] !== false){
                    tabs[i].className = tabs[i].className.replace('unavailable','');
                }
            }
        }
        ,
        "renderTabs": function () {

            var tabs = [];
            for (i = 1; i <= this.totalTickets; i++) {
                tabs.push({
                    num: i,
                    done: this.isDone(i),
                    available: this.isAvailable(i)
                });
            }

            return tabs;
        },

        "renderCondition": function () {

            return this.filledTickets[this.selectedTab];

        },
        
        countdown: function (timeout) {

            D.log('Ticket.countdown', 'func');
            var timer = $("#ticketCountdownHolder span");
            if(timer.length) {
                $("#ticketCountdownHolder span")
                    .countdown('destroy')
                    .countdown({
                        until : (timeout || $.countdown.periodsToSeconds($('#countdownHolder').countdown('getTimes'))),
                        layout: '{hnn}<span>:</span>{mnn}<span>:</span>{snn}'
                    });
            }

        }


    };

})();