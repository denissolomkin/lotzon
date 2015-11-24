/* global soundManager */
var machineName = 'slotmachine1';
var minBet = 1;
var maxBet = 10;
var numIconsPerReel = 6;
var windowID = 11074;

//var scripts = document.getElementsByTagName("script");
//alert('slotMachine - loaded!! '+ toString(scripts[scripts.length-1]) + " +++ " +scripts.length);

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
    isActive: !1,
    useMoney: !1,
    soundEnabled: !0,
    xFactor: 0.1,
    sounds: {},
    init: function () {
//        alert("slotMachine.init");
        slotMachine.change_bet(0);
        try {
            Player.updateBalance();
        } catch (e) {
        }
//        Player.updateBalance(); // resset static data
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
    spin: function () {
        var n = parseFloat($("#credits").html().replace(',', '.').replace(' ', ''), 10);
        if (slotMachine.isActive)
            return !1;
        slotMachine.isActive = !0;
        slotMachine.show_won_state(!1),
                $("#spinButton").addClass("disabled"),
                $("#credits").html(Player.fineNumbers(n - slotMachine.curBet)),
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
        }, i = !1, s = null;
        window.setTimeout(function () {
            i = !0, null != s && t()
        }, slotMachine.firstReelStopTime), $.ajax({
            url: slotMachine.spinURL,
            type: "POST",
            data: {bet: slotMachine.curBet, useMoney: slotMachine.useMoney, windowID: windowID, machine_name: slotMachine.machine_name},
            dataType: "json",
            timeout: 1e4,
            success: function (n) {
                //>>>>fake results
                n = [
                    {"reels": ["2.0", "3.0", "3.0"],
                        "prize": null,
                        "success": true,
                        "credits": parseFloat($("#credits").html().replace(',', '.').replace(' ', '')),
                        "dayWinnings": 14,
                        "lifetimeWinnings": 62
                    },
                    {"reels": ["5.0", "1.0", "1.0"],
                        "prize":
                                {
                                    "id": "41", "payoutCredits": slotMachine.curBet, "payoutWinnings": slotMachine.curBet
                                },
                        "lastWin": slotMachine.curBet * 7,
                        "success": true,
                        "credits": parseFloat($("#credits").html().replace(',', '.').replace(' ', '')) + (slotMachine.curBet * 7),
                        "dayWinnings": 21,
                        "lifetimeWinnings": 69
                    }
                ];
                n = n[Math.floor(Math.random() * (1 - 0 + 1)) + 0];
                //fake results
//                n = n.res.games.spin;
//
//                if ($i > n.length)
//                    $i = 0;
//                $i++;
//                n = n[$i];
//
//                console.log($i);

                return n.success ? (s = n, void(1 == i && t())) : (slotMachine.abort_spin_abruptly(), "loggedOut" == n.error ? $("#loggedOutMessage").show() : alert(n.error), !1)
            },
            error: function () {
                slotMachine.abort_spin_abruptly(), $("#failedRequestMessage").show()
            }
        })
    },
    /**
     * @description text
     * @param {type} n
     * @param {type} e
     * @param {type} t
     * @returns {undefined}
     */
    show_won_state: function (n, e, t) {
        n ? ($("#PageContainer, #SlotsContainer").addClass(t ? t : "won"), $("#trPrize_" + e).addClass("won")) : ($(".trPrize").removeClass("won"), $("#PageContainer, #SlotsContainer").removeClass(), $("#lastWin").html(""))
    },
    end_spin: function (n) {
        null != n.prize ? (slotMachine.show_won_state(!0, n.prize.id, n.prize.winType), slotMachine._increment_payout_counter(n)) : slotMachine._end_spin_after_payout(n)
    },
    _format_winnings_number: function (n) {
        return n == Math.floor(n) ? n : n.toFixed(2)
    },
    _end_spin_after_payout: function (n) {
        "undefined" != typeof n.credits && $("#credits").html(Player.fineNumbers(n.credits)),
                "undefined" != typeof n.dayWinnings && $("#dayWinnings").html(slotMachine.winningsFormatPrefix + slotMachine._format_winnings_number(n.dayWinnings)),
                "undefined" != typeof n.lifetimeWinnings && $("#lifetimeWinnings").html(slotMachine.winningsFormatPrefix + slotMachine._format_winnings_number(n.lifetimeWinnings)),
                "undefined" != typeof n.lastWin && $("#lastWin").html(slotMachine.add_val(n.lastWin));
        var e = parseFloat($("#credits").html().replace(',', '.').replace(' ', ''), 10);
        e > 0 && $("#spinButton").removeClass("disabled");
        slotMachine.isActive = !1;


        //>>>>fake update credits
        if (slotMachine.useMoney) {
            Player.updateMoney(parseFloat($("#credits").html().replace(',', '.').replace(' ', ''), 10));
        } else {
            Player.updatePoints(parseFloat($("#credits").html().replace(',', '.').replace(' ', ''), 10));
        }
        //>>>> END fake update credits

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
                e[s] < n[s] && (e[s] += 1, e[s] = Math.min(e[s], n[s]), $("#" + s).html("credits" != s ? slotMachine.winningsFormatPrefix + slotMachine._format_winnings_number(e[s]) : Player.fineNumbers(e[s])), t = !0)
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
//slotMachine.init();
