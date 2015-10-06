$(function () {

    clickMenu = function (event){

        event.stopPropagation();

        var active = $(this).hasClass('active'),
            menu = $(this).attr('class').replace(/ |menu-btn-item|active/g, ''),
            mobile = isMobile();

        hideMenu();


        if(active)  return false;
        else        $(this).addClass('active');

        switch (menu) {
            case 'menu-btn':

                if(mobile) {
                    $(I.menuMain).show();
                    $(I.menuMore).show();
                    $(I.menu).fadeIn(200);
                } else {
                    $(I.menuMore).fadeIn(200);
                }
                break;

            case 'menu-profile-btn':
                if(mobile){
                    $(I.menuProfile).show();
                    $(I.menuMain).hide();
                    $(I.menu).fadeIn(200);
                } else {
                    $(I.menuProfile).fadeIn(200);
                }
                break;

            case 'balance-btn':
            case 'menu-balance-btn':
                $(I.menuBalance).fadeIn(200);
                break;


            default:
                break;
        }
    };


});