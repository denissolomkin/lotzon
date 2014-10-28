$(function(){
    $('.pi-inp-bk input').on('focus', function(){
        $(this).closest('.pi-inp-bk').addClass('focus');
    });
    $('.pi-inp-bk input').on('blur', function(){
        $(this).closest('.pi-inp-bk').removeClass('focus');
    });

    $('.submit').on('click', function(){
        $.ajax({
            url: "/trailer",
            method: 'POST',
            data: {
                email: $.trim($('input[name="email"]').val()),
            },
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    $('.form').addClass('done');       
                    $('.txt').text('Спасибо! Вскоре, мы оповестим Вас об открытии. До встречи!');
                } else {
                    $('.form').addClass('error');

                    $('.txt').text(data.message);
                    if (data.message == 'INVALID_EMAIL') {
                        $('.txt').text('Неверный формат email!');
                    }
                    if (data.message == 'ALREADY_SUBSCRIBED') {
                        $('.txt').text('Упс! Не волнуйтесь, на этот email будет выслано письмо после открытия');
                    } 
                    window.setTimeout(function() {
                        $('.form').removeClass('error');       
                    }, 3000);

                }
            }, 
            error: function() {}
        });
    });
});
