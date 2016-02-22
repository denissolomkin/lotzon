(function() {

    Lottery = {

        data: null,
        tickets: null,
        summary: null,

        init: function() {

            Carousel.initOwl();
            Ticket.render();
            Tickets.countdown();
            // R.push('/lottery/history');
        },

        update: function() {

            Tickets.update(); // отрисует новые билеты
            Slider.update(); // отрисует новый слайдер и баланс

        },

        view: function(data) {

            var lotteryId = data.json.id; //parseInt(document.getElementById('lottery-history-view').getAttribute('data-lottery-id'));
            Lottery.data = data.json; //Cache.get('lottery-history-' + lotteryId);
            Lottery.summary = Lottery.getSummary();
            Lottery.animateSummary();

            R.push({
                href: 'lottery-history-' + lotteryId + '-tickets',
                format: Lottery.extendTickets,
                arguments: [Lottery.summary, Lottery.data.combination]
            })

        },

        extendTickets: function(ticketsArray, arguments) {

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

        getSummary: function() {

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
                    currency: ballData.currency,
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

        animateSummary: function() {

            var lotterySummary = this.summary,
                $won = $('.ghd-game-inf .ghd-all-won'),
                $table = $('.ghd-game-inf table');

            setTimeout(function() {
                if ((document.querySelector('.ghd-game-inf') != undefined)
                    && Device.onScreen.call(document.querySelector('.ghd-game-inf'), 300)) {
                    summaryVisible();
                } else {
                    $(window).on('scroll', summaryVisible);
                }
            }, 600);

            $won.hide();

            function summaryVisible() {

                for (var i in lotterySummary) {

                    if (!isNumeric(i))
                        continue;

                    $('tr.balls-matches-' + i + ' td:eq(0)', $table)
                        .delay(1000)
                        .next().html(lotterySummary[i].matches || 0).spincrement({'thousandSeparator': ' '})
                        .next().html('<span>' + Player.getCurrency(lotterySummary[i].currency, lotterySummary[i].sum) + '</span> ' +
                            '<span>' + Player.getCurrency(lotterySummary[i].currency) + '</span>')
                        .find('span').first().spincrement({'thousandSeparator': ' '});
                }

                $won.delay(2000).fadeIn(1000);
                setTimeout(function() {
                    for (var currency in lotterySummary.totalSum)
                        $('span.' + currency, $won)
                            .html(Player.getCurrency(currency, lotterySummary.totalSum[currency]))
                            .spincrement({'thousandSeparator': ' '})
                }, 3000);
               $(window).off('scroll', summaryVisible); 
            
            }
        },

        prepareData: function(id) {

            var href = id ? '/lottery/history/' + id : '/lastResult',
                lastLotteryId = parseInt(Tickets.lastLotteryId),
                format = function(json) {

                    Lottery.data = json;

                    switch (true) {
                        case Lottery.data.id == lastLotteryId:
                        default:
                            setTimeout(function() {
                                Lottery.prepareData(id)
                            }, 3000);
                            break;
                        case Lottery.data.id > lastLotteryId + 1:
                            Lottery.update();
                            break;
                        case Lottery.data.id == lastLotteryId + 1:
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

        prepareTickets: function(id) {

            var href = 'lottery-history-' + id + '-tickets',
                json = ((id == parseInt(Tickets.lastLotteryId) + 1) ? {
                    key: href,
                    cache: "session",
                    res: Tickets.filledTickets
                } : null),
                format = function(json) {
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

        renderAnimation: function() {

            var json = Object.deepExtend({},this.data),
                arrRandom = [],
                renderBalls = {
                    'combination': []
                },
                tickets = Lottery.extendTickets(
                    this.tickets, [Lottery.getSummary(this.data), this.data.combination]
                ),
                combination = this.data.combination.slice();

            Object.deepExtend(json, tickets);

            for (var i = 1; i <= Tickets.totalBalls; i++) {
                arrRandom.push(i);
            }

            while (combination.length) {
                var ball = combination.shift();
                arrRandom.shuffle().splice(arrRandom.indexOf(ball), 1);
                renderBalls.combination.push([ball].concat(arrRandom));
            }

            console.log(renderBalls.combination,'renderBalls.combination');

            Object.deepExtend(json, renderBalls);

            console.log('renderAnimation: ', json);

            R.push({
                json: json,
                href: 'lottery-animation-process',
                after: Lottery.runAnimation
            })
        },

        runAnimation: function() {
            var randomInterval,
                readyBalls = [],
                counter = 400,
                count = counter / 2;
                
            if (readyBalls.length === Tickets.requiredBalls) {
                $("#lottery-process").addClass('lottery-won');
                D.log('Lottery.runAnimation: clearInterval', 'func');
                window.clearInterval(intervalAnimation);
            }

            animation = function() {
                count = counter / 2;
                clearInterval(intervalAnimation);

                var ball = $('.g-oc_li.unfilled:first .goc_li-nb').not('.random-ball').last();
                var balls = $('.g-oc_li.unfilled:first .goc_li-nb').not('.random-ball');
                if (balls.length < 10) {
                    counter += 50;
                    if (balls.length < 2) {
                        count = counter;
                    }
                }


                ball.addClass('random-ball');
                ball.css({
                    'transition': 'all ' + counter + 'ms cubic-bezier(0.3, 0.2, 0.1, 0.1)'
                });

                $.each($('.g-oc_li.unfilled'), function (index, li) {
                    readyBalls.push(index);

                    var ball = $('.goc_li-nb', li).not('.random-ball');
                    if (ball.length) {
                    } else {
                        $(this).removeClass('unfilled')
                        counter = 400;
                        ball = parseInt($('.goc_li-nb', li).first().text());
                        window.setTimeout(function () {
                            $("#lottery-process").find('li [data-num="' + ball + '"]').addClass('won_ball')
                        }, 1000);
                    }

                });

                if (!$('.g-oc_li.unfilled').length) {

                    if ($("#lottery-process li.won_ball").length) {

                        setTimeout(function () {

                            $("#lottery-process").addClass('lottery-won');

                            $('#lottery-process .ghd-won, #lottery-process .won').css({
                                'display': 'block'
                            });

                            $(".goc_li-sh, .goc_li-sh2").css({'display': 'none'});

                        }, 2000)

                    }

                    clearInterval(intervalAnimation);
                }
                else {
                    intervalAnimation = setInterval(animation, count);
                }

            }
    

            var intervalAnimation = setInterval(animation, counter);
      
        }

    }


})();