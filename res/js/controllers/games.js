var fakeCounter = 0, debug = 0;
var fakeRandomData = [{
        "Uid": "568b9d3875b2b",
        "Prize": {
            "t": "item",
            "v": "96",
            "s": "5436a10e394a3.jpg",
            "n": "\u0422\u0435\u0440\u043c\u043e\u043a\u0440\u0443\u0436\u043a\u0430 Stanley"
        },
        "Cell": "1x1",
        "Moves": 3,
        "comb": []
    }, {
        "Uid": "568b9d3875b2b",
        "Prize": {
            "t": "points",
            "v": "250"
        },
        "Cell": "2x1",
        "Moves": 2,
        "comb": []
    }, {
        "Uid": "568b9d3875b2b",
        "Prize": {
            "t": "money",
            "v": "25"
        },
        "Cell": "2x1",
        "Moves": 1,
        "comb": []
    }, {
        "Uid": "568b9d3875b2b",
        "Prize": false,
        "Cell": "3x1",
        "Moves": 0,
        "GameField": {
            "1x1": {
                "t": "item",
                "v": "96",
                "s": "5436a10e394a3.jpg",
                "n": "\u0422\u0435\u0440\u043c\u043e\u043a\u0440\u0443\u0436\u043a\u0430 Stanley"
            },
            "2x1": {
                "t": "points",
                "v": "25"
            },
            "3x1": false
        },
        "GamePrizes": {
            "MONEY": 250,
            "POINT": 25,
            "ITEM": " \u0422\u0435\u0440\u043c\u043e\u043a\u0440\u0443\u0436\u043a\u0430 Stanley"
        },
        "comb": [],
        "player": {
            "balance": {
                "points": 1097,
                "money": "2307.85"
            }
        }
    },
    {
        "status": 0,
        "message": "\u0418\u0433\u0440\u0430 \u043d\u0435 \u043d\u0430\u0439\u0434\u0435\u043d\u0430",
        "res": []
    }
];

