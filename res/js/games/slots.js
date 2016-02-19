//путь к урлу
soundManager.url = "/res/js/plugins/";

/* global soundManager */
var minBet = 1;
var maxBet = 10;
var numIconsPerReel = 6;

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
    machine_name: "slotmachine1",
    spinURL: "/res/GET/games/spin",
    curBet: 1,
    isActive: !1,
    useMoney: !1,
    soundEnabled: !0,
    xFactor: 0.1,
    sounds: {},
    init: function () {

        $("#betSpinUp").click(function () {
            slotMachine.change_bet(1);
        }), $("#betSpinDown").click(function () {
            slotMachine.change_bet(-1);
        }), $("#spinButton").click(function () {
            slotMachine.spin();
        }), $("#soundOffButton").click(function () {
            slotMachine.toggle_sound();
        }), $("#Gold, #Scores").click(function () {
            slotMachine.change_currency(this);
        }), slotMachine.soundEnabled && (soundManager.url = "/res/js/plugins/", soundManager.onload = function () {
            slotMachine.sounds.payout = soundManager.createSound({
                id: "payout",
                url: "/res/audio/games/payout.mp3"
            }), slotMachine.sounds.fastpayout = soundManager.createSound({
                id: "fastpayout",
                url: "/res/audio/games/fastpayout.mp3"
            }), slotMachine.sounds.spinning = soundManager.createSound({
                id: "spinning",
                url: "/res/audio/games/spinning.mp3"
            });
        });
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
                    $('#credits').attr('class', 'holder-money');
                    slotMachine.useMoney = !0;
                    break;
                case "Scores" :
                    $("#slotsSelectorWrapper").removeClass('gold');
                    $('#credits').attr('class', 'holder-points');
                    slotMachine.useMoney = !1;
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
        $("#bet").html(slotMachine.add_val(slotMachine.curBet));
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
            $("#spinButton").addClass("disabled"),
            slotMachine._start_reel_spin(1, 0),
            slotMachine._start_reel_spin(2, slotMachine.secondReelStopTime),
            slotMachine._start_reel_spin(3, slotMachine.secondReelStopTime + slotMachine.thirdReelStopTime);

        try {
            slotMachine.sounds.spinning.play();
        } catch (e) {
        }

        var t = function () {
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
            i = !1,
            s = null;

        s = {
            reels: data.json.res.GameField,
            prizes: data.json.res.GamePrizes,
            win: data.json.res.Win
        };

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
            : ($(".trPrize").removeClass("won"), $("#PageContainer, #SlotsContainer").removeClass(), $("#lastWin").html(""))
    },
    end_spin: function (n) {
        n.win ? (slotMachine.show_won_state(!0, n.prizes.math), slotMachine._increment_payout_counter(n)) : slotMachine._end_spin_after_payout(n)
    },
    _format_winnings_number: function (n) {
        return n == Math.floor(n) ? n : n.toFixed(2)
    },
    _end_spin_after_payout: function (n) {

        "undefined" != typeof n.credits && $("#credits").html(Player.fineNumbers(n.credits)),
        "undefined" != typeof n.dayWinnings && $("#dayWinnings").html(slotMachine.winningsFormatPrefix + slotMachine._format_winnings_number(n.dayWinnings)),
        "undefined" != typeof n.lifetimeWinnings && $("#lifetimeWinnings").html(slotMachine.winningsFormatPrefix + slotMachine._format_winnings_number(n.lifetimeWinnings)),
        n.prizes && $("#lastWin").html(slotMachine.add_val(n.prizes));

        var e = parseFloat($("#credits").html().replace(',', '.').replace(' ', ''), 10);
        e > 0 && $("#spinButton").removeClass("disabled");
        slotMachine.isActive = !1;
    },
    _increment_payout_counter: function (n) {
        var e = {
                credits: n.credits - n.prize.payoutCredits,
                dayWinnings: n.dayWinnings - n.prize.payoutWinnings,
                lifetimeWinnings: n.lifetimeWinnings - n.prize.payoutWinnings
            },
            t = Math.max(n.credits - e.credits, n.dayWinnings - e.dayWinnings),
            i = t > 80 ? "fastpayout" : "payout",
            s = t > 80 ? 50 : 200;
        try {
            slotMachine.sounds[i].play({
                onfinish: function () {
                    this.play()
                }
            })
        } catch (o) {
        }
        var a = window.setInterval(function () {
            var t = !1;
            if ($.each(["credits", "dayWinnings", "lifetimeWinnings"], function (i, s) {
                    e[s] < n[s] && (e[s] += 1,
                        e[s] = Math.min(e[s], n[s]),
                        $("#" + s).html("credits" != s ? slotMachine.winningsFormatPrefix + slotMachine._format_winnings_number(e[s]) : Player.fineNumbers(e[s])),
                        t = !0)
                }), !t) {
                window.clearInterval(a);
                try {
                    slotMachine.sounds[i].stop()
                } catch (s) {
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
