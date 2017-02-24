(function () {

    Blog = {

        init: function (data) {

            $('.social-likes').socialLikes();
            Blog.videoFix();
        },
        videoFix: function(){
        	$('#blog-post-view iframe[allowfullscreen]').wrap( "<div class='video_wrapper'></div>" );
        }

    }

})();