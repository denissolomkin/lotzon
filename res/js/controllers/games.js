//$(function(){
//R.push({
//    template: 'badges-list',
//    json: [{
//            "key": "Moment",
//            "title": "title-moment-chance",
//            "text": "text-moment-will-be-available-for",
//            "button": "button-play",
//            "action": "/games/moment",
//            "timer": 60,
//            "timeout": "button"
//        },
//        {
//            "key": "Random",
//            "title": "title-random-chance",
//            "text": "text-random-will-be-available-in",
//            "button": "button-play",
//            "action": "/games/random",
//            "timer": 100,
//            "timeout": "close"
//        },
//        {
//            "key": "message",
//            "id": 10,
//            "title": "title-new-message",
//            "text": "Привет, как дела?",
//            "action": "/messages/123",
//            "timer": 3,
//            "timeout": "close"
//        }]
//});    
//})
//
//
//var fakeCounter = 0;
//var fakeRandomData = [{
//        "Uid": "568b9d3875b2b",
//        "Prize": {
//            "t": "item",
//            "v": "96",
//            "s": "5436a10e394a3.jpg",
//            "n": "\u0422\u0435\u0440\u043c\u043e\u043a\u0440\u0443\u0436\u043a\u0430 Stanley"
//        },
//        "Cell": "1x1",
//        "Moves": 3,
//        "comb": []
//    }, {
//        "Uid": "568b9d3875b2b",
//        "Prize": {
//            "t": "points",
//            "v": "250"
//        },
//        "Cell": "2x1",
//        "Moves": 2,
//        "comb": []
//    }, {
//        "Uid": "568b9d3875b2b",
//        "Prize": {
//            "t": "money",
//            "v": "25"
//        },
//        "Cell": "2x1",
//        "Moves": 1,
//        "comb": []
//    }, {
//        "Uid": "568b9d3875b2b",
//        "Prize": false,
//        "Cell": "3x1",
//        "Moves": 0,
//        "GameField": {
//            "1x1": {
//                "t": "item",
//                "v": "96",
//                "s": "5436a10e394a3.jpg",
//                "n": "\u0422\u0435\u0440\u043c\u043e\u043a\u0440\u0443\u0436\u043a\u0430 Stanley"
//            },
//            "2x1": {
//                "t": "points",
//                "v": "25"
//            },
//            "3x1": false
//        },
//        "GamePrizes": {
//            "POINT": 25,
//            "ITEM": " \u0422\u0435\u0440\u043c\u043e\u043a\u0440\u0443\u0436\u043a\u0430 Stanley"
//        },
//        "comb": [],
//        "player": {
//            "balance": {
//                "points": 1097,
//                "money": "2307.85"
//            }
//        }
//    },
//    {
//        "status": 0,
//        "message": "\u0418\u0433\u0440\u0430 \u043d\u0435 \u043d\u0430\u0439\u0434\u0435\u043d\u0430",
//        "res": []
//    }
//];

