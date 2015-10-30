(function() {

        Lottery = {

            data: null,
            tickets: null,

            getCurrency: function(currency) {

                switch (currency) {
                    case 'money':
                        currency = Player.currency.iso;
                        break;
                    case 'points':
                        currency = M.i18n('title-points');
                        break;
                    case 'lotzon':
                        currency = M.i18n('title-lotzon');
                        break;
                }

                return currency;
            },

            extendTickets: function(arguments) {

                ticketsArray = arguments[0];
                prizesData = arguments[1];
                lotteryCombination = arguments[2];

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

            getSummary: function(lotteryData) {

                var lotterySummary = {
                    totalSum: []
                };

                for (var i in lotteryData.statistics) {
                    if(!lotteryData.statistics.hasOwnProperty(i))
                        continue;

                    i = parseInt(i);

                    var ballData = lotteryData.statistics[i],
                        sum = ballData.prize * ballData.matches;

                    lotterySummary[ballData.balls] = {
                        currency: Lottery.getCurrency(ballData.currency),
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

            animateSummary: function(lotterySummary) {

                var $won = $('.ghd-game-inf .ghd-all-won'),
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
                setTimeout(function() {
                    for (var currency in lotterySummary.totalSum)
                        $('span.' + currency, $won).html(lotterySummary.totalSum[currency]).spincrement()
                }, 3000);

            },

            view: function() {

                var lotteryId = parseInt($('.game_history_detail').data('lotteryid')),
                    lotteryData = Cache.get('lottery-history-'+lotteryId),
                    lotterySummary = Lottery.getSummary(lotteryData);

                Lottery.animateSummary(lotterySummary);

                R.push({
                    template: 'lottery-history-tickets',
                    href: 'lottery-tickets-' + lotteryId,
                    format: Lottery.extendTickets,
                    arguments: [lotterySummary, lotteryData.combination],
                    box: '.ghd-tickets',
                    url: false
                })

            },

            init: function() {

                Carousel.initOwl();
                Ticket.render();
            },

            update: function() {

                Tickets.update(); // отрисует новые билеты
                Slider.update(); // отрисует новый слайдер и баланс

            },

            prepareData: function(id) {

                var lotteryData, url;

                if (id && lotteryData == Cache.get('lottery-history-' + id)) {

                    this.data = lotteryData;
                    Lottery.prepareTickets(id);

                } else {

                    url = id ? '/lottery/history/' + id : '/lastLottery';

                    $.getJSON(U.generate(url), function(response) {

                        Lottery.data = response.res;

                        if (response.res.id == Tickets.lastLotteryId) {

                            setTimeout(function() {
                                Lottery.prepareData(id)
                            }, 3000)

                        } else if (response.res.id > Tickets.lastLotteryId + 1) {
                            Lottery.update();
                        } else {
                            Lottery.prepareTickets(id);
                        }

                    });
                }

console.log('prepareData: ',this.data);

            },

            prepareTickets: function(id) {


                var playerTickets, 
                url;

                if (id) {

                    if (playerTickets = Cache.get('lottery-tickets-' + id)) {
                        this.tickets = playerTickets;
                        Lottery.renderAnimation();
                    } else {

                        $.getJSON(U.generate(url),'/lottery/tickets/' + id, function(response) {
                            Lottery.tickets = response.res;
                            Lottery.renderAnimation();
                        });
                    }

                } else {
                            this.tickets = Tickets.filledTickets;
                    Lottery.renderAnimation();

                }

console.log('prepareTickets: ',this.tickets);


            },

            renderAnimation: function() {

                var lottery = $.extend(
                    {}, 
                    this.data, 
                    Lottery.extendTickets([this.tickets, Lottery.getSummary(this.data), this.data.combination]));

console.log('renderAnimation: ',lottery);

                R.push({
                    template: 'lottery-animation-process',
                    json: lottery,
                    box: '.container',
                    url: false,
                    after: Lottery.runAnimation
                })
            },

            runAnimation: function() {

                var combination = Lottery.data.combination,
                    ballInterval,
                    ballAnimation = function(ball) {

                        var ball = combination.shift();
                        var spn = $("#lottery-process .g-oc_span.unfilled:first");

                        spn.text(ball);
                        var li = spn.parents('.g-oc_li');
                        li.find('.goc_li-nb').addClass('goc-nb-act');
                        spn.removeClass('unfilled');

                        window.setTimeout(function() {
                            $("#lottery-process").find('li[data-num="' + ball + '"]').addClass('won')
                        }, 1000);

                        if (!combination.length) {
                            window.clearInterval(ballInterval);
                            window.setTimeout(function() {
                                if ($("#lottery-process").find('li.won').length) {
                                    showWinPopup(data);
                                } else {
                                    showFailPopup(data);
                                }
                            }, 2000);
                        }
                    }

                window.setTimeout(function() {
                    ballAnimation();
                    ballInterval = window.setInterval(ballAnimation, 5000);
                }, 2000);

            },





        // showFailPopup: function(data) {

        //     $("#lottery-process").hide();
        //     $("#game-end").show();
        //     var ticketsHtml = '';
        //     for (var i = 0; i < 5; ++i) {
        //         var won = 0
        //         ticketsHtml += '<li class="yr-tt"><div class="yr-tt-tn">Билет #' + (i + 1) + '</div><ul data-ticket="' + i + '" class="yr-tt-tr">';

        //         if (data.tickets[i]) {
        //             $(data.tickets[i]).each(function(id, num) {
        //                 ticketsHtml += '<li class="yr-tt-tr_li" data-num="' + num + '">' + num + '</li>';
        //             });
        //         } else {
        //             ticketsHtml += "<li class='null'>Не заполнен</li>"
        //         }
        //         ticketsHtml += '</ul></li>';
        //     }
        //     $("#game-end").find('.yr-tb').html(ticketsHtml);
        //     var lotteryHtml = '';

        //     $(data.c).each(function(id, num) {
        //         lotteryHtml += '<li class="g-oc_li"><span class="g-oc_span">' + num + '</span></li>';
        //     });

        //     $("#game-end").find('.g-oc-b').html(lotteryHtml);

        //     return;

        //     $("#lottery-process").hide();
        //     $("#game-end").show();
        //     var ticketsHtml = '';
        //     for (var i = 1; i <= 5; ++i) {
        //         ticketsHtml += '<li class="yr-tt"><div class="yr-tt-tn">Билет #' + (i) + '</div><ul class="yr-tt-tr">';
        //         if (data.res.tickets[i]) {
        //             $(data.res.tickets[i]).each(function(id, num) {
        //                 ticketsHtml += '<li class="yr-tt-tr_li" data-num="' + num + '">' + num + '</li>';
        //             });
        //         } else {
        //             ticketsHtml += "<li class='null'>Не заполнен</li>"
        //         }
        //         ticketsHtml += '</ul></li>';
        //     }
        //     $("#game-end").find('.yr-tb').html(ticketsHtml);
        //     var lotteryHtml = '';

        //     $(data.res.lottery.combination).each(function(id, num) {
        //         lotteryHtml += '<li class="g-oc_li"><span class="g-oc_span">' + num + '</span></li>';
        //     });

        //     $("#game-end").find('.g-oc-b').html(lotteryHtml);
        // }

        // function showWinPopup(data) {
        //     $("#lottery-process").hide();
        //     $("#game-won").show();

        //     var nominals = [];
        //     $('.win-tbl .c-r .c-r_li').each(function(index) {
        //         nominals[index] = $(this).find('.tb-t').html();
        //     });
        //     nominals.reverse();

        //     var ticketsHtml = '';
        //     var wonMoney = 0;
        //     var wonPoints = 0;
        //     for (var i = 0; i < 5; ++i) {
        //         var won = 0
        //         ticketsHtml += '<li class="yr-tt"><div class="yr-tt-tn">Билет #' + (i + 1) + '</div><ul data-ticket="' + i + '" class="yr-tt-tr">';

        //         if (data.tickets[i]) {
        //             $(data.tickets[i]).each(function(id, num) {
        //                 ticketsHtml += '<li class="yr-tt-tr_li' + ($.inArray(parseInt(num), data.c) >= 0 ? ' won' : '') + '" data-num="' + num + '">' + num + '</li>';
        //             });
        //         } else {
        //             ticketsHtml += "<li class='null'>Не заполнен</li>"
        //         }
        //         ticketsHtml += '</ul>';

        //         var nominal = [];
        //         if (won = $(ticketsHtml).find('ul[data-ticket="' + i + '"] li.won').length) {
        //             ticketsHtml += '<div class="yr-tt-tc">' + nominals[won - 1] + '</div>';
        //             nominal = nominals[won - 1].split(" ");
        //             if (nominal[1] == getCurrency())
        //                 wonMoney += parseFloat(nominal[0]);
        //             else
        //                 wonPoints += parseInt(nominal[0]);
        //         }
        //         ticketsHtml += '</li>';
        //     }


        //     $("#game-won").find('.yr-tb').html(ticketsHtml);
        //     var lotteryHtml = '';

        //     $(data.c).each(function(id, num) {
        //         lotteryHtml += '<li class="g-oc_li"><span class="g-oc_span">' + num + '</span></li>';
        //     });

        //     $("#game-won").find('.g-oc-b').html(lotteryHtml);
        //     $("#game-won").find('.plPointHolder').text(wonPoints);
        //     $("#game-won").find('.plMoneyHolder').text((Math.round((wonMoney) * 100) / 100));

        //     return false;

        //     updateMoney(playerMoney + wonMoney);
        //     updatePoints(playerPoints + wonPoints);
        //     $("#lottery-process").hide();
        //     $("#game-won").show();
        //     var ticketsHtml = '';
        //     for (var i = 1; i <= 5; ++i) {
        //         ticketsHtml += '<li class="yr-tt"><div class="yr-tt-tn">Билет #' + (i) + '</div><ul class="yr-tt-tr">';
        //         if (data.res.tickets[i]) {
        //             $(data.res.tickets[i]).each(function(id, num) {
        //                 ticketsHtml += '<li class="yr-tt-tr_li" data-num="' + num + '">' + num + '</li>';
        //             });
        //         } else {
        //             ticketsHtml += "<li class='null'>Не заполнен</li>"
        //         }
        //         ticketsHtml += '</ul>';
        //         if (data.res.ticketWins[i] && data.res.ticketWins[i] != 0) {
        //             ticketsHtml += '<div class="yr-tt-tc">' + data.res.ticketWins[i] + '</div>'
        //         }
        //         ticketsHtml += '</li>';
        //     }
        //     $("#game-won").find('.yr-tb').html(ticketsHtml);
        //     var lotteryHtml = '';

        //     $(data.res.lottery.combination).each(function(id, num) {
        //         lotteryHtml += '<li class="g-oc_li"><span class="g-oc_span">' + num + '</span></li>';
        //         $("#game-won").find('li[data-num="' + num + '"]').addClass('won')
        //     });

        //     $("#game-won").find('.g-oc-b').html(lotteryHtml);
        //     $("#game-won").find('.player-points').text(data.res.player.points);
        //     $("#game-won").find('.player-money').text(data.res.player.money);
        // }


    }

})();