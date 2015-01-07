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
function is_numeric(mixed_var) {
    //   example 1: is_numeric(186.31); returns 1: true
    //   example 2: is_numeric('Kevin van Zonneveld'); returns 2: false
    //   example 3: is_numeric(' +186.31e2'); returns 3: true
    //   example 4: is_numeric(''); returns 4: false
    //   example 5: is_numeric([]); returns 5: false
    //   example 6: is_numeric('1 '); returns 6: false
    var whitespace =
        " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
    return (typeof mixed_var === 'number' || (typeof mixed_var === 'string' && whitespace.indexOf(mixed_var.slice(-1)) === -
        1)) && mixed_var !== '' && !isNaN(mixed_var);
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

function loadReviews(offset, successFunction, failFunction, errorFunction) {
    $.ajax({
        url: "/content/reviews?offset="+offset,
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

function getTransactions(offset, currency, successFunction, failFunction, errorFunction) {
    $.ajax({
        url: "/content/transactions/"+currency +"/",
        method: 'GET',
        data: {
            offset: offset,
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

function getNotices(offset, successFunction, failFunction, errorFunction) {
    $.ajax({
        url: "/content/notices/",
        method: 'GET',
        data: {
            offset: offset,
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

function socialSuccessPost(successFunction, failFunction, errorFunction) {
    $.ajax({
        url: "/players/social/",
        method: 'POST',
        data: {},
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

function uploadVkPhoto(url, successFunction, failFunction, errorFunction) {
    $.ajax({
        url: "/vkproxy",
        method: 'POST',
        data: {
            'uurl' : url,
        },
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                successFunction.call($(this), data.res);
            } else {
                failFunction.call($(this), data);
            }
        }, 
        error: function() {
            errorFunction.call($(this), data);
       }
    }); 
}

function requestForMoney(data, successFunction, failFunction, errorFunction) {
    $.ajax({
        url: "/order/money",
        method: 'POST',
        data: {
            'data' : data,
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

function sendPartnersFeedback(post, successFunction, failFunction, errorFunction) {
    $.ajax({
        url: "/feedback",
        method: 'POST',
        data: {
            'email' : post.email,
            'text' : post.text,
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
        url: "/players/ping?online="+(online?$.now():''),
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            // if main game screen is visible
            var gw = $(".ngm-bk .rls-r-ts:visible").length ||  $(".ngm-gm:visible").length || $("#game-won:visible").length || $("#game-won:visible").length || $("#game-end:visible").length || $("#game-process:visible").length || $("#game-itself:visible").length;
            if (!gw) {
                if (data.res && data.res.moment == 1) {
                    $('.popup').hide();
                    window.setTimeout(function() {
                        $("#mchance").hide();
                    }, 3 * 60000);
                    
                    $("#mchance").show();
                    $("#mchance").find('li').off('click').on('click', function(){
                        var li = $(this);
                        playChanceGame('moment', $(this).data('num'), function(data) {
                            if (data.res.status == 'win') {
                                li.html($("#mchance").data('pointsWin'));
                                li.addClass('won');
                                window.setTimeout(function() {
                                    $("#mchance").hide();
                                    $('.pz-ifo-bk').hide();
                                    $('.pz-rt-bk').text("Поздравляем, выигранные баллы зачислены на счет.").show().parents('#shop-items-popup').show();              
                                }, 2000)    
                                window.setTimeout(function() {
                                    location.reload();
                                }, 4000);
                            } else {
                                li.addClass('los');

                                for (var i in data.res.field) {                                    
                                    if(data.res.field[i] == 1) {
                                        var num = parseInt(i)+1;
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
                                        $('.pz-rt-bk').text("Повезет в следующий раз").show().parents('#shop-items-popup').show();

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