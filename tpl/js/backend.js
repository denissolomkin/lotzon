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

function addTicket(combination, successFunction, failFunction, errorFunction) 
{
    $.ajax({
        url: "/game/ticket/",
        method: 'POST',
        data: {
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