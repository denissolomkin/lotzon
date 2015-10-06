$(function () {

    clearMessageAddressee = function () {
        R.render({
            'template': 'communication-messages-new',
            'url': false,
            'callback': function (html) {
                $('.addressee').html($('.addressee', $(html)).html()).hide().fadeIn();
            }
        });
    }

    setMessageAddressee = function () {
        var userId = $(this).data('userid');
        R.render({
            'template': 'communication-messages-new?users=' + userId,
            'url': false,
            'callback': function (html) {
                $('.addressee').html($('.addressee', $(html)).html()).hide().fadeIn();
            }
        });
    }

    searchMessageAddressee = function () {
        $.getJSON(
            U.Generate.Json('/users/search?match=' + $(this).val()),
            function (data) {
                R.render({
                    'template': 'communication-messages-new',
                    'json': data.res,
                    'url': false,
                    'callback': function (html) {
                        $('.addressee .nm-search-result-box').html($('.addressee .nm-search-result-box', $(html)).html()).hide().fadeIn();
                    }
                });

            });
    }

    sendMessage = function () {
        event.preventDefault();
        var form = $(this).parents('form').serializeObject();
        if (form.userid && form.message)
            $.post(
                U.Generate.Post('/communications/messages/addMessage'),
                form,
                function (data) {
                    if (data.status == 1) {
                        R.render({
                            'template': 'communication-messages-new',
                            'json': data.res,
                            'url': false,
                            'callback': function (html) {
                                $('.addressee .nm-search-result-box').html($('.addressee .nm-search-result-box', $(html)).html()).hide().fadeIn();
                            }
                        });
                    } else {
                        throw(data.message);
                    }
                }, "json");
    }
});