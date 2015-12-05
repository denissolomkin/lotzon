(function () {

    Comments = {

        hide: function (event) {

            if (!DOM.up('c-notifications', event.target))
                Comments.hideNotifications();

        },

        hideNotifications: function () {

            if ($(I.notificationsList).is(':visible')) {
                $(I.notificationsList).slideUp('fast');
            }
        },

        after: {

            reply: function (data) {

                var form = this;

                if (form.elements['comment_id'].value) {
                    setTimeout(function () {
                        if (form.parentNode)
                            form.parentNode.removeChild(form);
                    }, form.getElementsByClassName('modal-message').length ? Form.getTimeout() : 0);
                }
            },

            replyForm: function (options) {

                if (!DOM.onScreen(options.rendered))
                    DOM.scroll(options.rendered);

                DOM.cursor('.message-form-area', options.rendered);
            },

            deleteNotifications: function () {

                Player.setCount('notifications', 0);
                Cache.remove({
                    communication: {
                        notifications: null
                    }
                });
                R.push({
                    href: 'communication-notifications',
                    json: {}
                });
            },

            showComment: function (options) {
                var commentReply = options.rendered.querySelector('.comment-content .comment-reply-btn');
                if(commentReply)
                    commentReply.click();
            }
        },

        validate: {

            reply: function (event) {
                return true;
            }
        },

        do: {

            showNotifications: function () {
                $(I.notificationsList).slideDown('fast');
                Content.infiniteScrolling();
            },

            closeNotification: function (event) {

                event.preventDefault();
                event.stopPropagation();

                var notification = DOM.up('c-notification', this),
                    obj = {
                        communication: {
                            notifications: {}
                        }
                    };

                obj.communication.notifications[notification.getAttribute('data-id')] = null;
                Player.decrement('notifications');
                Cache.remove(obj);

            },

            deleteNotifications: function (event) {

                Form.send.call(this, {
                    action: '/communication/notifications',
                    method: 'DELETE'
                });

            },

            viewComment: function (event) {

                var selector = '.c-notification [href="' + U.parse(this.href, 'url') + '"]',
                    notifications = document.getElementById('communication-notifications').querySelectorAll(selector),
                    loadedComment = document.getElementById(U.parse(this.href)),
                    obj = {
                        communication: {
                            notifications: {}
                        }
                    };

                Player.setCount('notifications', Player.setCount('notifications') - notifications.length);

                for (var i = 0; i < notifications.length; i++) {
                    obj.communication.notifications[notifications[i].parentNode.getAttribute('data-id')] = null;
                }

                Cache.remove(obj);

                if (loadedComment) {
                    event.preventDefault();
                    event.stopPropagation();
                    DOM.scroll(loadedComment);
                    loadedComment.classList.add('highlight');
                }

                Comments.hideNotifications();

            },

            replyForm: function () {

                var comment = DOM.up('comment-content', this),
                    node = DOM.up('comment', comment),
                    commentsList = DOM.up('render-list', node),
                    json = {
                        'user': {
                            "name": comment.getAttribute("data-user-name"),
                            'id': comment.getAttribute("data-user-id")
                        },
                        'comment_id': comment.getAttribute("data-comment-id"),
                        'post_id': comment.getAttribute('data-post-id')
                    };

                // delete other forms
                DOM.remove('.comment > form', commentsList);

                // up to comment block
                while (!node.classList.contains('comment') || node.classList.contains('answer'))
                    node = node.parentNode;

                // push new form
                R.push({
                    href: (json.post_id ? 'blog-post-view' : 'communication' ) + '-comments-replyform',
                    json: json,
                    node: node
                });

            },

            mobileForm: function () {

                if (!Device.isMobile())
                    return;

                if (this.getElementsByTagName('FORM').length)
                    return;

                var forms = DOM.visible('.comment-reply');
                forms.push(this.querySelector('.comment-reply'));

                DOM.toggle(forms); // hide
            }
        }

    }

})();