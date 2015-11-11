(function () {

    Reports = {

        init: function () {

            D.log('Reports.init');
            if ($('.daterange')
                    .filter(':visible')
                    .filter(function () {
                        return !$(this).data('daterangepicker')
                    })
                    .daterangepicker().length)
                Content.enableForm();

        }
    }

})();