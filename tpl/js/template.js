window.getTpl = {

    'init': doT.template(
        "{{ for(var prop in it) { }}{{=prop}}{{window['append'+prop](it[prop]);}}{{ } }}"
    ),

    'comments': {

        'comment': doT.template(
            "{{~it:review}}" +
            "<div data-id={{=review.id}} class='rv-item{{? review.answer }} rv-answer{{?}}'>" +
            "<div class='rv-i-avtr'><img src='{{?review.playerAvatar}}/filestorage/avatars/{{=Math.ceil(review.playerId/100)}}/{{=review.playerAvatar}}{{??}}/tpl/img/default.jpg{{?}}'></div>" +
            "<div class='rv-i-tl'><span class='rv-i-pl'>{{=review.playerName}}</span> • <span class='rv-i-dt'>{{=review.date}}</span> <span class='rv-i-ans'>{{=getText('button-answer')}}</span><!--span class='icon-like'></span><span class='icon-dislike'></span--></div>" +
            "<div class='rv-i-txt'>{{=review.text}}</div>" +
            "{{? review.image}}<div class='rv-i-img'><img src='/filestorage/reviews/{{=review.image}}'></div>{{?}}" +
            "</div>" +
            "{{~}}"
        ),

        'answer': doT.template(
            "<div class='rv-ans-tmpl'><div class='rv-form'><div contenteditable></div><div class='btn-ans'>{{=getText('button-answer')}}</div></div><div class='rv-sc'>{{=getText('message-review-approved')}}</div></div>"
        )
    }
};
