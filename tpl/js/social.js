function fbPost(postObject) {
    FB.getLoginStatus(function(r) {
        FB.login(function(){
            FB.api('/me/feed', 'post', postObject, function(r) {
                console.log(r);
            });   
        }, {scope: 'publish_actions'});        
    });
}