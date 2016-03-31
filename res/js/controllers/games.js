var fakeCounter = 0,
    debug = 0;
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
}, {
    "status": 0,
    "message": "\u0418\u0433\u0440\u0430 \u043d\u0435 \u043d\u0430\u0439\u0434\u0435\u043d\u0430",
    "res": []
}];

var Games = {

    disableNow: false,
    timer: null,
    online: {

        init: function(options) {
            Games.online.tabs();
            Games.online.hideSelectedButtons();
            Apps.audio[options.json.key] = Object.clone(options.json.audio);
            // show MoreDescription-button if desc.height > 360px
            var desc = $(".game-description > div");
            var vis = desc.height() > 360 ? true : false;

            if (desc && vis) {
            
                desc.css({ 'max-height': '360px' });
                desc.prev().show();
            }
        },
        // скрывает одиночные кнопки в вкладке "создать" 
        hideSelectedButtons: function(){

            var z = document.querySelectorAll(' .create-game > form > [class^="game_"]');
            
            if(z.length == 0) return;

            for (var i = 0; i < z.length; i++) {
                var a = z[i].querySelectorAll(' .buttons-group');
                var b = z[i].querySelectorAll(' .button');

                if (b.length <= a.length) {
                    z[i].style.display = 'none';
                } else {
                    for (var j = 0; j < a.length; j++) {
                        if (a[j].querySelectorAll(' .button').length <= 1)
                            a[j].style.display = 'none';
                    }
                }

            }

        },
        //online view
        tabs: function() {
            var tabs = $('#games-online-view-tabs > div'),
                tabsBlocks = $('.game-settings .blocks > div');
            if (!tabs)
                return;
            $(tabs).click(function() {
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

        timeout: function() {
            Games.timer && clearInterval(Games.timer);
            Games.timer = setTimeout(Games.online.now, 5000);
        },

        now: function() {
            var element = '#games-online-view-now .render-list-form';
            !Games.disableNow && $(element).is(':visible') && $(element).change();
        },

        validate: {

            create: function(e) {

                
                // return if this != form
                if (this.tagName !== "FORM") return false;




                var valid = false,
                    msg = 'title-games-insufficient_funds';
                    
                // stupid aple!!!!!
                var mode = document.querySelector('[name="mode"]:checked');
                // var mode = this.mode.value.split('-');
                if(mode && mode.value){
                    mode = mode.value.split('-');
                    switch (mode[0]) {
                        case 'POINT':
                            valid = Player.balance.points*1 >= mode[1]*1;
                            // console.debug('Player.balance.points >= mode[1]', Player.balance.points + '>=' + mode[1]);
                            break;
                        case 'MONEY':
                            valid = Player.balance.money*1 >= mode[1]*1;
                            // console.debug('Player.balance.money >= mode[1]', Player.balance.money + '>=' + mode[1]);
                            break;
                        case 'LOTZON':
                            valid = Player.balance.lotzon*1 >= mode[1]*1;
                            // console.debug('Player.balance.lotzon >= mode[1]', Player.balance.lotzon + '>=' + mode[1]);
                            break;
                        case '':
                            msg = 'title-games-select_rate';
                            break;
                    }
                }else{
                    msg = 'title-games-select_rate';
                }

                // show popup message
                if (!valid) {
                    popup({ 'msg': i18n(msg), 'timer': 3000 });
                }
                return valid;

            },
        }

    },

    slots: {
        init: function (data) {
            Carousel.initOwl();
            slotMachine.init();
        },
        play: function(data){
            slotMachine.spin(data);
        }
    },
    
    wheel: {
        init: function (data) {
        },
        play: function(data){
        }
    },

    chance: {

        key: null,

        conf: {
            data: {},
            play: !1
        },

        init: function(data) {

            if (!data.json)
                return false;

            console.debug(">>>",data);
            // check if game not finished
            // Games.chance.checkGame(data.json.id);
            Games.chance.key = data.json.key;

            // make config
            Games.chance.conf.data = {};
            Games.chance.conf.data = data.json;
            Games.chance.get("#games-chance-view-cells button:not(.played)", data.json.id);
            // in multiple prizes set first prize as @current@
            $("#games-chance-view-chance *:first-child[data-current] ").addClass('current');

            return;
        },

        checkGame: function(id){
            // Form.post.call(this, {
            //     href: '/games/chance/' + id,
            //     after: function(data) {

            //         // check game IDs
            //         if(id != data.json.res.Id){
            //             // reload tmpl
            //             R.push({ href: 'games/chance/'+data.json.res.Id });
            //             console.debug('---- reload tmpl -----');

            //             return;
            //         }


            //         var fields = data.json.res.GameField;

            //         if(Object.keys(fields).length > 0){

            //             // update all cells to default state
            //             Games.chance.resset();

            //             // draw played cells
            //             for(var i in fields){
            //                 if(fields[i]){
            //                     $('.minefield [data-cell="'+i+'"]').addClass('win played');
            //                 }else{
            //                     $('.minefield [data-cell="'+i+'"]').addClass('lose played');
            //                 }
            //             }

            //             // update trigger 'game ready'
            //             Games.chance.conf.play = !0;

            //             // add class for css styles
            //             $("#games-chance-view-chance").attr('class', 'game-started');

            //         }

            //     }
            // });
        },
        //chances view
        get: function(elements, id) {

            $(elements).click(function() {
                if (!Games.chance.conf.play)
                    return false;
                var cell = $(this).data('cell'),
                    that = this;

                // send
                Form.get.call(this, {
                    href: '/games/chance/' + id + '/play',
                    data: { 'cell': cell },
                    after: function(data) {

                        if (data.json.error)
                            return;
                        
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
        play: function(id) {
            Form.post.call(this, {
                href: '/games/chance/' + id,
                after: function(data) {


                    // check game IDs
                    if(id != data.json.res.Id){
                        // reload tmpl
                        R.push({ href: 'games/chance/'+data.json.res.Id });
                        console.debug('---- reload tmpl -----');


                        // NEED CALLBACK ^ rpush
                        
                        return;
                    }


                    var fields = data.json.res.GameField;

                    if(Object.keys(fields).length > 0){

                        // update all cells to default state
                        Games.chance.resset();

                        // draw played cells
                        for(var i in fields){
                            if(fields[i]){
                                $('.minefield [data-cell="'+i+'"]').addClass('win played');
                            }else{
                                $('.minefield [data-cell="'+i+'"]').addClass('lose played');
                            }
                        }

                        // update trigger 'game ready'
                        Games.chance.conf.play = !0;

                        // add class for css styles
                        $("#games-chance-view-chance").attr('class', 'game-started');

                    }else{

                        // update trigger 'game ready'
                        Games.chance.conf.play = !0;
                        // update all cells to default state
                        Games.chance.resset();
                        // add class for css styles
                        $("#games-chance-view-chance").attr('class', 'game-started');
                    }
                }
            });
        },
        end: function(fields) {
            for (var i in fields) {
                if (fields[i]) {
                    $("#games-chance-view-chance").removeClass('game-started');
                    $('#games-chance-view-chance button[data-cell="' + i + '"]').addClass('win');
                    Games.chance.conf.play = !1;
                }
            }

        },
        prizesMoves: function(moves) {
            var missCounter = Games.chance.conf.data.field.m - moves;
            //            data-current
            $("#games-chance-view-chance [data-current]").removeClass('current');
            $("#games-chance-view-chance [data-current=" + missCounter + "]").addClass('current');
        },
        resset: function() {
            $("#games-chance-view-cells button").removeAttr('class');
        }
    },
    random: {
        conf: {
            data: {},
            play: !1,
            url: ""
        },
        init: function(data) {
            if (!data.json) {
                return false;
            }

            //check if error
            if (data.response.message && data.response.status == 0) {
                $(".moment-game-box .message").html("<p style='text-align: center;'>" + data.response.message + "</p>");
                return;
            }

            //clear data
            Games.random.conf.data = {};
            // make config
            Games.random.conf.data = data.json;
            Games.random.conf.play = !0;

            //check type of game: default @random@
            if (data.json.Key && data.json.Key.toLowerCase() == "moment") {
                Games.random.conf.url = "/games/moment/play";
            } else {
                Games.random.conf.url = "/games/random/play";
            }

            $(".moment-game-box").addClass('game-started');

            //            set .minefield size by ( cells * (cell-width + cell-margin) ) - cell-margin
            var size = data.json.Field;
            $(".moment-game-box .minefield").css({ 'width': ((parseInt(size.x) * (parseInt(size.w) + parseInt(size.r))) - parseInt(size.r)) + 'px' });

            Games.random.get(".minefield button:not(.played)", data.json.id); // data.json.id -- не используеться пока
            // set ad
            Content.banner.moment(data);
        },
        get: function(elements, id) {

            $(elements).click(function() {
                if (!Games.random.conf.play)
                    return false;

                var cell = $(this).data('cell'),
                    element = this;

                ////////////////////////////////////////////////////////////////
                //                DELETE
                if (debug === 1) {
                    var data = { json: fakeRandomData[fakeCounter] };
                    Games.random.actions(element, data);
                    fakeCounter >= fakeRandomData.length - 1 ? fakeCounter = 0 : fakeCounter += 1;
                    return;
                }
                ////////////////////////////////////////////////////////////////
                // console.debug("random - url>>>",Games.random.conf.url);
                Form.get.call(this, {
                    href: Games.random.conf.url,
                    data: { 'cell': cell },
                    after: function(data) {
                        Games.random.actions(element, data);
                    }
                });
            });
        },
        actions: function(element, data) {
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
        makePrizeCell: function(prize) {
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
        end: function(data) {
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
        showMessage: function(data) {
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
            } else {
                msg += Cache.i18n("title-games-chance-card-lose");
            }

            msg += "</p>";

            $(".moment-game-box .message").css({ "display": "none" }).html(msg).delay(2000).fadeIn(200);

        },
        //        remove block by timeout
        destroy: function(timer) {
            setTimeout(function() {
                $(".moment-game-box").remove();
            }, timer * 1000);
        }
    }
};