var Games = {

    disableNow: true,
    timer: null,
    validate: function(e){
        alert('validate!!');
    //     // записаться в игровой стек
        $(document).on('submit', '#games-online-view-create form', function(event){
        
        console.debug(event, this, Player.balance);
        event.stopPropagation();
        event.preventDefault();
        return false;



            // if(appMode = $('.ngm-bk .rls-r .new-bl .prc-sel').find('.active').attr('data-price')) {
            //     price = appMode.split('-');
            //     appMode+='-'+($('.ngm-bk .rls-r .new-bl .plr-sel').find('.active').attr('data-players') ? $('.ngm-bk .rls-r .new-bl .plr-sel').find('.active').attr('data-players') : 2);

            //     if ((price[0] == 'POINT' && playerPoints < parseInt(price[1])) || (price[0] == 'MONEY' && playerMoney < getCurrency(price[1],1))) {

            //         $("#report-popup").show().find(".txt").text(getText('INSUFFICIENT_FUNDS')).fadeIn(200);

            //     } else {

            //         var variation = {};
            //         $('.ngm-bk .rls-r .new-bl .var-sel .var-vl.active').each(function() {
            //             variation[$(this).parent().attr('data-variation')]=$(this).attr('data-value');
            //         });

            //         var path = 'app/' + appName + '/0'; // + appId;
            //         var data = {'action': 'start', 'mode': appMode, 'variation': variation};

            //         WebSocketAjaxClient(path, data);

            //     }

            // } else {

            //     $("#report-popup").show().find(".txt").text(getText('CHOICE_BET')).fadeIn(200);

            // }
        });
    },
    online: {

        init: function () {
            Games.online.tabs();
            // Games.validate();
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

        timeout: function () {
            Games.timer && clearInterval(Games.timer);
            Games.timer = setTimeout(Games.online.now, 5000);
        },

        now: function(){
            var element = '#games-online-view-now .render-list-form';
            !Games.disableNow && $(element).is(':visible') && $(element).change();
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
            Games.chance.conf.data = {};
            Games.chance.conf.data = data.json;
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
                    // update trigger 'game ready'
                    Games.chance.conf.play = !0;
                    // update all cells to default state
                    Games.chance.resset();
					// add class for css styles
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
            play: !1,
            url: ""
        },
        init: function (data) {
            if (!data.json){
                return false;
            }

            //deb
//            console.error(">>>>",data);
            
            //check if error
            if (data.response.message && data.response.status == 0) {
	            $(".moment-game-box .message").html("<p style='text-align: center;'>"+data.response.message+"</p>");
                return;
            }

            //clear data
            Games.random.conf.data = {};
            // make config
            Games.random.conf.data = data.json;
            Games.random.conf.play = !0;

            //check type of game: default @random@
            if(data.json.Key && data.json.Key.toLowerCase() == "moment"){
                Games.random.conf.url = "/games/moment/play";
            }else{
                Games.random.conf.url = "/games/random/play";
            }

            $(".moment-game-box").addClass('game-started');
            
//            set .minefield size by ( cells * (cell-width + cell-margin) ) - cell-margin
            var size = data.json.Field;
            $(".moment-game-box .minefield").css({ 'width': (( parseInt(size.x) * ( parseInt(size.w) + parseInt(size.r) )) - parseInt(size.r))+'px' });

            Games.random.get(".minefield button:not(.played)", data.json.id); // data.json.id -- не используеться пока
            // set ad
            Content.banner.moment(data);
        },
        get: function (elements, id) {

            $(elements).click(function () {
                if (!Games.random.conf.play)
                    return false;

                var cell = $(this).data('cell'), element = this;
                
////////////////////////////////////////////////////////////////
//                DELETE
                if (debug === 1) {
                    var data = {json: fakeRandomData[fakeCounter]};
                    Games.random.actions(element,data);
                    fakeCounter >= fakeRandomData.length - 1 ? fakeCounter = 0 : fakeCounter += 1;
                    return;
                }
////////////////////////////////////////////////////////////////

                Form.get.call(this,
                        {
                            href: Games.random.conf.url,
                            data: {'cell': cell},
                            after: function (data) {
                                Games.random.actions(element, data);
                            }
                        });
            });
        },
        actions: function (element,data) {
            if (data.json.error)
                return;

            if (data.json.Prize) {
                $(element).addClass('win');
                // render prize 
                $(element).html(Games.random.makePrizeCell(data.json.Prize));
            } else {
                $(element).addClass('lose');
            }
            
            // Game winner Fields
            if (data.json.GameField) {
                if (data.json.Prize) {
                    $(".moment-game-box").addClass('win');
                } else {
                    $(".moment-game-box").addClass('lose');
                }
                Games.random.end(data.json);
            }
            $(element).addClass('played');
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
        }
        ,
        end: function (data) {
            var fields = data.GameField;
            for (var i in fields) {
                if (fields[i]) {
                    $(".moment-game-box").removeClass('game-started');
                    $('.moment-game-box button[data-cell="' + i + '"]').html(Games.random.makePrizeCell(fields[i])).addClass('win');
                    Games.random.conf.play = !1;

                    Games.random.showMessage(data.GamePrizes);
//                    Games.random.destroy(5);
                }
            }
        },
        showMessage: function (data) {
            var msg = "<p>";
            if (data && data.length !== 0) {
                msg += Cache.i18n("title-games-chance-card-win") + " ";
                for (var key in data) {
                    msg += "<span>" + data[key] + " ";
                    if (key === "POINT")
                        msg += Cache.i18n("title-of-points") + " ";
                    if (key === "MONEY")
                        msg += Player.getCurrency() + " ";
                    msg += "</span>";
                }
            } else{
                msg += Cache.i18n("title-games-chance-card-lose");
            }
            
            msg += "</p>";

            $(".moment-game-box .message").css({"display":"none"}).html(msg).delay( 2000 ).fadeIn( 200 );

        },
//        remove block by timeout
        destroy: function (timer) {
            setTimeout(function () {
                $(".moment-game-box").remove();
            }, timer * 1000);
        }
    }
};