var Games = {
    online: {
        init: function () {
//        alert("Online");
            Games.online.tabs();
            if (!Games.online.timeout) {
                Games.online.timeouts("#games-online-view-now > form", 5000);
            }

            return;
        },
        //online view
        tabs: function () {
            var tabs = $('#games-online-view-tabs > div'),
                    tabsBlocks = $('.game-settings .blocks > div');
            if (!tabs)
                return;
            $(tabs).click(function () {
                if ($(this).hasClass("active"))
                    return;
                //hide tabs container, remove active class from tab-button
                $(tabsBlocks).hide();
                $(tabs).removeClass('active');
                //show tabs container, add active class to tab-button
                $(this).addClass('active');
                $($(this).data("to")).show().find('form.render-list-form').change(); // плюшка для обновления данных при клике
            });
        },
        timeouts: function (element, time) {
            Games.online.timeout = setTimeout(function () {
                if ($(element).is(":visible")) {
                    console.error("timeout>>>>>>> on:visible ", element);
                    $(element).change();
                } else {
//                console.error("else",element);
                    clearTimeout(Games.online.timeout);
                }
                Games.online.timeouts(element, time);
            }, time);
        }

    },
    chance: {
        conf: {
            data: {},
            play: !1
        },
        init: function (data) {
            if (!data.json)
                return false;
            // make config
            Games.chance.conf.data = data.json;
//            alert('chance get init!!!');
            console.error(" data.json.id >>>>>>>", data.json.id);
            Games.chance.get("#games-chance-view-cells button:not(.played)", data.json.id);
            // in multiple prizes set first prize as @current@
            $("#games-chance-view-chance *:first-child[data-current] ").addClass('currennt');
            return;
        },
        //chances view
        get: function (elements, id) {

            $(elements).click(function () {
                if (!Games.chance.conf.play)
                    return false;
                var cell = $(this).data('cell'), that = this;
                console.info('>>>', $(that).attr('class'));
                // send 
                Form.get.call(this,
                        {
                            href: '/games/chance/' + id + '/play',
                            data: {'cell': cell},
                            after: function (data) {
                                console.log(data.json);
                                if (data.json.error)
                                    return;
                                // code after 
                                console.info(JSON.stringify(data.json, null, 2));
                                // prize
                                if (data.json.Prize) {
                                    $(that).addClass('win');
                                } else {
                                    $(that).addClass('lose');
                                }
                                // steps
                                if (data.json.Moves) {
                                    Games.chance.prizesMoves(data.json.Moves);
                                }
                                // Game winner Fields
                                if (data.json.GameField) {
                                    if (data.json.Prize) {
                                        $("#games-chance-view-chance").addClass('win');
                                    } else {
                                        $("#games-chance-view-chance").addClass('lose');
                                    }
                                    Games.chance.end(data.json.GameField);
                                }
                                $(that).addClass('played');
                            }
                        });
            });
        },
        play: function (id) {
            Form.post.call(this, {
                href: '/games/chance/' + id,
                after: function (data) {
//                    alert(JSON.stringify(data.json, null, 2));
                    // update trigger 'game ready'
                    Games.chance.conf.play = !0;
                    // update all cells to default state
                    Games.chance.resset();
//                    add class for css styles
                    $("#games-chance-view-chance").attr('class', 'game-started');
                }
            });
        },
        end: function (fields) {
            for (var i in fields) {
                if (fields[i]) {
                    $("#games-chance-view-chance").removeClass('game-started');
                    $('#games-chance-view-chance button[data-cell="' + i + '"]').addClass('win');
                    Games.chance.conf.play = !1;
                }
            }

        },
        prizesMoves: function (moves) {
//            console.error(">> moves >",moves);
            var missCounter = Games.chance.conf.data.field.m - moves;
//            data-current
            $("#games-chance-view-chance [data-current]").removeClass('currennt');
            $("#games-chance-view-chance [data-current=" + missCounter + "]").addClass('currennt');
        },
        resset: function () {
            $("#games-chance-view-cells button").removeAttr('class');
        }
    },
    random: {
        conf: {
            data: {},
            play: !1
        },
        init: function (data) {
            if (!data.json)
                return false;
            // make config
            Games.random.conf.data = data.json;
            Games.random.conf.play = !0;
            $("#games-random").attr('class', 'game-started');
            Games.random.get("#games-random-cells button:not(.played)", data.json.id); // data.json.id -- не используеться пока
        },
        get: function (elements, id) {
            console.error("Games.random.get >>>>", elements);
            $(elements).click(function () {
                if (!Games.random.conf.play)
                    return false;
                var cell = $(this).data('cell'), that = this;

//                if (fakeRandomData) {
//                    // prize
//                    var data = {json: fakeRandomData[fakeCounter]};
//
//                    if (data.json.error)
//                        return;
//
//                    if (data.json.Prize) {
//                        $(that).addClass('win');
//                        $(that).html(Games.random.makePrizeCell(data.json.Prize));
//                    } else {
//                        $(that).addClass('lose');
//                    }
//                    // steps
//                    if (data.json.Moves) {
//                        console.error("data.json.Moves >>>", data.json.Moves);
////                        Games.random.prizesMoves(data.json.Moves);
//                    }
//                    // Game winner Fields
//                    if (data.json.GameField) {
//                        console.error("data.json.GameField >>>>>>", data.json.GameField);
//                        if (data.json.Prize) {
//                            $("#games-random").addClass('win');
//                        } else {
//                            $("#games-random").addClass('lose');
//                        }
//                        Games.random.end(data.json.GameField);
//                    }
//                    $(that).addClass('played');
//
//
//                    fakeCounter >= fakeRandomData.length - 1 ? fakeCounter = 0 : fakeCounter += 1;
//
//                }


                // send

                Form.get.call(this,
                        {
                            href: '/games/random/play',
                            data: {'cell': cell},
                            after: function (data) {
//                                >>> code from top add here!!!
//                                var data = {json: fakeRandomData[fakeCounter]};

                    if (data.json.error)
                        return;

                    if (data.json.Prize) {
                        $(that).addClass('win');
                        $(that).html(Games.random.makePrizeCell(data.json.Prize));
                    } else {
                        $(that).addClass('lose');
                    }
                    // steps
                    if (data.json.Moves) {
                        console.error("data.json.Moves >>>", data.json.Moves);
//                        Games.random.prizesMoves(data.json.Moves);
                    }
                    // Game winner Fields
                    if (data.json.GameField) {
                        console.error("data.json.GameField >>>>>>", data.json.GameField);
                        if (data.json.Prize) {
                            $("#games-random").addClass('win');
                        } else {
                            $("#games-random").addClass('lose');
                        }
                        Games.random.end(data.json.GameField);
                    }
                    $(that).addClass('played');
//                                >>> code from top add here!!!
                                    
                            }
                        });
            });
        },
        makePrizeCell: function (prize) {
            var html = "<div class='flipFix'>"
            switch (prize.t) {
                case 'money':
                    html += "<span>" + prize.v + "</span>";
                    html += "<span>" + Player.getCurrency() + "</span>";
                    break;
                case 'points':
                    html += "<span>" + prize.v + "</span>";
                    html += "<span>" + Cache.i18n("title-of-points") + "</span>";
                    break;
                case 'item':
                    html += "<span>" + prize.n + "</span>";
                    break;
            }
            return html += "</div>";
        },
        end: function (fields) {
            for (var i in fields) {
                if (fields[i]) {
                    $("#games-random").removeClass('game-started');
                    $('#games-random button[data-cell="' + i + '"]').html(Games.random.makePrizeCell(fields[i])).addClass('win');
                    Games.random.conf.play = !1;
                }
            }

        }
    }




};



