function fbPost(postObject) {
    getFbLogin(function(postObject){
        FB.api('/me/feed', 'post', postObject, function(r) {
            if (r && !r.error) {
                socialSuccessPost(function(data) {
                    $('.sposts-count').text(data.res.postsCount);
                }, function(data) {}, function() {});
            }
        });   
    }, postObject);
}
function vkPost(postObject) {
    getVkLogin(function(post) {
        // upload post photo
        VK.Api.call('photos.getWallUploadServer', {}, function(wpu) {
            if (wpu.response && wpu.response.upload_url) {
                uploadVkPhoto(wpu.response.upload_url, function(photoData) {
                    VK.Api.call('photos.saveWallPhoto', photoData, function(wps) {
                        var photo = wps.response.shift();
                        VK.Api.call('wall.post', {
                            message: post.message + "\n" + post.link,
                            attachments: photo.id
                        }, function(pr) {      
                            console.log(pr);                          
                            if (pr && !pr.error) {
                                socialSuccessPost(function(data) {
                                    $('.sposts-count').text(data.res.postsCount);
                                }, function(data) {}, function() {});    
                            }                                
                        });
                    });
                }, function() {}, function() {});
            }
        }); 
    }, postObject);
}
function getVkLogin(callback, callbackInput) {
    VK.Auth.getLoginStatus(function(lsr) {
        if (lsr.status == 'connected') {
            callback.call($(this), callbackInput);
        } else {
            VK.Auth.login(function(r) {
                if (r.status == 'connected') {
                    callback.call($(this), callbackInput);
                }
            }, VK.access.PHOTOS);
        }
    })
}
function getFbLogin(callback, callbackInput) {
    FB.getLoginStatus(function(lsr) {
        if (lsr.status == 'connected') {
            callback.call($(this), callbackInput);
        } else {
            FB.login(function(r) {
                if (r.status == 'connected') {
                    callback.call($(this), callbackInput);
                }
            }, {scope: 'publish_actions'});
        }
    })
}
function gpPost() {

}