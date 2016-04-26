(function() {

    Friends = {
        init: function() {
            alert(1);
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
