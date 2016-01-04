var Games = {
    online: {
        init: function () {
//        alert("Online");
            Games.online.tabs();
//            Games.online.timeouts("#games-online-view-connect > form", 5000);

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
            var timeout = setTimeout(function () {
                if ($(element).is(":visible")) {
//                console.error("visible",element);
                    $(element).change();
                } else {
//                console.error("else",element);
                    clearTimeout(timeout);
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
            $("#games-chancegame *:first-child[data-current] ").addClass('currennt');
            return;

        },
        //chances view
        get: function (elements, id) {

            $(elements).click(function () {
                if (!Games.chance.conf.play) return false;
                
                var cell = $(this).data('cell'), that = this;
                console.info('>>>', $(that).attr('class'));
                // send 
                Form.get.call(this,
                        {
                            href: '/games/chance/' + id + '/play',
                            data: {'cell': cell},
                            after: function (data) {
                                console.log(data.json);
                                if(data.json.error) return;
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
                                    if(data.json.Prize){
                                        $("#games-chancegame").addClass('win');
                                    }else{
                                        $("#games-chancegame").addClass('lose');
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
                    $("#games-chancegame").attr('class','game-started');
                }
            });
        },
        end: function (fields) {
            for (var i in fields) {
                if (fields[i]) {
                    $("#games-chancegame").removeClass('game-started');
                    $('#games-chancegame button[data-cell="' + i + '"]').addClass('win');
                    Games.chance.conf.play = !1;
                }
            }

        },
        prizesMoves: function(moves) {
//            console.error(">> moves >",moves);
            var missCounter = Games.chance.conf.data.field.m - moves;
//            data-current
            $("#games-chancegame [data-current]").removeClass('currennt');
            $("#games-chancegame [data-current="+missCounter+"]").addClass('currennt');
        },
        resset: function () {
            $("#games-chance-view-cells button").removeAttr('class');
        }
    }




};



