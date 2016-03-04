(function() {

    Lottery = {

        data: null,
        tickets: null,

        init: function() {

            Carousel.initOwl();
            Ticket.render();
            Tickets.countdown();

        },

        update: function() {

            Tickets.update(); // отрисует новые билеты

        },

        view: function(data) {

            Lottery.data = data.json;

            var lotteryId = data.json.id,
                lotteryCombination = data.json.combination, //Cache.get('lottery-history-' + lotteryId);
                lotterySummary = Lottery.getSummary(); //parseInt(document.getElementById('lottery-history-view').getAttribute('data-lottery-id'));

            Lottery.animateSummary(lotterySummary);

            R.push({
                href: 'lottery-' + lotteryId + '-tickets',
                format: Lottery.extendTickets,
                arguments: [lotterySummary, lotteryCombination]
            });


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
                matchesBalls,
                ticketType;

            /* extend playerTickets */
            if (Object.size(ticketsArray))
                for (i = 1; i <= Tickets.totalTickets; i++) {

                    ticketType = Tickets.isGold(i)?'gold':'default';
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

                    if (prizesData[ticketType]
                        && prizesData[ticketType][matchesBalls]
                        && prizesData[ticketType][matchesBalls].currency
                        && !extendedTickets.win[prizesData[ticketType][matchesBalls].currency]) {

                        extendedTickets.win[prizesData[ticketType][matchesBalls].currency] = {
                            currency: prizesData[ticketType][matchesBalls].currency,
                            prize: 0
                        };
                    }

                    if (matchesBalls
                        && prizesData[ticketType]
                        && prizesData[ticketType][matchesBalls].prize
                        && prizesData[ticketType][matchesBalls].currency) {

                        extendedTicket.prize = parseFloat(prizesData[ticketType][matchesBalls].prize);
                        extendedTicket.currency = prizesData[ticketType][matchesBalls].currency;
                        extendedTickets.win[extendedTicket.currency].prize += extendedTicket.prize;

                    }

                    extendedTickets.tickets.push(extendedTicket)
                }

            console.error(extendedTickets);

            return extendedTickets;
        },

        getSummary: function() {

            this.data.statistics =
                this.data.statistics || Tickets.prizes;

            var lotterySummary = {
                    totalSum: {}
                },
                lotteryData = this.data;

            for (var type in lotteryData.statistics) {
                for (var i in lotteryData.statistics[type]) {

                    if (!lotteryData.statistics[type].hasOwnProperty(i) || lotteryData.statistics[type][i].matches === null)
                        continue;

                    i = parseInt(i);

                    var ballData = lotteryData.statistics[type][i],
                        sum = ballData.sum * ballData.matches;

                    if (!lotterySummary[type])
                        lotterySummary[type] = {};

                    lotterySummary[type][(ballData.balls || i)] = {
                        currency: ballData.currency,
                        prize: ballData.sum,
                        matches: ballData.matches,
                        sum: sum
                    };

                    if(ballData.currency) {
                        if (!lotterySummary.totalSum[ballData.currency])
                            lotterySummary.totalSum[ballData.currency] = 0;
                        lotterySummary.totalSum[ballData.currency] += sum;
                    }

                }
            }

            return lotterySummary;
        },

        animateSummary: function(lotterySummary) {
            console.error(lotterySummary);

            var $won = $('.ghd-game-inf .ghd-all-won'),
                $table = $('.ghd-game-inf table'),
                $inf = $('.ghd-game-inf .title');

            $inf.hide();
            $won.hide();
            $table.hide();

            setTimeout(function() {
                if ((document.querySelector('.ghd-game-inf') != undefined)
                    && Device.onScreen.call(document.querySelector('.ghd-game-inf'), 100)) {
                    summaryVisible();
                } else {
                    $(window).on('scroll', summaryVisible);
                    $table.hide();
                }
            }, 600);


            function summaryVisible() {

                if(!Object.size(lotterySummary.totalSum)){
                    return;
                }

                $inf.show();
                $table.fadeIn();

                for (var i in lotterySummary.default) {

                    if (!isNumeric(i))
                        continue;

                    $('tr.balls-matches-' + i + ' td:eq(0)', $table)
                        .delay(1000)
                        .next().html(lotterySummary.default[i].matches || 0).spincrement({'thousandSeparator': ' '})
                        .next().html(
                        '<span>' + parseFloat(lotterySummary.default[i].sum.toFixed(2)) + '</span> ' +
                        '<span>' + Player.getCurrency(lotterySummary.default[i].currency) + '</span>')
                        .find('span').first().spincrement({'thousandSeparator': ' '});
                }

                $won.delay(2000).fadeIn(1000);
                setTimeout(function() {
                    for (var currency in lotterySummary.totalSum) {
                        if(lotterySummary.totalSum.hasOwnProperty(currency))
                            $('span.' + currency, $won)
                                .html(parseFloat(lotterySummary.totalSum[currency].toFixed(2)))
                                .spincrement({'thousandSeparator': ' '});
                    }
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
                            console.error('prepareData: ', Lottery.data);

                            /* todo adding to lottery-history all & mine */
                            Cache.set(
                                '/lottery/history/' + Lottery.data.id,
                                Object.deepExtend({type:'mine'},json),
                                'session'
                            );

                            console.error(
                                '/lottery/history/' + Lottery.data.id,
                                Object.deepExtend({type:'mine'},json),
                                'session'
                            );

                            Lottery.prepareTickets(Lottery.data.id);
                            Lottery.update();
                            break;
                    }
                };

            R.json({
                href: href,
                format: format
            });
        },

        prepareTickets: function(id) {

            var href = 'lottery-' + id + '-tickets',
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
                    this.tickets, [
                        Lottery.getSummary(),
                        this.data.combination
                    ]
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

                        setTimeout(function () {

                            $(".goc_li-sh, .goc_li-sh2").css('display', 'none');

                            if ($("#lottery-process li.won_ball").length) {
                                $("#lottery-process").addClass('lottery-won');
                                $('#lottery-process .won').css('display', 'block');
                            }

                            $('#lottery-process .ghd-text, #lottery-process .container-center').css('display', 'block');

                        }, 2000);

                    clearInterval(intervalAnimation);
                }
                else {
                    intervalAnimation = setInterval(animation, count);
                }

            };
    

            var intervalAnimation = setInterval(animation, counter);
      
        }

    }


})();