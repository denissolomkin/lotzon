function registerPlayer(playerData, successFunction, failFunction, errorFunction)
{
    $.ajax({
        url: "/players/register/",
        method: 'POST',
        data: playerData,
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                successFunction.call(playerData, data);
            } else {
                failFunction.call(playerData, data);
            }
        }, 
        error: function() {
            errorFunction.call(playerData, data);
       }
    });
}

function loginPlayer(authData, successFunction, failFunction, errorFunction) 
{
    $.ajax({
        url: "/players/login/",
        method: 'POST',
        data: authData,
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                successFunction.call(authData, data);
            } else {
                failFunction.call(authData, data);
            }
        }, 
        error: function() {
            errorFunction.call(authData, data);
       }
    });   
}

function updatePlayerProfile(playerData, successFunction, failFunction, errorFunction)
{
    $.ajax({
        url: "/players/update/",
        method: 'POST',
        data: playerData,
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                successFunction.call(playerData, data);
            } else {
                failFunction.call(playerData, data);
            }
        }, 
        error: function() {
            errorFunction.call(playerData, data);
       }
    });
}

function addTicket(tickNum, combination, successFunction, failFunction, errorFunction) 
{
    $.ajax({
        url: "/game/ticket/",
        method: 'POST',
        data: {
            'tnum' : tickNum,
            'combination' : combination,   
        },
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                successFunction.call(combination, data);
            } else {
                failFunction.call(combination, data);
            }
        },
        error: function() {
            errorFunction.call(combination, data);
       }
    });   
}

function loadLotteries(offset, onlyMine, successFunction, failFunction, errorFunction)
{
    if (!onlyMine) {
        onlyMine = 0;
    }
    $.ajax({
        url: "/content/lotteries?offset="+offset+"&onlyMine=" + (onlyMine ? 1: 0),
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                successFunction.call($(this), data);
            } else {
                failFunction.call($(this), data);
            }
        },
        error: function() {
            errorFunction.call($(this), data);
       }
    });   
}

function loadShop(category, offset, successFunction, failFunction, errorFunction)
{
    $.ajax({
        url: "/content/shop?category="+category+"&offset="+offset,
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                successFunction.call($(this), data);
            } else {
                failFunction.call($(this), data);
            }
        },
        error: function() {
            errorFunction.call($(this), data);
       }
    });   
}

function getLotteryData(successFunction, failFunction, errorFunction) {
    $.ajax({
        url: "/game/lastLottery",
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                successFunction.call($(this), data);
            } else {
                failFunction.call($(this), data);
            }
        },
        error: function() {
            errorFunction.call($(this), data);
       }
    });   
}

function loadNews(offset, successFunction, failFunction, errorFunction) {
    $.ajax({
        url: "/content/news?offset="+offset,
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                successFunction.call($(this), data);
            } else {
                failFunction.call($(this), data);
            }
        },
        error: function() {
            errorFunction.call($(this), data);
       }
    });      
}

function createItemOrder(order, successFunction, failFunction, errorFunction) 
{
    $.ajax({
        url: "/order/item/",
        method: 'POST',
        data: order,
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                successFunction.call(order, data);
                $('#shop-items-popup').removeClass('chance').find('.cs').show();
            } else {
                failFunction.call(order, data);
            }
        },
        error: function() {
            errorFunction.call(order, data);
       }
    });   
}

function loadLotteryDetails(lotteryId, deps, successFunction, failFunction, errorFunction) {
    var url = "/content/lottery/" + lotteryId;
    if (deps == 'next') {
        url = "/content/lottery/next/" + lotteryId;
    } else if (deps == 'prev') {
        url = "/content/lottery/prev/" + lotteryId;
    }
    $.ajax({
        url: url,
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                successFunction.call($(this), data);
            } else {
                failFunction.call($(this), data);
            }
        },
        error: function() {
            errorFunction.call($(this), data);
       }
    });   
}

