(function() {

    Friends = {
        // init: function() {
        //         console.debug(this);
            
        // },
        search: {
            init: function(){
                console.debug(this);
                $('#users-search select').chosen();

                // $.ajax({
                //   url: "/logout/",
                //   dataType: "json",
                //   beforeSend: function(){$('ul.chzn-results').empty();},
                //   success: function( data ) {
                //     response( $.map( data, function( item ) {
                //       $('ul.chzn-results').append('<li class="active-result">' + item.name + '</li>');
                //     }));
                //   }
                // });
            }
        },
        do: {
            searchUser: function() {

                var find = this.value;

                Messages.typingTimer && clearTimeout(Messages.typingTimer);

                if (find.length >= 3 || find.length == 0)
                    Messages.typingTimer = setTimeout(function() {
                        R.push({
                            template: 'users-friends',
                            href: '/users/friends/list',
                            query: { match: find }
                        });
                    }, Messages.doneTypingInterval);

            }
        }
    }

})();
