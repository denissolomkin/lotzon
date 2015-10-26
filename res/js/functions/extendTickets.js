$(function () {


    // TICKET ================================= //

    $.extend(Tickets, {

        "isDone": function () {
            return (this.balls && this.balls[this.selectedTab] && this.balls[this.selectedTab].length && this.balls[this.selectedTab].length == this.selectedBalls);
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
                    select: $.inArray(i, this.balls[this.selectedTab]) !== -1
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