function removePlayerAvatar(successFunction, failFunction, errorFunction) {
    $.ajax({
        url: '/players/updateAvatar',
        method: 'DELETE',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                successFunction.call($(this), data);
            } else {
                failFunction.call($(this), data);
            }
        },
        error: function() {
            errorFunction.call($(this), data);
       }
    });
}

function addEmailInvite(email, successFunction, failFunction, errorFunction)
{
    $.ajax({
        url: "/invites/email",
        method: 'POST',
        data: {
            'email' : email,
        },
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                successFunction.call($(this), data);
            } else {
                failFunction.call($(this), data);
            }
        },
        error: function() {
            errorFunction.call($(this), data);
       }
    });   
}

function startChanceGame(gi, successFunction, failFunction, errorFunction) {
    $.ajax({
        url: "/chance/build/" + gi,
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                successFunction.call($(this), data);
            } else {
                failFunction.call($(this), data);
            }
        }, 
        error: function() {
            errorFunction.call($(this), data);
       }
    });
}
function playChanceGame(gi, choose, successFunction, failFunction, errorFunction) {
    var data = {
        choose: choose
    };
    $.ajax({
        url: "/chance/play/" + gi,
        method: 'POST',
        data: data,
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                successFunction.call($(this), data);
            } else {
                failFunction.call($(this), data);
            }
        }, 
        error: function() {
            errorFunction.call($(this), data);
       }
    });
}

function getLandingStats(successFunction, failFunction, errorFunction)
{
    $.ajax({
        url: "/stats/promo",
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                successFunction.call($(this), data);
            } else {
                failFunction.call($(this), data);
            }
        }, 
        error: function() {
            errorFunction.call($(this), data);
       }
    });
}

function resendPassword(email, successFunction, failFunction, errorFunction) 
{
    $.ajax({
        url: "/players/resendPassword",
        method: 'POST',
        data: {
            email: email
        },
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                successFunction.call($(this), data);
            } else {
                failFunction.call($(this), data);
            }
        }, 
        error: function() {
            errorFunction.call($(this), data);
       }
    });   
}

window.setInterval(function() {
    $.ajax({
        url: "/players/ping",
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            // if main game screen is visible
            var gw = $("#game-won:visible").length || $("#game-end:visible").length || $("#game-process:visible").length || $("#game-itself:visible").length;
            if (!gw) {
                if (data.res && data.res.moment == 1) {
                    window.setTimeout(function() {
                        $("#mchance").hide();
                    }, 3 * 60000);
                    
                    $("#mchance").show();
                    $("#mchance").find('li').on('click', function(){
                        var li = $(this);
                        playChanceGame('moment', $(this).data('num'), function(data) {
                            if (data.res.status == 'win') {
                                li.html($("#mchance").data('pointsWin'));
                                li.addClass('won');
                                $("#mchance").hide();
                                $('.pz-ifo-bk').hide();
                                $('.pz-rt-bk').text("Вы выиграли в моментальный шанс!").show().parents('#shop-items-popup').show();          
                                window.setTimeout(function() {
                                    location.reload();
                                }, 4000);
                            } else {
                                li.addClass('los');

                                for (var i in data.res.field) {                                    
                                    if(data.res.field[i] == 1) {
                                        var num = i+1;
                                        $('li[data-num="' + num + '"]').addClass('blink');
                                    }
                                }
                                $('li.blink').html($("#mchance").data('pointsWin'));
                                var blinkCount = 3;
                                var blinkInterval = window.setInterval(function() {
                                    if (blinkCount == 0) {
                                        window.clearInterval(blinkInterval);
                                        $("#mchance").hide();
                                        $('.pz-ifo-bk').hide();
                                        $('.pz-rt-bk').text("Вы проиграли!").show().parents('#shop-items-popup').show();

                                        window.setTimeout(function() {
                                            location.reload();
                                        }, 4000);
                                        return;
                                    }
                                    blinkCount--;

                                    $('li.blink').toggleClass('true');
                                }, 600);
                            }
                        })
                    });
                }
            }
        },
        error: function() {}
    });   
}, 60 * 1000);