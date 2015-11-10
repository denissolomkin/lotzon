(function () {

    Tickets = {

        "init": function(init){

            D.log('Tickets.init', 'func');
            Object.deepExtend(this, init);

        },

        "isDone": function (ticketId) {
            ticketId = ticketId || this.selectedTab;
            return (this.filledTickets && this.filledTickets[ticketId] && typeof this.filledTickets[ticketId] === 'object' && this.filledTickets[ticketId].length && this.filledTickets[ticketId].length == this.requiredBalls);
        },

        "isComplete": function () {
            return this.countFilled() === this.totalTickets;
        },

        "isAvailable": function (ticketId) {
            ticketId = ticketId || this.selectedTab;
            return this.filledTickets[ticketId] !== false;
        },

        "isGold": function () {
            return this.selectedTab == 8;    
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
            $.each(this.filledTickets, function (index, balls) {
                var ticket = {
                    index: index,
                    balls: []
                };
                $.each(balls, function (number, ball) {
                    ticket.balls.push(ball);
                });
                tickets.push(ticket);
            });

            return tickets;
        },



        "update": function () {

            var url = '/lottery/tickets';
            
            $.getJSON(url, function(response) {

                if (response.res.id == Tickets.lastLotteryId) {

                    setTimeout(function() {
                        Tickets.update()
                    }, 3000)

                } else {
                    
                    $.extend(Tickets, data.res);
                    Ticket.switch();
                }

            });
        },

        "renderBalls": function () {

            var balls = [];
            for (i = 1; i <= this.totalBalls; i++) {
                balls.push({
                    num: i,
                    select: $.inArray(i, this.filledTickets[this.selectedTab]) !== -1
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
        
        countdown: function () {

            D.log('Ticket.countdown', 'func');
            $("#ticketCountdownHolder span").countdown({
                until: ($.countdown.periodsToSeconds($('#countdownHolder').countdown('getTimes'))),
                layout: '{hnn}<span>:</span>{mnn}<span>:</span>{snn}'
            });

        },


    };

})();