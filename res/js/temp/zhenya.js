$(document).ready(function () {

  
//получает результаты розыгрыша
  function getLotteryData(successFunction, failFunction, errorFunction) {

    $.ajax({
      url: "/res/lastLottery?" + $.now(),
      method: 'GET',
      async: true,
      dataType: 'json',
      success: function (data) {
        if (data.i) {
          successFunction.call($(this), data);
        } else {
          failFunction.call($(this));
        }
      },
      error: function () {
        errorFunction.call($(this));
      }
    });
  }

//запускает анимацию
  runAnimation = function () {
      R.render({
        'template': 'lottery-animation-process',
        'json': {},
        'url': false,
        'callback': function (html) {
          $('body').append(html);
          proccessResult();
        }
      });
  }

//window.setTimeout(runAnimation,100);

  proccessResult = function () {
    
    getLotteryData(function (data) {


      $('#game-process .g-oc-b .goc_li-nb').removeClass('goc-nb-act');
      var tickets = [];
      if ($('.tb-slide').length > 0)
        $('.tb-slide').each(function (index, el) {
          var ticket = [];
          $(el).find('.loto-tl_li.select').each(function (i, val) {
            ticket.push($(val).text());
          });
          if (ticket.length == 6)
            tickets.push(ticket);
        });
      else if ($('.yr-tb').length > 0)
        $('.yr-tb .yr-tt .yr-tt-tr').each(function (index, el) {
          var ticket = [];
          $(el).find('.yr-tt-tr_li').each(function (i, val) {
            if (parseInt($(val).text()) > 0)
              ticket.push($(val).text());
          });
          if (ticket.length == 6)
            tickets.push(ticket);
        });

      if (!tickets.length)
        $("#game-itself").hide();
      else
        data.tickets = tickets;

      var ticketsHtml = '';
      for (var i = 0; i < 5; ++i) {
        ticketsHtml += '<li class="yr-tt"><div class="yr-tt-tn">Билет #' + (i + 1) + '</div><ul class="yr-tt-tr">';
        if (tickets[i]) {
          $(tickets[i]).each(function (id, num) {
            ticketsHtml += '<li class="yr-tt-tr_li" data-num="' + num + '">' + num + '</li>';
          });
        } else {
          ticketsHtml += "<li class='null'>Не заполнен</li>"
        }
        ticketsHtml += '</ul></li>';
      }

      $("#game-process").find('.yr-tb').html(ticketsHtml);


      if (1 || data.i == parseInt($('._section.profile-history .ht-bk .lot-container').first().data('lotid')) + 1) {

        var ball = '';
        var lotInterval;
        var combination = $(data.c).get();
        var lotAnimation = function () {
          ball = combination.shift();
          var spn = $("#game-process .g-oc_span.unfilled:first");

          spn.text(ball);
          var li = spn.parents('.g-oc_li');
          li.find('.goc_li-nb').addClass('goc-nb-act');
          spn.removeClass('unfilled');

          window.setTimeout(function () {
            $("#game-process").find('li[data-num="' + ball + '"]').addClass('won')
          }, 1000);

          if (!combination.length) {
            window.clearInterval(lotInterval);
            window.setTimeout(function () {
              if ($("#game-process").find('li.won').length) {
                showWinPopup(data);
              } else {
                showFailPopup(data);
              }
            }, 2000);
          }
        }
        window.setTimeout(function () {
          lotAnimation();
          lotInterval = window.setInterval(lotAnimation, 5000);
        }, 2000);


      } else if (!data || data.i == parseInt($('._section.profile-history .ht-bk .lot-container').first().data('lotid'))) {
        window.setTimeout(proccessResult, (Math.floor(Math.random() * 5) + 1) * 1000);
      } else {
        location.reload();
      }


    }, function () {
      window.setTimeout(proccessResult, (Math.floor(Math.random() * 5) + 1) * 1000);
    }, function () {
      window.setTimeout(proccessResult, (Math.floor(Math.random() * 5) + 1) * 1000);
    });
  }


})