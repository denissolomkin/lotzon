$(function () {

    Message = {

        clearAddressee: function () {
            R.push({
                'template': 'communication-messages-new',
                'replace': '.addressee',
                'url': false
            });
        },

        setAddressee: function () {
            var userId = $(this).data('userid');
            R.push({
                'template': 'communication-messages-new?users=' + userId,
                'replace': '.addressee',
                'url': false
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
                        'replace': '.addressee .nm-search-result-box',
                        'url': false
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
                                'replace': '.addressee .nm-search-result-box',
                                'url': false
                            });
                        } else {
                            throw(data.message);
                        }
                    }, "json");
        }
    }
});