(function () {

    Comments = {

        notificationsList: '.c-notifications-list',

        emotionsToServer: {
            '<img src="/res/img/smiles_png/i-smile-smile.png" class="i-smile-smile">': ':)',
            '<img src="/res/img/smiles_png/i-smile-laughing.png" class="i-smile-laughing">': ':D',
            '<img src="/res/img/smiles_png/i-smile-cool.png" class="i-smile-cool">': '8)',
            '<img src="/res/img/smiles_png/i-smile-wink.png" class="i-smile-wink">': ';)',
            '<img src="/res/img/smiles_png/i-smile-confused.png" class="i-smile-confused">': '*CONFUSED*',
            '<img src="/res/img/smiles_png/i-smile-sad.png" class="i-smile-sad">': '*SAD*',
            '<img src="/res/img/smiles_png/i-smile-surprise.png" class="i-smile-surprise">': ':O',
            '<img src="/res/img/smiles_png/i-smile-angry.png" class="i-smile-angry">': '*ANGRY*',
            '<img src="/res/img/smiles_png/i-smile-kiss.png" class="i-smile-kiss">': ':*',
            '<img src="/res/img/smiles_png/i-smile-tongue.png" class="i-smile-tongue">': ':P',
            '<img src="/res/img/smiles_png/i-smile-wasntme.png" class="i-smile-wasntme">': '*WASNTME*',
            '<img src="/res/img/smiles_png/i-smile-party.png" class="i-smile-party">': '*PARTY*',
            '<img src="/res/img/smiles_png/i-smile-dull.png" class="i-smile-dull">': '*|-(*',
            '<img src="/res/img/smiles_png/i-smile-facepalm.png" class="i-smile-facepalm">': '*FACEPALM*',
            '<img src="/res/img/smiles_png/i-smile-love.png" class="i-smile-love">': '*LOVE*',
            '<img src="/res/img/smiles_png/i-smile-geek.png" class="i-smile-geek">': '*GEEK*',
            '<img src="/res/img/smiles_png/i-smile-like.png" class="i-smile-like">': '*LIKE*',
            '<img src="/res/img/smiles_png/i-smile-dislike.png" class="i-smile-dislike">': '*DISLIKE*',
            '<img src="/res/img/smiles_png/i-smile-crying.png" class="i-smile-crying">': ':(',
            '<img src="/res/img/smiles_png/i-smile-robot.png" class="i-smile-robot">': '*ROBOT*',
            // old smiles 
            '<img data="old" src="/res/img/smiles_png/i-smile-happy.png" class="i-smile-happy">': '*HAPPY*',
            '<img data="old" src="/res/img/smiles_png/i-smile-money.png" class="i-smile-money">': '$)',
            '<img data="old" src="/res/img/smiles_png/i-smile-security.png" class="i-smile-security">': '*SECURITY*',
            '<img data="old" src="/res/img/smiles_png/i-smile-sleepi.png" class="i-smile-sleepi">': '*SLEEPI*',
            '<img data="old" src="/res/img/smiles_png/i-smile-gay.png" class="i-smile-gay">': '*GAY*',
            '<img data="old" src="/res/img/smiles_png/i-smile-clown.png" class="i-smile-clown">': '*CLOWN*'
        },

        getEmotionsHTML: function () {
            var html = '';
            for (var emotion in Comments.emotionsToServer)
                html += emotion;
            return html;
        },

        hideNotifications: function () {
            if ($(Comments.notificationsList).is(':visible')) {
                $(Comments.notificationsList).slideUp('fast');
            }
        },

        renderNotifications: function () {

            var notifications = document.getElementById('communication-notifications');

            if (notifications) {
                var notificationsList = notifications.getElementsByClassName('c-notifications-list')[0];
                if (!Player.getCount('notifications') || !notificationsList || notificationsList.style.display !== 'block') {
                    console.error('renderNotifications');
                    R.push({
                        href: 'communication-notifications',
                        json: {}
                    });
                }
            }

        },

        do: {

            hideNotifications: function (event) {
                if (!DOM.up('.c-notifications', event.target))
                    Comments.hideNotifications();
            },

            showNotifications: function () {
                $(Comments.notificationsList).slideDown('fast');
                DOM.byId('communication-notifications').querySelector('form button[type="submit"]') ? Content.infiniteScrolling() : R.push('communication-notifications-list');
            },

            closeNotification: function (event) {

                event.preventDefault();
                event.stopPropagation();

                var notification = DOM.up('.c-notification', this),
                    obj = {
                        communication: {
                            notifications: {}
                        }
                    };

                obj.communication.notifications[notification.getAttribute('data-id')] = null;
                Player.decrement('local');
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

                Player.decrement('local', notifications.length);

                if (notifications.length) {
                    for (var i = 0; i < notifications.length; i++) {
                        obj.communication.notifications[notifications[i].getAttribute('data-id')] = null;
                    }

                    Cache.remove(obj);
                }

                R.push('communication-notifications');

            },

            deleteNotifications: function (event) {

                Form.send.call(this, {
                    action: '/communication/notifications',
                    method: 'DELETE',
                    after: function () {
                        Cache.init({'drop': {'communication': ['notifications']}});
                    }
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

                Player.decrement('local', notifications.length);

                for (var i = 0; i < notifications.length; i++) {
                    obj.communication.notifications[notifications[i].parentNode.getAttribute('data-id')] = null;
                }

                Cache.remove(obj);

                if (loadedComment) {
                    event.preventDefault();
                    event.stopPropagation();
                    Comments.hideNotifications();
                    DOM.scroll(loadedComment);
                    setTimeout(function () {
                        setTimeout(function () {
                            loadedComment.classList.remove('animated');
                            loadedComment.classList.remove('tada');
                        }, 2000);
                        loadedComment.classList.add('animated');
                        loadedComment.classList.add('tada');
                    }, 500);

                }


            },

            complainForm: function () {

                var comment = DOM.up('.comment-content', this),
                    id = comment.parentNode.getAttribute("data-id");

                // push new form
                R.push({
                    href: 'communication-comments-' + id + '-complain',
                    json: {id: id},
                    node: comment
                });

            },

            replyForm: function () {

                var comment = DOM.up('.comment-content', this),
                    node = DOM.up('.comment', comment),
                    commentsList = DOM.up('.render-list', node),
                    json = {
                        'user': {
                            "name": comment.getAttribute("data-user-name"),
                            'id': comment.getAttribute("data-user-id")
                        },
                        'comment_id': comment.getAttribute("data-comment-id"),
                        'object_id': comment.getAttribute('data-object-id')
                    };

                // delete other forms
                DOM.remove('.comment > form', commentsList);

                // up to comment block
                while (!node.classList.contains('comment') || node.classList.contains('answer'))
                    node = node.parentNode;

                // push new form
                R.push({
                    href: (json.object_id ? 'blog-post-view' : 'communication') + '-comments-replyform',
                    json: json,
                    node: node,
                    after: function(e){
                        // scroll form tot top in mobile devices
                        if(!Device.isMobile()){
                            return;
                        }
                        $(this.rendered).find('[contenteditable]').focus();
                        $('html, body').animate({
                            scrollTop: $(this.rendered).offset().top - 50
                        }, 'slow');
                    }
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

        after: {

            reply: function () {

                var form = this;

                if (form.elements['comment_id'].value) {
                    setTimeout(function () {
                        if (form.parentNode)
                            form.parentNode.removeChild(form);
                    }, form.getElementsByClassName('modal-message').length ? Form.getTimeout() : 0);
                }
                if ($('.thumb') != undefined) {
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
            }

        },

        validate: {
            reply: function (event) {
                return true;
            }
        },

        uploadImage: function () {
            if(Player.is.unauthorized){
                Content.popup.enter();
                return false;
            }
            // create form
            var form = DOM.up('form', this),
                $form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="image"/></form>'),
                $input = $form.find('input[type="file"]').damnUploader({
                    url: '/image',
                    fieldName: 'image',
                    dataType: 'json'
                });

            $input.off('du.add').on('du.add', function (e) {
                e.uploadItem.completeCallback = function (success, data, status) {
                    if (success) {
                        var span = document.createElement('span');
                        span.className = 'thumb';
                        span.style.background = 'url(' + Config.tempFilestorage + '/' + data.imageName + ') center';
                        span.style.height = '55px';
                        span.style['background-size'] = 'cover';
                        span.innerHTML = '<i class="i-x-slim"></i>';
                        form.querySelector('.message-form-actions .paste-image').insertBefore(span, null);

                        if(!form.elements['image']){
                            var input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'image';
                            form.appendChild(input);
                        }

                        form.elements['image'].value = data.imageName;

                    } else {
                        D.error.call(form, ['Code: ', status]);
                    }
                };
                e.uploadItem.progressCallback = function (perc) {
                };
                e.uploadItem.upload();
            });

            Comments.deleteImage.call(this);
            $form.find('input[type="file"]').click();
        },

        deleteImage: function () {

            var form = DOM.up('form', this);
            var image = form && form.elements['image'];

            if (image && image.value)
                Form.delete({
                    action: '/image',
                    data: {
                        image: image.value
                    },
                    after: function () {
                        DOM.remove('.thumb', form);
                        image.value = null;
                    }
                });
        },

        showSmiles: function () {

            $(this).closest('.message-form').find('.smiles').toggleClass('hidden');
            $(this).toggleClass('active');

            if(!$(this).hasClass('active')){   

                var result = $(this).closest('.message-form').find('div[contenteditable="true"]')[0];
                result.focus();
                placeCaretAtEnd(result);

                function placeCaretAtEnd(el) {
                    el.focus();
                    if (typeof window.getSelection != "undefined" && typeof document.createRange != "undefined") {
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
            }

        },

        chooseSmiles: function (e) {

            div = $(this).closest('.message-form-actions').prev();
            div.append($(this).clone());

            // var result = $(this).closest('.message-form').find('div[contenteditable="true"]')[0];
            // // result.focus();
            // placeCaretAtEnd(result);

            // function placeCaretAtEnd(el) {
            //     console.error(el);
            //     el.focus();
            //     if (typeof window.getSelection != "undefined" && typeof document.createRange != "undefined") {
            //         var range = document.createRange();
            //         range.selectNodeContents(el);
            //         range.collapse(false);
            //         var sel = window.getSelection();
            //         sel.removeAllRanges();
            //         sel.addRange(range);
            //     } else if (typeof document.body.createTextRange != "undefined") {
            //         var textRange = document.body.createTextRange();
            //         textRange.moveToElementText(el);
            //         textRange.collapse(false);
            //         textRange.select();
            //     }
            // }
        },

        pasteText: function (e) {

            var text = '',
                that = $(this);

            if (e.clipboardData)
                text = e.clipboardData.getData('text/plain');
            else if (window.clipboardData)
                text = window.clipboardData.getData('Text');
            else if (e.originalEvent.clipboardData)
                text = $('<div></div>').text(e.originalEvent.clipboardData.getData('text'));

            if (document.queryCommandSupported('insertText')) {
                document.execCommand('insertHTML', false, $(text).html());
                return false;
            } else { // IE > 7
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

        extractTextWithWhitespace: function (elems) {
            var ret = "", elem;

            for (var i = 0; elems[i]; i++) {
                elem = elems[i];
                // Get the text from text nodes and CDATA nodes
                if (elem.nodeType === 3 || elem.nodeType === 4) {
                    ret += elem.nodeValue + "\n";

                    // Traverse every thing else, except comment nodes
                } else if (elem.nodeType !== 8) {
                    ret += extractTextWithWhitespace2(elem.childNodes);
                }
            }

            return ret;
        },

        checkInput: function (e) {

            var key = e.keyCode,
                el = $(this)[0];
            if (key === 13) {

                e.preventDefault(); // Prevent the <div /> creation.
                var br = $('<br><br>');
                $(this).append(br); // Add the <br at the end
                console.log(br.next(br));
                // br.next(br).remove();
                if (typeof window.getSelection != "undefined" && typeof document.createRange != "undefined") {
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
                // if ($('.message-form-area br').next('br').next('br')) {
                //    $('.message-form-area br').next('br').next('br').remove(); 
                // }


            }

        },

        getFullPicture: function (event) {
            event.preventDefault();

            var co = {
                'url': this.getAttribute('data-href') || '',
                'element': document.querySelector("#FullPictureWrapper") || '',
                'html': '<div class="inner">\
                            <i class="i-x-slim" onclick="this.parentNode.parentNode.removeAttribute(\'class\')" ></i>\
                            <div class="content"></div>\
                         </div>'
            };

            if (!co.url) return;

            if (!co.element) {
                co.element = document.createElement('div');
                co.element.id = 'FullPictureWrapper';
                co.element.setAttribute("onclick", "this.removeAttribute('class')");
                co.element.innerHTML = co.html;

                document.body.appendChild(co.element);
            } else {
                co.element.removeAttribute("class");
            }

            co.img = document.createElement('img');
            co.img.src = co.url;

            co.element.querySelector('.content').innerHTML = '';
            co.element.querySelector('.content').appendChild(co.img);

            co.img.onload = function () {
                co.element.setAttribute("class", "load");
            }

            return false;
        },

        smilePost: function (form) {

            if (!$('.smiles').hasClass('hidden'))
                $('.smiles').addClass('hidden');

            for (var emotion in Comments.emotionsToServer) {
                form.data.text = form.data.text
                    .replaceAll(emotion, Comments.emotionsToServer[emotion]);
            }

            form.data.text = form.data.text
                .replace(/(<br\/*>(\s*))+/ig, '\n')
                .replace(/&nbsp;/g, '');

            return form;

        },

        submit: function (form) {
            Comments.smilePost(form);
            return form;
        },

        complain: function () {
            
            $(this).closest("form").submit();
            
            // return true;
        }

    }

})();
