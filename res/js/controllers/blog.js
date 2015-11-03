(function () {

    Blog = {

        init: function(){
            R.push({
                'box': $('.content-box-content:visible'),
                'template': 'blog-posts',
                'url': false
            })
        },


        loadComments: function(options){
            R.push({
                'box': $('.content-box-content:visible'),
                'template': 'blog-posts',
                'url': false
            })
        }
    }

})();