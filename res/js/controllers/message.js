(function () {

    if(typeof I === 'undefined') I={};
    Object.deepExtend(I, {

        /* communication */
        comment: '.comment',
        notifications: '.c-notifications',
        showNotifications: '.c-show-notifications',
        hideNotifications: '.c-hide-notifications',
        notificationsList: '.c-notifications-list',
        closeList: '.c-notifications-list .close-list',
        closeNotification: '.c-notification .close-notification',
        textArea: '.message-form-area'

    });

//setup before functions
    var typingTimer;                //timer identifier
    var doneTypingInterval = 5000;  //time in ms, 5 second for example
    var $input = $('#myInput');

//on keyup, start the countdown
    $input.on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });

//on keydown, clear the countdown
    $input.on('keydown', function () {
        clearTimeout(typingTimer);
    });

//user is "finished typing," do something
    function doneTyping () {
        //do something
    }

    Message = {

        typingTimer: null,
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

            clearUser: function () {

                document.getElementById('communication-messages-new')
                    .getElementsByTagName('FORM')[0]
                    .elements['recipient_id'].value = '';

                R.push({
                    'href': 'communication-messages-new-user',
                    'json': {}
                });

            },

            setUser: function () {

                var user = {
                    id: this.getAttribute('data-user-id'),
                    name: this.getAttribute('data-user-name'),
                    img: this.getAttribute('data-user-img')
                };

                document.getElementById('communication-messages-new')
                    .getElementsByTagName('FORM')[0]
                    .elements['recipient_id'].value = user.id;

                document.getElementById('communication-messages-new-users').innerHTML = '';

                R.push({
                    template: 'communication-messages-new-user',
                    json: user
                });

            },

            searchUser: function () {
                var find = this.value;
                document.getElementById('communication-messages-new-users').innerHTML = '';
                Message.typingTimer && clearTimeout(Message.typingTimer);
                Message.typingTimer = setTimeout(function(){
                    R.push({
                        template: 'communication-messages-new-users',
                        href: '/users/search',
                        query: {name: find}
                    });
                }, 1000);

            }
        }
    }

})();