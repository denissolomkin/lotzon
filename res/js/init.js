$(function () {

    /* ========================================================= */
    //                        TAB AND CAT SWITCH
    /* ========================================================= */

    // call callbacks
    C.init();
    C.menu();

    // call render init
    R.init();

    $Tickets = {

        "selectedBalls": 6,
        "totalBalls": 49,
        "totalTickets": 8,

        "favorite": [],
        "balls": {
            "1": [1, 2, 3, 4, 5, 6],
            "2": [31, 22, 13, 44, 25, 9],
            "3": [3, 2, 14, 34, 15, 19],
            "4": [1, 2, 3, 4, 5, 6],
            "5": [31, 22, 13, 44, 25, 9],
            "6": [3, 2, 14, 34, 15, 19],
            "7": [31, 22, 13, 44, 25, 9],
        },

        "ballsHTML": function () {
            var html = '';
            for (i = 1; i <= this.totalBalls; i++) {

                html += "<li class='ball-number number-" + i + ($.inArray(i, this.balls[$($TicketTabs).filter('.active').data('ticket')]) == -1 ? '' : ' select') + "'>" + i + "</li>";
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
            return (this.balls && this.balls[$($TicketTabs).filter('.active').data('ticket')] && this.balls[$($TicketTabs).filter('.active').data('ticket')].length && this.balls[$($TicketTabs).filter('.active').data('ticket')].length == this.selectedBalls);
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

    };

});