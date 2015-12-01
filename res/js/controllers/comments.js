(function () {

    Comments = {

        hide: function (event) {

            if (!DOM.parent('c-notifications',event.target))
                Comments.hideNotifications();

            /*

             $(I.comment).removeClass('active');

            else {

                if (1 || document.getElementById('communication-notifications-list').children.length) {
                    Comments.showNotifications();
                    Content.infiniteScrolling();
                } else {
                    R.push({
                        href: 'communication-notifications-list',
                        after: Comments.showNotifications
                    });
                }
            }
            */

        },

        hideNotifications: function () {

            if ($(I.notificationsList).is(':visible')) {
                $(I.notificationsList).slideUp('fast');
            }
        },

        showNotifications: function () {
            $(I.notificationsList).slideDown('fast');
        },

        after: {

            reply: function (options) {

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
            }
        },

        validate: {

            reply: function (event) {
                return true;
            }
        },

        do: {

            closeNotification: function(event){

                var notification = this;
                while(!notification.classList.contains('c-notification'))
                    notification = notification.parentNode;

                notification.parentNode.remove(notification);
                //Cache.update({delete:{communication:{notifications:1}}});
            },

            viewComment: function(event){

                var notification = this;
                while(!notification.classList.contains('c-notification'))
                    notification = notification.parentNode;

                if(comment = document.getElementById(U.parse(this.action))){
                    event.preventDefault();
                    event.stopPropagation();
                    Comments.hideNotifications();
                    DOM.scroll(comment);
                }

                Comments.do.closeNotification.call(this);

            },

            replyForm: function () {

                var comment = this.parentNode,
                    node = this.parentNode;

                // up to first comment-content block
                while (!comment.classList.contains('comment-content'))
                    comment = comment.parentNode;

                json = {
                    'user': {
                        "name": comment.getAttribute("data-user-name"),
                        'id': comment.getAttribute("data-user-id")
                    },
                    'comment_id': comment.getAttribute("data-comment-id"),
                    'post_id': comment.getAttribute('data-post-id')
                };

                // up to comment block
                while (!node.classList.contains('comment') || node.classList.contains('answer'))
                    node = node.parentNode;

                // find other forms
                var commentsNode = node.parentNode;
                while (!commentsNode.classList.contains('render-list'))
                    commentsNode = commentsNode.parentNode;

                // delete other forms
                var existingForms = commentsNode.querySelectorAll('.comment > form');
                if (existingForms.length)
                    for (var i = 0; i < existingForms.length; i++)
                        existingForms[i].parentNode.removeChild(existingForms[i]);

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

                /*
                 // todo
                 width: 100%;
                 height: 100%;
                 left: 0;
                 top: 0;
                 background-color: rgba(255,255,255,0.5);
                 */
            }
        }

    }

})();