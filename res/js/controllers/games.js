var Games = {
    init: function () {
        Games.tabs();
        Games.timeouts("#games-online-view-connect > form",5000);
        
        return;
    },
    tabs: function () {
        var tabs = $('#games-online-view-tabs > div'),
            tabsBlocks  = $('.game-settings .blocks > div');
        if(!tabs) return;
        
        $(tabs).click(function(){
            if($(this).hasClass("active")) return;
            //hide tabs container, remove active class from tab-button
            $(tabsBlocks).hide();
            $(tabs).removeClass('active');
            
            //show tabs container, add active class to tab-button
            $(this).addClass('active');
            $($(this).data("to")).show().find('form.render-list-form').change(); // плюшка для обновления данных при клике
        });
        
    },
    timeouts: function(element, time){
        var timeout = setTimeout(function(){
            if($(element).is(":visible")){
//                console.error("visible",element);
                $(element).change();
            }else{
//                console.error("else",element);
                clearTimeout(timeout);
            }
            GamesView.timeouts(element, time);
        }, time);
    }
};



