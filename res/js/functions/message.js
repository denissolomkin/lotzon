$(function () {

    Message = {

        clearAddressee: function () {
            R.render({
                'template': 'communication-messages-new',
                'url': false,
                'callback': function (html) {
                    var replaceBox = '.addressee';
                    var replaceHTML = $(replaceBox, $(html)).html();
                    $(replaceBox)
                        .html(replaceHTML)
                        .hide()
                        .fadeIn();
                }
            });
        },

        setAddressee: function () {
            var userId = $(this).data('userid');
            R.render({
                'template': 'communication-messages-new?users=' + userId,
                'url': false,
                'callback': function (html) {
                    var replaceBox = '.addressee';
                    var replaceHTML = $(replaceBox, $(html)).html();
                    $(replaceBox)
                        .html(replaceHTML)
                        .hide()
                        .fadeIn();
                }
            });
        },

        searchAddressee: function () {
            var search = $(this).val();
            $.getJSON(
                U.Generate.Json('/users/search?match=' + search),
                function (data) {
                    R.render({
                        'template': 'communication-messages-new',
                        'json': data.res,
                        'url': false,
                        'callback': function (html) {
                            var replaceBox = '.addressee .nm-search-result-box';
                            var replaceHTML = $(replaceBox, $(html)).html();
                            $(replaceBox)
                                .html(replaceHTML)
                                .hide()
                                .fadeIn();
                        }
                    });

                });
        },

        send: function () {
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
                                    var replaceBox = '.addressee .nm-search-result-box';
                                    var replaceHTML = $(replaceBox, $(html)).html();
                                    $(replaceBox)
                                        .html(replaceHTML)
                                        .hide()
                                        .fadeIn();
                                }
                            });
                        } else {
                            throw(data.message);
                        }
                    }, "json");
        }
    }
});