(function () {

    Banner = {

        load: function f(href) {
            if (typeof href == 'object')
                while (href.length)
                    f(href.shift());
            else
                Form.get({
                    action: href,
                    after: function (data) {
                        $('#' + href).empty().append(data.json.res);
                        
                        // add padding top body-top if branding
                        if (href === "banner-desktop-brand" && data.json.res) {
                            var wrapper= document.createElement('div');
                                wrapper.innerHTML= data.json.res;
                                // wrapper.innerHTML= '<a target="_blank" data-margin="300" style="background-image:url(http://stag.lotzon.com/tpl/img/baners/branding_sz.jpg);" href="http://shopszon.com/?ref=614"></a>';
                            var m = wrapper.querySelector('a[data-margin]');
                                m = m && m.getAttribute('data-margin') || 0;

                            $('body').css({'padding-top': m+'px'});
                        }

                    },
                    data: {
                        href: window.location.pathname,
                        page: /\w+/gi.test(document.location.pathname) && document.location.pathname.match(/\w+/gi)[0]
                    }
                });
        },

        update: function () {
            // var tst = 0;
            // Banner.t && clearInterval(Banner.t);
            // Banner.t = setInterval(function(){
            //     tst++;
            //     console.debug(tst);
            // }, 1000)
            // up after t
            var t = 240000;
            Banner.adTimer && clearTimeout(Banner.adTimer);
            Banner.adTimer = null;

            if(!Banner.adTimer){
                Banner.adTimer = setTimeout(function(){
                    Banner.update();
                },t)
            }

            if (window.location.pathname !== '/') {
                Device.isMobile()
                    ? Banner.load('banner-tablet-top')
                    : Banner.load(['banner-desktop-right', 'banner-desktop-teaser', 'banner-desktop-top']);
            }
            // console.debug("> banners update");
        },

        scroll: function (event) {

            if (Device.isMobile())
                return;

            var teaser = document.getElementById('banner-desktop-teaser'),
                height = document.getElementsByTagName('header')[0].getBoundingClientRect().height;

            if (teaser) {
                if (document.getElementById('banner-desktop-right').getBoundingClientRect().bottom < height) {
                    if (teaser.style.position !== 'fixed') {
                        teaser.style.position = 'fixed';
                        teaser.style.top = height + 'px';
                    }
                } else if (teaser.style.position === 'fixed') {
                    teaser.style.position = 'relative';
                    teaser.style.top = '0px';
                }

            }
        },

        loadBranding: function () {

            if (Device.isMobile())
                return;
            Banner.load('banner-desktop-brand');

        },
        loadBlock: function (target) {

            // if (Device.isMobile())
            //     return;

            var parent = $(target).parent();
            $(target).remove();
            var block = parent.html();
            parent.html(block);


        },

        moment: function (data) {
            if (Device.isMobile())
                return;

            if (data.json.hasOwnProperty('block') && data.json.block) {
                var node = data.hasOwnProperty('node') && data.node.getElementsByClassName('ad')[0];
                if (node) {
                    $(node).empty().append(data.json.block);
                }
            }
        }
    }

})();