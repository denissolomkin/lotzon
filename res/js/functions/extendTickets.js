$(function () {


    // TICKET ================================= //

    $.extend(Tickets, {

        "isDone": function () {
            return (this.balls && this.balls[$(I.TicketTabs).filter('.active').data('ticket')] && this.balls[$(I.TicketTabs).filter('.active').data('ticket')].length && this.balls[$(I.TicketTabs).filter('.active').data('ticket')].length == this.selectedBalls);
        },

        "isComplete": function () {
            return (this.balls && Object.keys(this.balls).length == this.totalTickets);
        },

        "renderTickets": function () {

            var tickets = [];
            $.each(this.balls, function (index, balls) {
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

        "renderBalls": function () {

            var balls = [];
            for (i = 1; i <= this.totalBalls; i++) {
                balls.push({
                    num: i,
                    select: $.inArray(i, this.balls[$(I.TicketTabs + '.active').data('ticket')]) !== -1
                });
            }

            return balls;
        },

        "renderTabs": function () {

            var tabs = [];
            for (i = 1; i <= this.totalTickets; i++) {
                tabs.push({
                    num: i,
                    done: this.balls && this.balls[i] ? true : false
                });
            }

            return tabs;
        }
    });
});