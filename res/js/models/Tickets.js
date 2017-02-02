function cPop(){
    Player.dates.captchaNotification = 0;
    Player.is.noCaptchaNotification = false;
}
function cTimer(){
    Player.dates.captcha = 0;
}

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
        "delayCaptchaPop": function(){
                Form.post.call(1,{
                            href: '/tickets/captcha/close',
                            data: {},
                            after: function(data) {
                                console.debug('delayCaptchaPop');
                                console.debug(data);
                                Tickets._popCaptchaHold = false;
                                $('.pop-box').remove();

                            }
                });
        },
        "neverCaptchaPop": function(){
                Form.post.call(1,{
                            href: '/tickets/captcha/closeForever',
                            data: {},
                            after: function(data) {
                                console.debug('neverCaptchaPop');
                                console.debug(data);
                                Tickets._popCaptchaHold = false;

                            }
                });
        },
        "captchaPop": function () {
            
            if (Player.is.unauthorized || Player.is.noCaptchaNotification) {
                console.debug('unauthorized || nomore popup');
                return;
            }
            if (!Tickets.captchaTime() ) {
                console.debug('not now! timeout...');
                return; 
            }
            if (Tickets.isAvailable(7) ) {
                console.debug('not now! ticket is ready...');
                return; 
            }
            
            console.debug('captchaPop... call!',popTime());
            
            if (!Tickets._popCaptchaHold && popTime()) {
            
                console.debug('captchaPop!!');
            
                // R.push('popup-money-captcha');
                R.push({
                    template:'popup-money-captcha',
                    after: function(e){
                        Tickets._popCaptchaHold = true;
                    }});
            }

            function popTime(){
                var timeout = +Config.captchaNotificationTime || 18000,
                lastUse = +Player.dates.captchaNotification || 0;

                return ( new Date( (lastUse + timeout) * 1000 ) < new Date() ); 
            }
        },
        "captchaTime": function () {
            
            if(!Player.dates){
                return true;
            }
            var timeout = +Config.captchaTime || 86400,
                lastUse = +Player.dates.captcha || 0;
            // console.debug(new Date((+Player.dates.captcha + (24*3600))* 1000) >  new Date() );
            return ( new Date( (lastUse + timeout) * 1000 ) < new Date() );
        },
        "getSevenTimer": function(){
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
            var f = form;
            var code = $(f).find('#moneycaptcha_code').val();

            if(!code){ 
                return; 
            }
            Form.post.call(f,{
                            href: f.action,
                            data: {'moneycaptcha_code':code},
                            after: function(data) {

                                if ( document.querySelector('#moca') ){
                                    document.querySelector('#moca').style.height = "";
                                }

                                // gotoSevenTicket
                                if (f.className.indexOf("_pop") !== -1) {
                                    $('.pop-box').remove();
                                    // $('#popup-money-captcha .close-pop-box').click();
                                    Tickets.selectedTab = 7; Tickets.update(); R.push('lottery');
                                }
                                console.debug(data);

                                }
                                });
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