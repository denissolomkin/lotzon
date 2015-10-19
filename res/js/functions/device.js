$(function () {

    // DETECT DEVICE ========================== //

    Device = {

        get: function () {
            return $('.js-detect').css('opacity');
        },

        detect: function () {

            switch (parseFloat($('.js-detect').css('opacity')).toFixed(1)) {
                case '0.2':
                    return 'mobile-small';
                case '0.3':
                    return 'mobile-landscape';
                case '0.5':
                    return 'mobile';
                case '0.6':
                    return 'tablet';
                case '0.8':
                    return 'desktop';
                case '1.0':
                    return 'desktop-hd';

            }
        },

        isMobile: function () {
            return $('.js-detect').css('opacity') < 0.8;
        }
    }


});