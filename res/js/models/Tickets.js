(function () {

    Tickets = {

        "init": function(init){

            if('timeToLottery' in init && 'timeToLottery' in this) {
                Slider.countdown(init.timeToLottery);
                Tickets.countdown(init.timeToLottery);
            }

            if('filledTickets' in init && 'filledTickets' in this) {
                Ticket.render();
            }

            D.log('Tickets.init', 'func');
            Object.deepExtend(this, init);

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