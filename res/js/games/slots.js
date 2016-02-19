/* global soundManager */
var minBet = 1;
var maxBet = 10;
var numIconsPerReel = 6;
soundManager.url = "/res/js/plugins/";
var slotMachine = {
    stripHeight: 720,
    alignmentOffset: 86,
    firstReelStopTime: 667,
    secondReelStopTime: 575,
    thirdReelStopTime: 568,
    payoutStopTime: 700,
    reelSpeedDifference: 0,
    reelSpeed1Delta: 100,
    reelSpeed1Time: 0,
    reelSpeed2Delta: 100,
    positioningTime: 200,
    bounceHeight: 200,
    bounceTime: 1e3,
    winningsFormatPrefix: "",
    curBet: 1,
    currency: 'points',
    isActive: !1,
    useMoney: !1,
    soundEnabled: !0,
    step: {
        money: 0.1,
        points: 1
    },
    xFactor: 0.1,
    sounds: {},
    init: function () {

        $("#betSpinUp").click(function () {
            slotMachine.change_bet(1);
        }),
            $("#betSpinDown").click(function () {
                slotMachine.change_bet(-1);
            }),
            $("#soundOffButton").click(function () {
                slotMachine.toggle_sound();
            }),
            $("#Gold, #Scores").click(function () {
                slotMachine.change_currency(this);
            }),
        slotMachine.soundEnabled && (
            slotMachine.sounds.payout = soundManager.createSound({
                id: "payout",
                url: "/res/audio/games/payout.mp3"
            }),
            slotMachine.sounds.fastpayout = soundManager.createSound({
                id: "fastpayout",
                url: "/res/audio/games/fastpayout.mp3"
            }),
            slotMachine.sounds.spinning = soundManager.createSound({
                id: "spinning",
                url: "/res/audio/games/spinning.mp3"
            })
        ),
            $("#Scores").click();
    },
    /**
     * @param {object} that
     * @returns {undefined}
     */
    change_currency: function (that) {
        if (!slotMachine.isActive && that.id) {
            switch (that.id) {
                case "Gold" :
                    $("#slotsSelectorWrapper").addClass('gold');
                    $('#playerBalance').attr('class', 'holder-money');
                    slotMachine.useMoney = !0, slotMachine.currency = 'money';
                    break;
                case "Scores" :
                    $("#slotsSelectorWrapper").removeClass('gold');
                    $('#playerBalance').attr('class', 'holder-points');
                    slotMachine.useMoney = !1, slotMachine.currency = 'points';
                    break;
            }
            $("#Scores, #Gold").removeClass('active');
            $(that).addClass('active');
            Player.updateBalance();
            slotMachine.change_bet(0);
        }
    },
    /**
     * @description парсит числа на дробные и целые, когда игрок использует деньги или баллы
     * @param {type} n
     * @returns {Number}
     */
    add_val: function (n) {
        if (slotMachine.useMoney) {
            return (n * 1).toFixed(2);
        }
        return n * 1;
    },
    change_bet: function (n) {
        if (slotMachine.isActive)
            return !1;

        if (slotMachine.useMoney) {
            slotMachine.curBet += n * slotMachine.xFactor;
            slotMachine.curBet = parseFloat(Math.min(Math.max(0.1, slotMachine.curBet), maxBet * slotMachine.xFactor).toFixed(2)); // fix max bet
        } else {
            slotMachine.curBet = slotMachine.curBet > 0 && slotMachine.curBet < 1 ? slotMachine.curBet / slotMachine.xFactor : slotMachine.curBet;
            slotMachine.curBet += n;
            slotMachine.curBet = Math.min(Math.max(1, parseInt(slotMachine.curBet)), maxBet); // fix max bet
        }
        slotMachine.show_won_state(!1);
        // $("#bet").html(slotMachine.add_val(slotMachine.curBet));
        $("#bet").val(slotMachine.add_val(slotMachine.curBet));
        $("#prizes_list .tdPayout").each(function () {
            var n = $(this);
            n.html((n.attr("data-payoutPrefix") || "") + slotMachine.add_val(parseInt(n.attr("data-basePayout"), 10) * slotMachine.curBet) + (n.attr("data-payoutSuffix") || ""));
        });
    },
    toggle_sound: function () {
        $("#soundOffButton").hasClass("off") ? soundManager.unmute() : soundManager.mute(), $("#soundOffButton").toggleClass("off")
    },
    /**
     * @description machine start
     * @returns {Boolean}
     */
    spin: function (data) {
        if (slotMachine.isActive)
            return !1;

        slotMachine.isActive = !0;
        slotMachine.show_won_state(!1),
            $("#spinButton").attr("disabled", "disabled").addClass("disabled"),
            slotMachine._start_reel_spin(1, 0),
            slotMachine._start_reel_spin(2, slotMachine.secondReelStopTime),
            slotMachine._start_reel_spin(3, slotMachine.secondReelStopTime + slotMachine.thirdReelStopTime);

        try {
            slotMachine.sounds.spinning.play();
        } catch (e) {
        }

        var s = {
                reels: data.json.res.GameField,
                prizes: data.json.res.GamePrizes,
                win: data.json.res.Win
            },
            t = function () {
                var n = 0;
                window.setTimeout(function () {
                    slotMachine._stop_reel_spin(1, s.reels[0])
                }, n), n += slotMachine.secondReelStopTime, window.setTimeout(function () {
                    slotMachine._stop_reel_spin(2, s.reels[1])
                }, n), n += slotMachine.thirdReelStopTime, window.setTimeout(function () {
                    slotMachine._stop_reel_spin(3, s.reels[2])
                }, n), n += slotMachine.payoutStopTime, window.setTimeout(function () {
                    slotMachine.end_spin(s)
                }, n)
            },
            i = !1;

        window.setTimeout(function () {
            i = !0,
            null != s && t()
        }, slotMachine.firstReelStopTime)
    },

    /**
     * @description text
     * @param {type} n
     * @param {type} e
     * @param {type} t
     * @returns {undefined}
     */
    show_won_state: function (n, e) {
        n
            ? ($("#SlotsContainer").addClass("won"), $("#trPrize_" + e).addClass("won"))
            : ($(".trPrize.won").removeClass("won"), $("#PageContainer, #SlotsContainer").removeClass(), $("#lastWin").html(""))
    },

    end_spin: function (n) {
        n.win ? (slotMachine.show_won_state(!0, n.prizes.multi), slotMachine._increment_payout_counter(n)) : slotMachine._end_spin_after_payout(n)
    },

    _format_winnings_number: function (n) {
        return n == Math.floor(n) ? n : n.toFixed(2)
    },

    _end_spin_after_payout: function (n) {

        //"undefined" != typeof n.credits && $("#playerBalance").html(Player.fineNumbers(n.credits)),
        //"undefined" != typeof n.dayWinnings && $("#dayWinnings").html(slotMachine.winningsFormatPrefix + slotMachine._format_winnings_number(n.dayWinnings)),
        //"undefined" != typeof n.lifetimeWinnings && $("#lifetimeWinnings").html(slotMachine.winningsFormatPrefix + slotMachine._format_winnings_number(n.lifetimeWinnings)),
        n.win && $("#lastWin").html(slotMachine.add_val(n.win));
        Player.balance[slotMachine.currency] > 0 && $("#spinButton").attr("disabled", false).removeClass("disabled");
        slotMachine.isActive = !1;
    },

    _increment_payout_counter: function (n) {

        n.win =  parseFloat(n.win);
        var e = 0,
            step = slotMachine.step[slotMachine.currency],
            i = (n.win / step) > 80 ? "fastpayout" : "payout",
            s = (n.win / step) > 80 ? 50 : 200;

        try {
            slotMachine.sounds[i].play({
                onfinish: function () {
                    this.play()
                }
            })
        } catch (o) {console.log(o);}

        var a = window.setInterval(function () {
            var t = !1;
            if ((e < n.win && (e += step, $("#playerBalance").html(Player.fineNumbers(Player.balance[slotMachine.currency]+e)), t = !0)), !t) {
                window.clearInterval(a);
                var init = {
                    balance:{}
                };
                init.balance[slotMachine.currency] = Player.balance[slotMachine.currency] + n.win;
                Player.init(init);
                try {
                    slotMachine.sounds[i].stop()
                } catch (s) {
                    console.log(s);
                }
                slotMachine._end_spin_after_payout(n)
            }
        }, s)
    },

    abort_spin_abruptly: function () {
        slotMachine._stop_reel_spin(1, null), slotMachine._stop_reel_spin(2, null), slotMachine._stop_reel_spin(3, null);
        try {
            slotMachine.sounds.spinning.stop()
        } catch (n) {
        }
    },

    _start_reel_spin: function (n, e) {
        var t = Date.now(), i = $("#reel" + n);
        i.css({top: -(Math.random() * slotMachine.stripHeight * 2)});
        var s = parseInt(i.css("top"), 10), o = function () {
            i.css({top: s}), s += Date.now() < t + slotMachine.reelSpeed1Time + e ? slotMachine.reelSpeed1Delta : slotMachine.reelSpeed2Delta, s += n * slotMachine.reelSpeedDifference, s > 0 && (s = 2 * -slotMachine.stripHeight)
        }, a = window.setInterval(o, 20);
        i.data("spinTimer", a)
    },

    _stop_reel_spin: function (n, e) {
        var t = $("#reel" + n), i = t.data("spinTimer");
        if (window.clearInterval(i), t.data("spinTimer", null), null != e) {
            var s = slotMachine.stripHeight / window.numIconsPerReel, o = -slotMachine.stripHeight - (e - 1) * s + slotMachine.alignmentOffset;
            t.css({top: o - slotMachine.stripHeight}).animate({top: o + slotMachine.bounceHeight}, slotMachine.positioningTime, "linear", function () {
                t.animate({top: o}, slotMachine.bounceTime, "easeOutElastic")
            })
        }
    }
};
