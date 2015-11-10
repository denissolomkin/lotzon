(function () {

    Message = {

        init: function () {

            /* ========================================================= */
            //                     COMMUNICATION
            /* ========================================================= */

            // COMMENTS ============================== //

            $(I.comment).on('click', function (event) {
                event.stopPropagation();
                $(I.comment).removeClass('active');
                if (Device.detect() === 'mobile') {
                    $(this).addClass('active');
                }
            });

            $(I.hideNotifications).on('click', function () {
                $(I.notifications).fadeOut('fast', function () {
                    $(I.notifications).remove();
                });
            });

            $(I.closeList).on('click', function () {
                $(I.notifications).slideUp('fast', function () {
                    $(I.notifications).remove();
                });
            });

            $(I.closeNotification).on('click', function () {
                if ($(I.notificationsList).find('.c-notification').length < 2) {
                    $(I.notifications).slideUp('fast', function () {
                        $(I.notifications).remove();
                    });
                } else {
                    $(this).parent().slideUp('fast', function () {
                        $(this).remove();
                    });
                }
            });

            $(I.showNotifications).on('click', function (event) {
                $(I.notificationsList).slideDown('fast');
            });

            $(I.notificationsList).on('click', function (event) {
                event.stopPropagation();
            });

            // $notifications.on('click', function(event) {
            // 	event.stopPropagation();
            // });

            // TEXTAREA ------------------------- //
            function h(e) {
                $(e).css({'height': 'auto', 'overflow-y': 'hidden'}).height(e.scrollHeight);
            }

            $(I.textArea).each(function () {
                h(this);
            }).on('input', function () {
                h(this);
            });
        },

        do: {

            clearAddressee: function () {
                R.push({
                    'template': 'communication-messages-new',
                    'replace': '.addressee'
                });
            },

            setAddressee: function () {
                var userId = $(this).data('userid');
                R.push({
                    'template': 'communication-messages-new?users=' + userId,
                    'replace': '.addressee'
                });
            },

            searchAddressee: function () {
                var search = $(this).val();
                $.getJSON(
                    U.Generate.Json('/users/search?match=' + search),
                    function (data) {
                        R.push({
                            'json': data.res,
                            'template': 'communication-messages-new',
                            'replace': '.addressee .nm-search-result-box'
                        });

                    });
            },

            send: function () {
                event.preventDefault();
                var formData = $(this).parents('form').serializeObject();
                if (formData.userid && formData.message)
                    $.post(
                        U.Generate.Post('/communications/messages/addMessage'),
                        formData,
                        function (data) {
                            if (data.status == 1) {
                                R.push({
                                    'json': data.res,
                                    'template': 'communication-messages-new',
                                    'replace': '.addressee .nm-search-result-box'
                                });
                            } else {
                                throw(data.message);
                            }
                        }, "json");
            }
        }
    }

})();