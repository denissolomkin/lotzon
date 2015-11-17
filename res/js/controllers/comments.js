(function () {

    Comments = {
        hide: function (event) {

            $(I.comment).removeClass('active');

            if (!$(event.target).hasClass('c-show-notifications')) {
                if ($(I.notificationsList).is(':visible')) {
                    $(I.notificationsList).slideUp('fast');
                }
            }
        },

        do: {

            renderForm: function () {

                var button = this,
                    json = {
                        'userid': button.getAttribute('data-userid'),
                        'commentid': button.getAttribute('data-commentid'),
                        'postid': button.getAttribute('data-postid')
                    },
                    href = 'communication-comments-form',
                    box = button.parentNode;

                while (!box.classList.contains(''))
                    box = box.parentNode;


                R.push({
                    href: href,
                    json: json,
                    box: box
                })

            }
        }
    }

})();