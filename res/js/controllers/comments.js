(function () {

    if (typeof I === 'undefined') I = {};
    Object.deepExtend(I, {notificationsList: '.c-notifications-list'});

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

        renderNotifications: function () {

            var notifications = document.getElementById('communication-notifications');
            if (notifications) {
                var notificationsList = notifications.getElementsByClassName('c-notifications-list')[0];
                if (Player.getCount('notifications') || (notificationsList && notificationsList.style.display !== 'block')) {
                    R.push({
                        href: 'communication-notifications',
                        json: {}
                    });
                }
            }
        },

        after: {

            reply: function () {

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

                options.rendered.firstElementChild.classList.add('animated');
                options.rendered.firstElementChild.classList.add('zoomIn');

                DOM.cursor('.message-form-area', options.rendered);
            },

            showComment: function (options) {
                var commentReply = options.rendered.querySelector('.comment-content .comment-reply-btn');
                if (commentReply)
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

            closeNotifications: function (event) {

                event.preventDefault();
                event.stopPropagation();

                var notifications = document.getElementById('communication-notifications').getElementsByClassName('c-notification'),
                    obj = {
                        communication: {
                            notifications: {}
                        }
                    };

                Player.decrement('notifications', notifications.length);

                for (var i = 0; i < notifications.length; i++) {
                    obj.communication.notifications[notifications[i].getAttribute('data-id')] = null;
                }

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

                Player.decrement('notifications', notifications.length);

                for (var i = 0; i < notifications.length; i++) {
                    obj.communication.notifications[notifications[i].parentNode.getAttribute('data-id')] = null;
                }

                Cache.remove(obj);

                if (loadedComment) {
                    event.preventDefault();
                    event.stopPropagation();
                    Comments.hideNotifications();
                    DOM.scroll(loadedComment);
                    loadedComment.classList.add('highlight');
                }


            },

            replyForm: function () {

                var comment = DOM.up('comment-content', this),
                    node = DOM.up('comment', comment),
                    commentsList = DOM.up('render-list', node),
                    json = {
                        'user'      : {
                            "name": comment.getAttribute("data-user-name"),
                            'id'  : comment.getAttribute("data-user-id")
                        },
                        'comment_id': comment.getAttribute("data-comment-id"),
                        'post_id'   : comment.getAttribute('data-post-id')
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
        },

        showPreviewImage: function () {

            var img = $(".image-container");
            var $that = $(this),
                formData = new FormData($that.get(0)); // создаем новый экземпляр объекта и передаем ему нашу форму
            formData.append('date_upl', new Date()); // добавляем данные, не относящиеся к форме
            $.ajax({
                url        : $that.attr('action'),
                type       : $that.attr('method'),
                contentType: false, // важно - убираем форматирование данных по умолчанию
                processData: false, // важно - убираем преобразование строк по умолчанию
                data       : formData,
                dataType   : 'json',
                success    : function (json) {
                    if (json) {
                        img.attr("src", json);
                    }
                }
            });

        },

        showSmiles: function () {
            $(this).closest('.message-form').find('.smiles').toggleClass('hidden');
        }, 

        chooseSmiles: function () {

            div =  document.querySelector('.message-form-area');
            console.log(div);
            smile = this.cloneNode(true);
            console.log(smile);
            div.appendChild(smile);

            // console.log($(this).attr('class'));
            // console.log($(this).closest('.message-form').find('.message-form-area').html());
            // text =  $(this).closest('.message-form').find('.message-form-area').html();
            // div = $(this).closest('.message-form').find('.message-form-area');
            // element =  $(this);
            // div.appendChild(element);
            // $(this).closest('.message-form').find('.message-form-area').html(text + this);

        }





        // $('form[name="profile"]').find('.pi-ph.true i').off('click').on('click', function(e) {
        //     e.stopPropagation();

        //     removePlayerAvatar(function(data) {
        //         $('form[name="profile"]').find('.image-container').find('img').remove();
        //         $('form[name="profile"]').find('.image-container').removeClass('true');
        //     }, function() {}, function() {});
        // });
        // $('form[name="profile"]').find('.pi-ph').on('click', function(){
        //     // create form
        //     form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="image"/></form>');

        //     var input = form.find('input[type="file"]').damnUploader({
        //         url: '/players/updateAvatar',
        //         fieldName: 'image',
        //         dataType: 'json',
        //     });

        //     var image = $('<img></img>');
        //     var holder = $(this);
        //     if (holder.find('img').length) {
        //         image = holder.find('img');
        //     }

        //     input.off('du.add').on('du.add', function(e) {

        //         e.uploadItem.completeCallback = function(succ, data, status) {
        //             image.attr('src', data.res.imageWebPath);

        //             holder.addClass('true');
        //             holder.append(image);

        //             $('form[name="profile"]').find('.pi-ph.true i').off('click').on('click', function(e) {
        //                 e.stopPropagation();

        //                 removePlayerAvatar(function(data) {
        //                     $('form[name="profile"]').find('.pi-ph').find('img').remove();
        //                     $('form[name="profile"]').find('.pi-ph').removeClass('true');
        //                 }, function() {}, function() {});
        //             });
        //         };

        //         e.uploadItem.progressCallback = function(perc) {}
        //         e.uploadItem.upload();
        //     });

        //     form.find('input[type="file"]').click();
        // })


    }

})();


