(function () {

    Blog = {

        init: function(){
            R.push({
                'box': $('.content-box-content:visible'),
                'template': 'blog-posts'
            })
        },

        loadPostData: function(options){
            R.push(options.href+'/comments');
            R.push(options.href+'/similar');
        }
    }

})();