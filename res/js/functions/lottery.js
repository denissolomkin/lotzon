(function () {

    Lottery = {

        data: null,
        tickets: null,
        summary: null,

        init: function () {

            Carousel.initOwl();
            Ticket.render();
        },

        update: function () {

            Tickets.update(); // отрисует новые билеты
            Slider.update(); // отрисует новый слайдер и баланс

        },

        extendTickets: function (ticketsArray, arguments) {

            var prizesData = arguments[0],
                lotteryCombination = arguments[1];

            if (typeof ticketsArray !== 'object')
                return false;

            var extendedTickets = {
                    win: [],
                    tickets: []
                },
                extendedTicket,
                matchesBalls;

            /* extend playerTickets */
            if (Object.size(ticketsArray))
                for (i = 1; i <= Tickets.totalTickets; i++) {

                    i = parseInt(i);

                    matchesBalls = 0;
                    extendedTicket = {
                        num: i,
                        combination: [],
                        prize: null,
                        currency: ''
                    };

                    /* extend playerTicket */
                    for (var b in ticketsArray[i]) {

                        if (!ticketsArray[i].hasOwnProperty(b))
                            continue;

                        extendedBall = {
                            ball: ticketsArray[i][b],
                            win: false
                        };

                        if (lotteryCombination.indexOf(ticketsArray[i][b]) > -1) {
                            matchesBalls += 1;
                            extendedBall.win = true;
                        }

                        extendedTicket.combination.push(extendedBall);
                    }

                    if (matchesBalls) {
                        extendedTicket.prize = prizesData[matchesBalls].prize;
                        extendedTicket.currency = prizesData[matchesBalls].currency;
                        if (!extendedTickets.win[prizesData[matchesBalls].currency]) {
                            extendedTickets.win[prizesData[matchesBalls].currency] = {
                                currency: extendedTicket.currency,
                                prize: extendedTicket.prize
                            };
                        } else
                            extendedTickets.win[extendedTicket.currency].prize += extendedTicket.prize;
                    }

                    extendedTickets.tickets.push(extendedTicket)
                }

            return extendedTickets;
        },

        getSummary: function () {

            var lotterySummary = {
                    totalSum: []
                },
                lotteryData = this.data;

            for (var i in lotteryData.statistics) {
                if (!lotteryData.statistics.hasOwnProperty(i))
                    continue;

                i = parseInt(i);

                var ballData = lotteryData.statistics[i],
                    sum = ballData.prize * ballData.matches;

                lotterySummary[ballData.balls] = {
                    currency: Player.getCurrency(ballData.currency),
                    prize: ballData.prize,
                    matches: ballData.matches,
                    sum: sum
                };

                if (!lotterySummary.totalSum[ballData.currency])
                    lotterySummary.totalSum[ballData.currency] = 0;
                lotterySummary.totalSum[ballData.currency] += sum;

            }

            return lotterySummary;
        },

        animateSummary: function () {

            var lotterySummary = this.summary,
                $won = $('.ghd-game-inf .ghd-all-won'),
                $table = $('.ghd-game-inf table');

            for (var i in lotterySummary) {

                if (!isNumeric(i))
                    continue;

                $('tr.balls-matches-' + i + ' td:eq(1)', $table).append(lotterySummary[i].currency)
                    .delay(1000)
                    .next().html(lotterySummary[i].matches).spincrement()
                    .next().html('<span>' + lotterySummary[i].sum + '</span> <span>' + lotterySummary[i].currency + '</span>')
                    .find('span').first().spincrement();
            }

            $won.hide().delay(2000).fadeIn(1000);
            setTimeout(function () {
                for (var currency in lotterySummary.totalSum)
                    $('span.' + currency, $won).html(lotterySummary.totalSum[currency]).spincrement()
            }, 3000);

        },

        view: function () {

            var lotteryId = parseInt($('.game_history_detail').data('lotteryid'));

            Lottery.data = Cache.get('lottery-history-' + lotteryId);
            Lottery.summary = Lottery.getSummary();
            Lottery.animateSummary();

            R.push({
                template: 'lottery-history-tickets',
                href: 'lottery-tickets-' + lotteryId,
                format: Lottery.extendTickets,
                arguments: [Lottery.summary, Lottery.data.combination],
                box: '.ghd-tickets',
                url: false
            })

        },

        prepareData: function (id) {

            var href = id ? '/lottery/history/' + id : '/lastLottery',
                format = function (json) {

                    console.log(json);

                    Lottery.data = json;
                    switch (true) {
                        case Lottery.data.id == Tickets.lastLotteryId:
                        default:
                            setTimeout(function () {
                                Lottery.prepareData(id)
                            }, 3000);
                            break;
                        case Lottery.data.id > Tickets.lastLotteryId + 1:
                            Lottery.update();
                            break;
                        case Lottery.data.id == Tickets.lastLotteryId + 1:
                            Lottery.prepareTickets(Lottery.data.id);
                            break;
                    }
                    console.log('prepareData: ', Lottery.data);
                };

            R.json({
                href: href,
                format: format
            });


        },

        prepareTickets: function (id) {

            var href = 'lottery-tickets-' + id,
                json = (id == Tickets.lastLotteryId + 1)
                    ? {key: href, cache: "session", res: Tickets.filledTickets}
                    : null,
                format = function (json) {
                    Lottery.tickets = json;
                    Lottery.renderAnimation();
                    console.log('prepareTickets: ', Lottery.tickets);
                };

            R.json({
                href: href,
                json: json,
                format: format
            });


        },

        renderAnimation: function () {

            var json = $.extend(
                {},
                this.data,
                Lottery.extendTickets(
                    this.tickets,
                    [Lottery.getSummary(this.data), this.data.combination]
                )
            );

            console.log('renderAnimation: ', json);

            R.push({
                box: '.container',
                json: json,
                template: 'lottery-animation-process',
                after: Lottery.runAnimation,
                url: false
            })
        },

        runAnimation: function() {

            var combination = Lottery.data.combination,
                ballInterval,
                arrRandom = [],
                timer = {
                    fake: 200,
                    ball: 1000,
                    tries: 5
                };

            for(var i = 1; i <= Tickets.totalBalls; i++) {
                arrRandom.push(i);
            }
            var li;
            var spn;
            fakeAnimation = function() {


                var ball = arrRandom[Math.ceil(Math.random() * (arrRandom.length)-1)]


                spn = $("#lottery-process .g-oc_span.unfilled:first");
                spn.text(ball);
                li = spn.parents('.g-oc_li');
                li.find('.goc_li-nb').addClass('goc-nb-act');


            }

            ballAnimation = function() {


                var ball = combination.shift();


                arrRandom.splice(arrRandom.indexOf(ball),1);
                spn = $("#lottery-process .g-oc_span.unfilled:first");
                spn.text(ball);
                var li = spn.parents('.g-oc_li');

                spn.removeClass('unfilled');

                window.setTimeout(function() {
                    $("#lottery-process").find('li[data-num="' + ball + '"]').addClass('won')
                }, 1000);

                if (!combination.length) {
                    window.clearInterval(fakeInterval);
                    window.clearInterval(ballInterval);
                    window.setTimeout(function() {
                        if ($("#lottery-process").find('li.won').length) {
                            // showWinPopup(data);
                        } else {
                            // showFailPopup(data);
                        }
                    }, 2000);
                }
            }


            window.setTimeout(function() {
                // ballAnimation();
                fakeInterval = window.setInterval(fakeAnimation, timer.fake);
                ballInterval = window.setInterval(ballAnimation, timer.fake * timer.tries + timer.ball);
            }, 1000);
            console.log('timer.fake * timer.tries + timer.ball', timer.fake * timer.tries + timer.ball)

        }


    }

})();