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


         // showPreviewImage: function () {

         //    var formData = new FormData;

         //    $.each(this.files, function (i, file){
         //       formData.append('files[0]', file);
         //    });

         //    fileInputResult = function(result) {
         //       var span = document.createElement('span');
         //        span.innerHTML = ['<img class="thumb" src="', result,
         //                '" title="', escape(theFile.name), '"/>'].join('');
         //        document.querySelector('.message-form-actions').insertBefore(span, null);  
         //    }

         //    xhrRequest('post', url, formData, fileInputResult, error);

            
         //    var i = 0,
         //    files = this.files,
         //    len = files.length;
 
         //    for (; i < len; i++) {
         //        console.log("Filename: " + files[i].name);
         //        console.log("Type: " + files[i].type);
         //        console.log("Size: " + files[i].size + " bytes");
         //    }
         // },

        showPreviewImage: function () {
            var currentReview = {
                image: '',
                text: '',
                id: 0,
            };

            var answerReview = {
                image: '',
                text: '',
                id: 0,
                reviewId: null,
            };
            var span = document.createElement('span');
                span.innerHTML = '<img class="thumb" src="">';
            document.querySelector('.message-form-actions').insertBefore(span, null);  

            var image = $('.thumb');
            if(!currentReview.image)
            {
                // create form
                var form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="image"/></form>');
                //$(button).parents('.photoalbum-box').prepend(form);

                var input = form.find('input[type="file"]').damnUploader({
                    url: '/review/uploadImage',
                    fieldName: 'image',
                    data: currentReview,
                    dataType: 'json',
                });



                input.off('du.add').on('du.add', function(e) {

                    e.uploadItem.completeCallback = function(succ, data, status) {
                        // image.attr('src', data.res.imageWebPath).show();
                        image.attr('src', data.res.imageWebPath);
                        
                        // $('.reviews .rv-upld-img img').attr('src','/tpl/img/but-delete-review.png');
                        currentReview.image = data.res.imageName;
                    };

                    e.uploadItem.progressCallback = function(perc) {
                        console.log(perc)
                    }

                    e.uploadItem.addPostData('Id', currentReview.id);
                    e.uploadItem.addPostData('Image', currentReview.image);
                    e.uploadItem.upload();
                });

                form.find('input[type="file"]').click();
            } else {


                $.ajax({
                    url: "/review/removeImage/",
                    method: 'POST',
                    async: true,
                    data: {image:currentReview.image},
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 1) {

                            span.remove();
                            currentReview.image = null;

                        } else {
                            alert(data.message);
                        }
                    },
                    error: function() {
                        alert('Unexpected server error');
                    }
                });


            }
        },


        showSmiles: function () {
            $(this).closest('.message-form').find('.smiles').toggleClass('hidden');
            $('.message-form-smileys').toggleClass('active');
        }, 

        chooseSmiles: function () {
            

            div =  document.querySelector('.message-form-area');
            console.log(div);
            smile = this;

            console.log(smile);
            div =  document.querySelector('.message-form-area');
            console.log(div);
            smile = this.cloneNode(true);


            smile_data = this.className;
            var img = document.createElement('img');
            img.src = '/res/img/smiles_png/'+ smile_data + '.png';
            img.className = smile_data;


            div.appendChild(img);

        }

    }

})();


