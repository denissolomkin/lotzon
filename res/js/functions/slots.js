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
    spinURL: "/res/json/games/spin",
    curBet: 1,
    soundEnabled: !0,
    sounds: {},
    init: function () {

        $("#betSpinUp").click(function () {
            slotMachine.change_bet(1)
        }), $("#betSpinDown").click(function () {
            slotMachine.change_bet(-1)
        }), $("#spinButton").click(function () {
            slotMachine.spin()
        }), $("#soundOffButton").click(function () {
            slotMachine.toggle_sound()
        }), slotMachine.soundEnabled && (soundManager.url = "/res/js/libs/", soundManager.onload = function () {
            slotMachine.sounds.payout = soundManager.createSound({
                id: "payout",
                url: "/res/audio/games/payout.mp3"
            }), slotMachine.sounds.fastpayout = soundManager.createSound({
                id: "fastpayout",
                url: "/res/audio/games/fastpayout.mp3"
            }), slotMachine.sounds.spinning = soundManager.createSound({
                id: "spinning",
                url: "/res/audio/games/spinning.mp3"
            })
        })
//        , $('main, .content-top').addClass('fullscreen_mobile'); //fix - fullscreen in mobile
    },
    change_bet: function (n) {
        slotMachine.curBet += n, slotMachine.curBet = Math.min(Math.max(1, slotMachine.curBet), maxBet), slotMachine.show_won_state(!1), $("#bet").html(slotMachine.curBet), $("#prizes_list .tdPayout").each(function () {
            var n = $(this);
            n.html((n.attr("data-payoutPrefix") || "") + parseInt(n.attr("data-basePayout"), 10) * slotMachine.curBet + (n.attr("data-payoutSuffix") || ""))
        })
    },
    toggle_sound: function () {
        $("#soundOffButton").hasClass("off") ? soundManager.unmute() : soundManager.mute(), $("#soundOffButton").toggleClass("off")
    },
    spin: function () {
        var n = parseInt($("#credits").html(), 10);
        if ($("#spinButton").hasClass("disabled"))
            return !1;
        slotMachine.show_won_state(!1), $("#spinButton").addClass("disabled"), $("#credits").html(n - slotMachine.curBet), slotMachine._start_reel_spin(1, 0), slotMachine._start_reel_spin(2, slotMachine.secondReelStopTime), slotMachine._start_reel_spin(3, slotMachine.secondReelStopTime + slotMachine.thirdReelStopTime);
        try {
            slotMachine.sounds.spinning.play()
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
        }, i = !1, s = null;
        window.setTimeout(function () {
            i = !0, null != s && t()
        }, slotMachine.firstReelStopTime), $.ajax({
            url: slotMachine.spinURL,
            type: "POST",
            data: {bet: slotMachine.curBet, windowID: windowID, machine_name: slotMachine.machine_name},
            dataType: "json",
            timeout: 1e4,
            success: function (n) {

                n = n.res.games.spin;

                if ($i > n.length)
                    $i = 0;
                $i++;
                n = n[$i];

                console.log($i);

                return n.success ? (s = n, void(1 == i && t())) : (slotMachine.abort_spin_abruptly(), "loggedOut" == n.error ? $("#loggedOutMessage").show() : alert(n.error), !1)
            },
            error: function () {
                slotMachine.abort_spin_abruptly(), $("#failedRequestMessage").show()
            }
        })
    },
    show_won_state: function (n, e, t) {
        n ? ($("#PageContainer, #SlotsOuterContainer").addClass(t ? t : "won"), $("#trPrize_" + e).addClass("won")) : ($(".trPrize").removeClass("won"), $("#PageContainer, #SlotsOuterContainer").removeClass(), $("#lastWin").html(""))
    },
    end_spin: function (n) {
        null != n.prize ? (slotMachine.show_won_state(!0, n.prize.id, n.prize.winType), slotMachine._increment_payout_counter(n)) : slotMachine._end_spin_after_payout(n)
    },
    _format_winnings_number: function (n) {
        return n == Math.floor(n) ? n : n.toFixed(2)
    },
    _end_spin_after_payout: function (n) {
        "undefined" != typeof n.credits && $("#credits").html(n.credits), "undefined" != typeof n.dayWinnings && $("#dayWinnings").html(slotMachine.winningsFormatPrefix + slotMachine._format_winnings_number(n.dayWinnings)), "undefined" != typeof n.lifetimeWinnings && $("#lifetimeWinnings").html(slotMachine.winningsFormatPrefix + slotMachine._format_winnings_number(n.lifetimeWinnings)), "undefined" != typeof n.lastWin && $("#lastWin").html(n.lastWin);
        var e = parseInt($("#credits").html(), 10);
        e > 0 && $("#spinButton").removeClass("disabled")
    },
    _increment_payout_counter: function (n) {
        var e = {
            credits: n.credits - n.prize.payoutCredits,
            dayWinnings: n.dayWinnings - n.prize.payoutWinnings,
            lifetimeWinnings: n.lifetimeWinnings - n.prize.payoutWinnings
        }, t = Math.max(n.credits - e.credits, n.dayWinnings - e.dayWinnings), i = t > 80 ? "fastpayout" : "payout", s = t > 80 ? 50 : 200;
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
                e[s] < n[s] && (e[s] += 1, e[s] = Math.min(e[s], n[s]), $("#" + s).html("credits" != s ? slotMachine.winningsFormatPrefix + slotMachine._format_winnings_number(e[s]) : e[s]), t = !0)
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
slotMachine.init();