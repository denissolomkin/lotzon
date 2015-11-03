(function () {

    Blog = {

        init: function(){
            R.push({
                'box': $('.content-box-content:visible'),
                'template': 'blog-posts',
                'url': false
            })
        },


        loadPostData: function(){

            R.push({
                'href': U.href+'/comments',
                'url': false
            })

            R.push({
                'href': U.href+'/posts',
                'url': false
            })

        }
    }

})();