(function () {

    if (typeof I === 'undefined') I = {};
    Object.deepExtend(I, {notificationsList: '.c-notifications-list'});


    Comments = {
      emotionsToServer : {


                '<img src="/res/img/smiles_png/i-smile-angry.png" class="i-smile-angry">' : '*ANGRY*' ,
                '<img src="/res/img/smiles_png/i-smile-clown.png" class="i-smile-clown">' :'*CLOWN*' ,
                '<img src="/res/img/smiles_png/i-smile-confused.png" class="i-smile-confused">' :'*CONFUSED*'  ,
                '<img src="/res/img/smiles_png/i-smile-cool.png" class="i-smile-cool">' : '*COOL*'  ,
                '<img src="/res/img/smiles_png/i-smile-crying.png" class="i-smile-crying">' : ';('  ,
                '<img src="/res/img/smiles_png/i-smile-dislike.png" class="i-smile-dislike">' : ':(' ,
                '<img src="/res/img/smiles_png/i-smile-gay.png" class="i-smile-gay">' : '*GAY*'  ,
                '<img src="/res/img/smiles_png/i-smile-geek.png" class="i-smile-geek">' : '*GEEK*' ,
                '<img src="/res/img/smiles_png/i-smile-happy.png" class="i-smile-happy">' : '*HAPPY*',
                '<img src="/res/img/smiles_png/i-smile-kiss.png" class="i-smile-kiss">' :  '*KISS*',
                '<img src="/res/img/smiles_png/i-smile-laughing.png" class="i-smile-laughing">' : ':D',
                '<img src="/res/img/smiles_png/i-smile-like.png" class="i-smile-like">' : '*LIKE*',
                '<img src="/res/img/smiles_png/i-smile-money.png" class="i-smile-money">' : '*MONEY*',
                '<img src="/res/img/smiles_png/i-smile-robot.png" class="i-smile-robot">' : '*ROBOT*',
                '<img src="/res/img/smiles_png/i-smile-security.png" class="i-smile-security">' : '*SECURITY*',
                '<img src="/res/img/smiles_png/i-smile-sleepi.png" class="i-smile-sleepi">' : '*SLEEPI*',
                '<img src="/res/img/smiles_png/i-smile-smile.png" class="i-smile-smile">' : ':)',
                '<img src="/res/img/smiles_png/i-smile-surprise.png" class="i-smile-surprise">' : ':O',
                '<img src="/res/img/smiles_png/i-smile-tongue.png" class="i-smile-tongue">' : ':P',
                '<img src="/res/img/smiles_png/i-smile-wink.png" class="i-smile-wink">'  : ';)' 



        },

        // Text2Emotions : {

        //         '*ANGRY*' : '<img src="/res/img/smiles_png/i-smile-angry.png" class="i-smile-angry">',
        //         '*CLOWN*' : '<img src="/res/img/smiles_png/i-smile-clown.png" class="i-smile-clown">',
        //         '*CONFUSED*' : '<img src="/res/img/smiles_png/i-smile-confused.png" class="i-smile-confused">'



        // },

        getEmotionsHTML : function () {
            var html = '';
            for(var emotion in Comments.emotionsToServer)
                html+=emotion;
            return html;
        },

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
                if ($('.thumb') !=undefined) {
                    $('.thumb').remove();
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
            },

            
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




        showPreviewImage: function (e) {
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

            e.preventDefault();

            console.log(e);


            var span = document.createElement('span');
                span.className = 'thumb';
                span.innerHTML = '<i class="i-x-slim"></i>';
            document.querySelector('.message-form-actions').insertBefore(span, null);  

            var image = $('.thumb ');
            if(!currentReview.image)

            {

                // create form
                var form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="image"/></form>');
                //$(button).parents('.photoalbum-box').prepend(form);

                 $input = form.find('input[type="file"]').damnUploader({
                    url: '/res/POST/image',
                    fieldName: 'image',
                    data: currentReview,
                    dataType: 'json',
                });

                $input.off('du.add').on('du.add', function(e) {

                    e.uploadItem.completeCallback = function(succ, data, status) {
                        image.css({'background': 'url(' + Config.tempFilestorage + '/' + data.imageName + ') center', 'height':'55px'});
                        currentReview.image = data.imageName;
                        console.log(currentReview.image, 'currentReview.image')
                    };

                    e.uploadItem.progressCallback = function(perc) {
                        console.log(perc)
                    }

                    e.uploadItem.addPostData('Id', currentReview.id);
                    e.uploadItem.addPostData('Image', currentReview.image);
                    e.uploadItem.upload();
                    console.log(currentReview.image, 'currentReview.image')
                });

                form.find('input[type="file"]').click();

            } else {

                $.ajax({
                    url: '/res/POST/removeImage',
                    method: 'POST',
                    async: true,
                    data: {image:currentReview.image},
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 1) {
                            image.remove();
                            currentReview.image = null;

                        } else {
                            console.log(data.status, "data");
                            alert(data.message);

                        }
                    },
                    error: function() {
                        alert('Unexpected server error');
                    }
                });
            }
        },
        deletePreviewImage: function (e) {
            var currentReview = {
                image: '',
                text: '',
                id: 0,
            }, image = this;
            $.ajax({
                url: '/res/POST/removeImage',
                method: 'POST',
                async: true,
                data: {image:currentReview.image},
                dataType: 'json',
                success: function(data) {
                    if (data.status == 1) {
                        image.remove();
                        currentReview.image = null;

                    } else {
                        console.log(data.status, "data");
                        alert(data.message);

                    }
                },
                error: function() {
                    alert('Unexpected server error');
                }
            });
        },

        showSmiles: function () {
            $(this).closest('.message-form').find('.smiles').toggleClass('hidden');
            $('.message-form-smileys').toggleClass('active');
        }, 



        chooseSmiles: function (e) {
            console.log(e)  ;
            div = $(this).closest('.message-form-actions').prev();
            console.log($(this).clone())  ;
            div.append($(this).clone());

           var result = $('div[contenteditable="true"]')[0];
               
                result.focus();
                placeCaretAtEnd(result);
          
            function placeCaretAtEnd(el) {
                el.focus();
                if (typeof window.getSelection != "undefined"
                        && typeof document.createRange != "undefined") {
                    var range = document.createRange();
                    range.selectNodeContents(el);
                    range.collapse(false);
                    var sel = window.getSelection();
                    sel.removeAllRanges();
                    sel.addRange(range);
                } else if (typeof document.body.createTextRange != "undefined") {
                    var textRange = document.body.createTextRange();
                    textRange.moveToElementText(el);
                    textRange.collapse(false);
                    textRange.select();
                }
            }
        },
        pasteText: function (e) {
            

            var text = '';
            var that = $(this);

            if (e.clipboardData)
                text = e.clipboardData.getData('text/plain');
            else if (window.clipboardData)
                text = window.clipboardData.getData('Text');
            else if (e.originalEvent.clipboardData)
            text = $('<div></div>').text(e.originalEvent.clipboardData.getData('text'));

            if (document.queryCommandSupported('insertText')) {
                document.execCommand('insertHTML', false, $(text).html());
                return false;
            }
            else { // IE > 7
                that.find('*').each(function () {
                     $(this).addClass('within');
                });

                setTimeout(function () {
                      that.find('*').each(function () {
                           $(this).not('.within').contents().unwrap();
                      });
                }, 1);
            }
        },

        checkInput: function() {
            var empty, text, div, el;
            $('.message-form-area > div').each(function(){
                div = $(this);
                console.log($(this), '$(this)');
                el = div.text();

                    console.log(div.children().length, 'div.children()[0].tagName');

                    if (((el == '')&& div.children().length == 0) || (div.children().length > 0  && (div.children() && (div.children()[0].tagName == "BR" || div.children()[0].tagName == "HR") ))) {
                        console.log(div.children()[0]);
                         console.log(div,'div')
                        var emptyDiv = div;
                        emptyDiv.addClass('emptyDiv');
                    }
                
                if (emptyDiv != undefined && emptyDiv.next(emptyDiv) != undefined) {
                   emptyDiv.next(emptyDiv).css("display", "none"); 
                   console.log(' emptyDiv.next(emptyDiv)', emptyDiv.next(emptyDiv));
                }
                
            });

        },

        smilePost: function(form) {
            if ($('.smiles').hasClass('hidden')) {
                
            }
            else {
                console.log('VISIBLE');
              $('.smiles').addClass('hidden');  
            }
                
             
            
            for (var emotion in Comments.emotionsToServer) {
                form.data.text = form.data.text.replaceAll(emotion, Comments.emotionsToServer[emotion]);
            }
            return form;

        }

    }

})();




