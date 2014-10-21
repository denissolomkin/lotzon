$(function(){
    $('.pi-inp-bk input').on('focus', function(){
        $(this).closest('.pi-inp-bk').addClass('focus');
    });
    $('.pi-inp-bk input').on('blur', function(){
        $(this).closest('.pi-inp-bk').removeClass('focus');
    });

    $('.submit').on('click', function(){
        var val = $('.pi-inp-bk input').val();
        if($.trim(val).length){
            $('.form').addClass('done');
        }else{
            $('.form').addClass('error');
        }
    });
});
