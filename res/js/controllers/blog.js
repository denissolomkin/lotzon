(function () {

    Blog = {

        init: function () {
            R.push({
                'box': $('.content-box-content:visible'),
                'template': 'blog-posts',
                'node': visible('.content-box-content')
            })
        },

        post: {

            init: function (options) {
                R.push(options.href + '/comments');
                R.push(options.href + '/similar');
            }

        },

        after: {

            reply: function (options) {
                this.innerHTML = "New post added";
            }
        },

        validate: {

            reply: function (event) {
                return true;
            }
        },

        do: {

            replyForm: function () {

                var existingForms = [],
                    button = this,
                    model = {
                        'user': {
                            "name": button.getAttribute("data-user-name"),
                            'id': button.getAttribute("data-user-id")
                        },
                        'comment': button.getAttribute("data-comment-id"),
                        'post': button.getAttribute('data-post-id')
                    },
                    href = 'blog-post-comments-form',
                    box = button.parentNode,
                    commentsNode = box.parentNode;

                // up to comment block
                while (!box.classList.contains('comment-content'))
                    box = box.parentNode;

                // find other forms
                while (!commentsNode.classList.contains('render-list'))
                    commentsNode = commentsNode.parentNode;

                existingForms = commentsNode.getElementsByClassName('message-form-wrapper');
                if (existingForms.length)
                    for (var i = 0; i < existingForms.length; i++)
                        existingForms[i].remove();

                // push new form
                R.push({
                    append: true,
                    href: href,
                    json: model,
                    box: $(box),
                    node: box
                });

            },

            mobileForm: function () {

                if (!Device.isMobile())
                    return;

                if (this.getElementsByClassName('message-form').length)
                    return;

                var forms = visible('.comment-reply');
                forms.push(this.querySelector('.comment-reply'));

                toggle(forms); // hide

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