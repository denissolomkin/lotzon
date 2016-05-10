
(function () {

    /* ========================================================= */
    //                         BONUSES
    /* ========================================================= */

    // POPUP BANNER ---------------------- //

    $(document).on('click','.bonus-banner-view-btn', function () {

        var $popupBanner = $('.popup-banner');

        $popupBanner.css({
            top: ($(window).outerHeight() / 2 - $popupBanner.outerHeight() / 2) + 'px',
            left: ($(window).outerWidth() / 2 - $popupBanner.outerWidth() / 2) + 'px'
        }).fadeIn('fast');
    });


    //  --------------------------------- //

    /* ========================================================= */
    /* ========================================================= */
    
    Bonuses = {
        init: function (data) {
            $('.ae-social').socialLikes({
                url: 'https://lotzon.com/?ref=' + Player.id,
                title: 'LOTZON. Выигрывает каждый!',
                counters: false,
                singleTitle: 'LOTZON. Выигрывает каждый!',
                data: {
                    media: 'https://lotzon.com/res/img/lotzon_soc.jpg'
                }
            });
        },
        bufferCopy: function(e) {

            var el = $(e.target);
            var target = $("#"+el.attr('data-to'));
            var name = '';
            var txt = el.text();
            console.debug(target);
            if(target){
                name = target.attr('name');
                target.select();
                document.execCommand('copy');
            
                el.text( el.attr('data-after') ).addClass('ready');
                setTimeout(function(){
                    el.text( txt ).removeClass('ready');
                }, 4000)
            }else{
                alert("Не получилось скопировать, попробуйте вручную..");
            }
        },
        showBanner: function () {  
            $('.bonus-banner-view-item').removeClass('active');
            $('.show-banner > div').hide();
            $('.bonus-share-banner-view .close').show();
            bannerSize = $(this).attr("id");
            var thisBanner = $(this);
            $('.show-banner > div').each(function(){
                console.log($(this).hasClass(bannerSize),'$(this).text(),bannerSize');
                if ($(this).hasClass(bannerSize)) {

                    $(this).css('display', 'block');
                    thisBanner.addClass('active');
                }        
            });
        },
        hideBanner: function () {
            $('.bonus-banner-view-item').removeClass('active');
            $('.show-banner > div').hide();
            $(this).hide();
        },
        copyBanner: function () {  
            var copyLink = $(this).closest('.banner-copy').find('input').text();
            console.log('$(this).closest(".banner-copy").find("input").text()',copyLink);
           
            var clip = new ZeroClipboard.Client(); 
             ZeroClipboard.setMoviePath('res/img/ZeroClipboard.swf');
            clip.setText(''); 
            clip.addEventListener( 'complete', function(client, text) { 
                alert("Текст уже в буфере: " + text ); 
            } );
            
            clip.addEventListener( 'mouseDown', function(client) {  
                clip.setText( document.getElementById('source').innerHTML ); 
            } );

            clip.glue('banner-copy-btn','banner-copy'); 
        },


        downloadFile : function(sUrl) {
         
            //If in Chrome or Safari - download via virtual link click
            if (window.downloadFile.isChrome || window.downloadFile.isSafari) {
                //Creating new link node.
                var link = document.createElement('a');
                link.href = sUrl;
         
                if (link.download !== undefined){
                    //Set HTML5 download attribute. This will prevent file from opening if supported.
                    var fileName = sUrl.substring(sUrl.lastIndexOf('/') + 1, sUrl.length);
                    link.download = fileName;
                }
         
                //Dispatching click event.
                if (document.createEvent) {
                    var e = document.createEvent('MouseEvents');
                    e.initEvent('click' ,true ,true);
                    link.dispatchEvent(e);
                    return true;
                }
            }
         
            // Force file download (whether supported by server).
            var query = '?download';
         
            window.open(sUrl + query, '_self');

            window.downloadFile.isChrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
            window.downloadFile.isSafari = navigator.userAgent.toLowerCase().indexOf('safari') > -1;
        }  


        

    }

})();


