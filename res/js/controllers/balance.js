(function() {

    Balance = {

        moneyConvert: '.cc-out .cc-sum',
        pointsConvert: '.cc-income .cc-sum',
        moneyCashout: 'input.cco-sum',

        validate: {

            convert: function() {

                var $input_money = $(Profile.moneyConvert),
                    $calc_points = $(Profile.pointsConvert, $input_money.closest('form')),
                    input_money = Player.checkMoney($input_money.val()),
                    calc_points = Player.calcPoints(input_money);

                $input_money.val(input_money);
                $calc_points.val(calc_points);

                return true;

            },

            cashout: function() {


                var $input_money = $(Profile.moneyCashout),
                    input_money = Player.checkMoney($input_money.val());

                $input_money.val(input_money);
                // input-radio
                if(!$('[action="/balance/cashout"] input.input-radio:checked').length){
                    $('[action="/balance/cashout"] label.input-radio').addClass('fail');
                    setTimeout(function(){
                        $('[action="/balance/cashout"] label.input-radio').removeClass('fail');
                        
                    },4000)
                }
                
                if(!$('[action="/balance/cashout"] input[name="sum"]').val()){
                    $('[action="/balance/cashout"] input[name="sum"]').addClass('fail');
                    setTimeout(function(){
                        $('[action="/balance/cashout"] input[name="sum"]').removeClass('fail');
                        
                    },4000)
                }

                // console.error($(Profile.moneyCashout), this);


                return true || input_money >= parseFloat(Config.minMoneyOutput);

            },
        },

        after: {

            cashout: function() {
                Player.updateBalance();
                $('.cco-enter-sum-box').innerHTML = i18n("button-user-remove-request");
            }

        },

    };

})();