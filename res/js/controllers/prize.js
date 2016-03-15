(function () {

    Prize = {

        init: function () {
            if(!DOM.byId('prizes-exchange'))
                R.push("prizes/exchange");
        },

        update: {
            exchange: function () {

                return true;

            }
        },

        validate: {
            balance: function (event) {

                if (1) {
                    alert("pfijfs")
                event.stopPropagation();
                event.preventDefault();
                Content.modal.call(this.children[0], "message-prize-isnt-many");
                }


            }, 
            exchange: function () {

                return true;

            }
        },

        error:{

            exchange: function(){

                return true;

            }

        }


    }

})();