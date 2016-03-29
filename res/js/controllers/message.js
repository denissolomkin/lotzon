(function () {

    Messages = {

        doneTypingInterval: 1000,
        typingTimer       : null,
        init              : function () {

        },

        after: {

            markRead: function (data) {

                var message = {
                    'communication': {
                        'messages': {}
                    }},

                    id = ('nodeType' in this)
                        ? this.getAttribute('data-id')
                        : data.json.id;

                message.communication.messages[id] = {
                    id: id,
                    isUnread: false
                };

                Cache.update(message);

            }
        },

        do: {

            markRead: function (event) {
                Form.delete.call(this, '/users/' + this.getAttribute('data-id') + '/messages');
            },

            clearUser: function () {

                document.getElementById('communication-messages-new')
                    .getElementsByTagName('FORM')[0]
                    .elements['recipient_id'].value = '';

                R.push({
                    'href': 'communication-messages-new-user',
                    'json': {}
                });

            },

            setUser: function () {

                var user = {
                    id  : this.getAttribute('data-user-id'),
                    name: this.getAttribute('data-user-name'),
                    img : this.getAttribute('data-user-img')
                };

                document.getElementById('communication-messages-new')
                    .getElementsByTagName('FORM')[0]
                    .elements['recipient_id'].value = user.id;

                document.getElementById('communication-messages-new-users').innerHTML = '';

                R.push({
                    template: 'communication-messages-new-user',
                    json    : user
                });

            },

            searchUser: function () {

                var find = this.value;

                document.getElementById('communication-messages-new-users').innerHTML = '';

                Messages.typingTimer && clearTimeout(Messages.typingTimer);

                if (find.length >= 3)
                    Messages.typingTimer = setTimeout(function () {
                        R.push({
                            template: 'communication-messages-new-users',
                            href    : '/users/search',
                            query   : {name: find}
                        });
                    }, Messages.doneTypingInterval);

            }
        }
    }

})();