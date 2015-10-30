(function () {

    Comments = {
        hide: function (event) {

            $(I.comment).removeClass('active');

            if (!$(event.target).hasClass('c-show-notifications')) {
                if ($(I.notificationsList).is(':visible')) {
                    $(I.notificationsList).slideUp('fast');
                }
            }
        }
    }

})();