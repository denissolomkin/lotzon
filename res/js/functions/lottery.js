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
                    win: {},
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

            console.log(extendedTickets);
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
                box: '.ghd-tickets'
            })

        },

        prepareData: function (id) {

            var href = id ? '/lottery/history/' + id : '/lastLottery',
                format = function (json) {

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
                            console.log('prepareData: ', Lottery.data);
                            Lottery.prepareTickets(Lottery.data.id);
                            break;
                    }
                };

            R.json({
                href: href,
                format: format
            });
        },

        prepareTickets: function (id) {

            var href = 'lottery-tickets-' + id,
                json = ((id == Tickets.lastLotteryId + 1) ? {
                    key: href,
                    cache: "session",
                    res: Tickets.filledTickets
                } : null),
                format = function (json) {
                    Lottery.tickets = json;
                    console.log('prepareTickets: ', Lottery.tickets);
                    Lottery.renderAnimation();
                };

            R.json({
                href: href,
                json: json,
                format: format
            });


        },

        renderAnimation: function () {

            var json = this.data,
                arrRandom = [],
                renderBalls = {'combination': []},
                tickets = Lottery.extendTickets(
                    this.tickets, [Lottery.getSummary(this.data), this.data.combination]
                ),
                combination = this.data.combination;

            Object.deepExtend(json,tickets);

            for (var i = 1; i <= Tickets.totalBalls; i++)
                if (combination.indexOf(i) == -1)
                    arrRandom.push(i);

            while (combination.length) {
                var balls = [combination.shift()];
                for (var y = 0; y < Tickets.requiredBalls - combination.length; y++) {
                    arrRandom.shuffle();
                    for (var i = 0; i < 10; i++) {
                        balls.push(arrRandom[i]);
                    }
                }
                renderBalls.combination.push(balls);
            }

            Object.deepExtend(json,renderBalls);

            console.log('renderAnimation: ', json);

            R.push({
                box: '.container',
                json: json,
                template: 'lottery-animation-process',
                after: Lottery.runAnimation
            })
        },

        runAnimation: function () {

            var randomInterval,
                readyBalls = [];

            randomInterval = window.setInterval(function () {

                if (readyBalls.length === Tickets.requiredBalls) {

                    D.log('Lottery.runAnimation: clearInterval','func');

                    window.clearInterval(randomInterval);

                    if ($("#lottery-process").find('li.won').length) {
                        $('.ghd-won').css({
                            'display': 'block'
                        });
                    } else {
                        $('.ghd-won').css({
                            'display': 'block'
                        });
                    }
                }

                $.each($('.g-oc_li'), function (index, li) {

                    if (readyBalls.indexOf(index) !== -1)
                        return true;

                    var ball = $('.goc_li-nb', li).not('.random-ball').last();

                    if (ball.length) {
                        ball.addClass('random-ball');
                    } else {
                        readyBalls.push(index);
                        ball = parseInt($('.goc_li-nb', li).first().text());

                        window.setTimeout(function() {
                            $("#lottery-process").find('li[data-num="' + ball + '"]').addClass('won')
                        }, 1000);
                    }

                })

            }, 300);

        }
    }


})();