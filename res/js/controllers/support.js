(function () {

    Support = {

        init: function () {

            D.log('Support.init');
            $('.rules p').hide();

        },

        do: {

            collapse: function () {

                D.log('Support.do.collapse');

                if($(this).next().is(':visible') == false )
                    $('.support p').slideUp(300);

                $(this).next().slideToggle(300);


            }
        }
    }

})();