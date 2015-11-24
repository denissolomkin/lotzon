(function () {

    Communication = {

        after: {

            reply: function (options) {

                var form = this;

                if(form.elements['comment_id'].value){
                    setTimeout(function(){
                        form.parentNode.removeChild(form);
                    }, Form.getTimeout());
                } else {
                    form.getElementsByClassName('message-form-area')[0].innerHTML = '';
                }
            },

            replyForm: function (options) {

                if(!DOM.onScreen(options.rendered))
                    DOM.scroll(options.rendered);

                DOM.cursor('.message-form-area', options.rendered);
            }
        },

        do: {

            replyForm: function () {

                var json = {
                        'user': {
                            "name": this.getAttribute("data-user-name"),
                            'id': this.getAttribute("data-user-id")
                        },
                        'comment': this.getAttribute("data-comment-id")
                    },
                    node = this.parentNode;

                // up to comment block
                while (!node.classList.contains('comment') || node.classList.contains('answer'))
                    node = node.parentNode;

                // find other forms
                var commentsNode = node.parentNode;
                while (!commentsNode.classList.contains('render-list'))
                    commentsNode = commentsNode.parentNode;

                // delete other forms
                var existingForms = commentsNode.getElementsByTagName('FORM');
                if (existingForms.length)
                    for (var i = 0; i < existingForms.length; i++)
                        existingForms[i].remove();

                // push new form
                R.push({
                    href: 'communication-comments-replyform',
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