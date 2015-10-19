$(function () {


    // TICKET ================================= //

    Tickets = $.extend(Tickets, {
        "ballsHTML": function () {
            var html = '';
            for (i = 1; i <= this.totalBalls; i++) {

                html += "<li class='ball-number number-" + i + ($.inArray(i, this.balls[$(I.TicketTabs).filter('.active').data('ticket')]) == -1 ? '' : ' select') + "'>" + i + "</li>";
            }
            return html;

        },

        "tabsHTML": function () {
            var html = '';
            for (i = 1; i <= this.totalTickets; i++) {
                html += "<li data-ticket='" + i + "' class='" + (this.balls && this.balls[i] ? 'done' : '') + "'><span>" + M.i18n('title-ticket') + " </span>#" + i + "</li>";
            }
            return html;
        },

        "isDone": function () {
            return (this.balls && this.balls[$(I.TicketTabs).filter('.active').data('ticket')] && this.balls[$(I.TicketTabs).filter('.active').data('ticket')].length && this.balls[$(I.TicketTabs).filter('.active').data('ticket')].length == this.selectedBalls);
        },

        "isComplete": function () {
            return (this.balls && Object.keys(this.balls).length == this.totalTickets);
        },

        "completeHTML": function () {

            var html = '';

            $.each(this.balls, function (index, ticket) {
                html += "<ul class='ticket-result'><li class='ticket-number-result'><span>БИЛЕТ</span> #" + index + "";
                $.each(ticket, function (number, ball) {
                    html += "<li class='ball-number-result'>" + ball + "</li>";
                });
                html += "</ul>";
            });

            return html;
        }
    });
});