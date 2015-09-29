$(function () {

    /* ========================================================= */
    //                        SYSTEM FUNCTIONS
    /* ========================================================= */

    $.ajaxSetup({
        error: function (xhr, status, message) {
            throw('An AJAX error occured: ' + status + "\nError: " + message);
        }
    });

    window.onerror = function (message, url, line, col, error) {

        /* err(message, url);
         return; */

        D.log([message, url, line],'error');

        if ($Button) {
            $Button.data('disabled', false);
            $Button = null;
        }

        $(".error").remove();
        $(".loading").remove();

        $Error = $('<div class="error"><div><span>' + M.i18n('title-error') + '</span>' + message + '</div></div>');


        if (!$Box)
            $Box = $('.content-box').length == 1 ? $('.content-box') : $('.content-top');

        $Box.append($Error);

        if ($Errors = $(".error"))
            setTimeout(function () {
                $Errors.fadeOut(500);
                setTimeout(function () {
                    $Errors.remove();
                }, 500)
            }, 1000);

        R.empty();

        return true;
    }

    /* ========================================================= */
    //                        ENGINE
    /* ========================================================= */

    D = {

        "Enable": true,

        "log": function (log, type) {

            type = type || 'log';
            var d = new Date();

            if (D.Enable) {

                var output = '';

                if (typeof log == 'object' && log.length) {

                    $.each(log, function (index, obj) {
                        if (obj)
                            output += JSON.stringify(obj).replace(/"/g, "").substring(0, "type"=="error"?100:40) + ' ';
                    });

                } else {
                    output = JSON.stringify(log).replace(/"/g, "").substring(0, "type"=="error"?100:40);
                }


                console[type](d.toLocaleTimeString('ru-RU') + ' ' + output);
            }
        },

        "error": function (message, trace) {

            if ($Button) {
                $Button.data('disabled', false);
                $Button = null;
            }

            $(".error").remove();
            $(".loading").remove();

            $Error = $('<div class="error"><div><span>' + M.i18n('title-error') + '</span>' + message + '</div></div>');


            if (!$Box)
                $Box = $('.content-box').length == 1 ? $('.content-box') : $('.content-top');

            $Box.append($Error);

            if ($Errors = $(".error"))
                setTimeout(function () {
                    $Errors.fadeOut(500);
                    setTimeout(function () {
                        $Errors.remove();
                    }, 500)
                }, 1000);

            R.empty();
            return false;
        }
    };

    R = {

        "Cache": {},
        "Templates": {},
        "Render": [],
        "Path": [],
        "IsRendering": false,

        "init": function () {

            D.log(['init']);
            R.empty();

            R.Path = window.location.pathname.split('/');
            R.Path[1] = R.Path[1] || 'blog';
            $('[href="/' + R.Path[1] + '"]').click();

        },

        "render": function (options) {

            try {
                if (!options) options = {};

                if (!options.template)
                    options.template = U.Parse.Undo($Template || $Href || $Tab.attr('href'));
                if (!options.href)
                    options.href = U.Parse.Undo($Href || options.template);
                if (!options.json)
                    options.json = $JSON || false;
                if (!options.callback)
                    options.callback = $Callback;

                R.empty('soft');

                D.log(['render.push:', options.template, options.href, options.json], 'info');

                R.Render.push({
                    'options': {
                        'box': $Box,
                        'tab': $Tab,
                        'callback': options.callback,
                        "this": options.template
                    },
                    'url': options.url,
                    'template': options.template,
                    'href': options.href,
                    'json': options.json
                });

                if (!R.IsRendering)
                    R.rendering();

            } catch (error) {
                throw(error);
            }
        },

        "rendering": function () {

            try {
                while (R.Render.length) {

                    R.IsRendering = true;

                    var render = R.Render.shift();

                    R.loading(render.options);

                    D.log(['rendering.run:', render.template, render.href, render.json], 'info');

                    if (render.url !== false)
                        U.Update(typeof render.href != 'object' ? render.href : render.template);

                    if (typeof render.json == 'object') {
                        D.log(['JSON from Object:', render.json]);
                        R.renderTMPL(render.template, render.json, render.options);
                    } else {
                        R.renderJSON(render.href, render.template, render.options);
                    }
                }

                R.stop();

            } catch (error) {
                throw(error);
            }
        },

        "renderJSON": function (href, template, options) {

            try {
                D.log(['renderJSON:', href]);

                var json = null;

                if (json = R.cache(href)) {

                    D.log(['JSON from Cache:', json]);
                    R.renderTMPL(template, json, options);

                } else {

                    $.getJSON(U.Generate.Json(href), function (response) {
                        if (response.status == 1) {

                            json = R.cache(href, response.res);
                            D.log(['JSON from AJAX:', json], 'warn');
                            R.renderTMPL(template, json, options);

                        } else {

                            D.error(response.message);

                        }
                    });
                }

            } catch (error) {
                throw(error);
            }
        },

        "renderTMPL": function (template, json, options) {

            try {
                template = U.Parse.Tmpl(template);
                D.log(['renderTMPL:', template]);

                if ($('.template.' + template).length) {

                    D.log(['TMPL already in DOM', template]);
                    R.renderHTML(template, json, options);

                } else if (R.Templates[template]) {

                    template = R.Templates[template];
                    D.log(['TMPL from Cache', template]);
                    R.renderHTML(template, json, options);

                } else if ($('#tmpl-' + template).length) {

                    template = R.Templates[template] = $('#tmpl-' + template).html();
                    D.log(['TMPL from HTML:', template]);
                    R.renderHTML(template, json, options);

                } else {
                    $.get(U.Generate.Tmpl(template), function (data) {

                        if (!$(data).attr('class')) {
                            throw("Format Template Error");
                        } else {
                            template = R.Templates[template] = data;
                            D.log(['TMPL from AJAX:', template], 'warn');
                            R.renderHTML(template, json, options);
                        }

                    });
                }
            } catch (error) {
                throw(error);
            }
        },

        "renderHTML": function (template, json, options) {

            try {

                D.log(['renderHTML:', template, json]);
                var rendered = null;

                if (typeof json != 'object') {

                    D.log('Rendered with HTML');
                    rendered = $($('.template.' + template)[0].outerHTML).html(json);

                } else {

                    D.log('Rendered with Template');
                    Mustache.parse(template);   // optional, speeds up future uses
                    rendered = Mustache.render(template, $.extend({"i18n": M.i18n}, json));

                }

                R.inputHTML(rendered, options);
            } catch (error) {
                throw(error);
            }
        },

        "inputHTML": function (rendered, options) {

            try {

                D.log(['inputHTML into:', (typeof options.box == 'object' ? options.box.attr('class') : options.box)]);
                var findClass = '.' + $(rendered).attr('class').replace(/ /g, '.');

                if (options.box) {

                    $(' > div', options.box).hide();

                    if ($(findClass, options.box).length) {

                        $(findClass, options.box).html($(rendered).html()).show();

                    } else {

                        options.box.append(rendered).find(findClass).hide().show();

                    }

                }

                if (options.callback) {
                    D.log(['callback']);
                    options.callback(rendered, findClass);
                }

                /* tickets functionality */
                if ($('.ticket-items', $(rendered)).length && !$('.ticket-items li.active').length)
                    renderTicket();

                /* parent box functionality after rendering */
                if (options.box) {

                    /* if new box has tabs */
                    if ($($Tabs, options.box).filter(":visible").length) {

                        /* click on unactive tab */

                        if (!$($Tabs, options.box).filter(".active:visible").length) {
                            D.log(['clickTab:', $($Tabs, options.box).not(".active").filter(":visible").first().attr('href')]);
                            $($Tabs, options.box).not(".active").filter(":visible").first().click();
                        }

                    }

                    /* tab functionality after click on tab */
                    if (options.tab) {

                        $('.active', options.tab.parent().parent()).removeClass('active');

                        if ($($Cats, options.tab.parents('.content-box')).filter(":visible").length) {
                            D.log(['clickCat:', $($Cats, options.box).first().attr('href')]);
                            $($Cats, options.tab.parents('.content-box')).first().click();
                        }

                        options.tab.addClass('active');
                    }


                }

                R.empty();

            } catch (error) {
                throw(error);
            }
        },

        "loading": function (options) {
            if (options.box)
                $('.loading', options.box).length ? $('.loading', options.box).show() : options.box.append('<div class="loading"><div></div></div>');
        },

        "cache": function (key, data) {

            if (key && R.Cache[key]) {
                return R.Cache[key];
            } else if (data && !data['nocache']) {
                return R.Cache[key] = data;
            } else if (data) {
                return data;
            } else
                return false;
        },

        "empty": function (mode) {

            D.log('empty.' + (mode ? mode : 'hard'));
            $Template = $Href = $JSON = $Callback = null;
            if (!mode) $Tab = $Box = $Button = null;

        },

        "stop": function () {

            D.log('rendering.stop');
            R.IsRendering = false;
            $(".loading").remove();

        }
    };


    M = {

        "i18n": function (key) {
            return key ? (M.Texts[key] ? M.Texts[key] : key) : (function (key) {
                return M.i18n(key);
            });
        },

        "Texts": {
            "title-ticket": "Билет",
            "message-autocomplete": "АВТОЗАПОЛНЕНИЕ",
            "message-done-and-approved": "<b>ПОДТВЕРЖДЕН И ПРИНЯТ К РОЗЫГРЫШУ</b>",
            "message-numbers-yet": "ЕЩЕ <b></b> НОМЕРОВ",
            "message-favorite": "<b>ЛЮБИМАЯ КОМБИНАЦИЯ</b><span>настраивается в кабинете</span>",
            "button-add-ticket": "Подтвердить",
            "title-prizes-draw": "Розыгрыш призов",
            "title-limited-quantity": "Ограниченное количество",
            "title-pieces": "шт.",
            "title-points": "баллов",
            "title-error": "Ошибка"
        }
    };


    U = {

        "Path": {

            "Json": "/res/json/",
            "Ajax": "/res/json/",
            "Tmpl": "/res/tmpl/"

        },

        "Generate": {
            "Ajax": function (url) {
                return U.Path.Ajax + U.Parse.Url(url);
            },

            "Json": function (url) {
                return U.Path.Json + U.Parse.Url(U.Parse.Json(url));
            },

            "Tmpl": function (url) {
                return U.Path.Tmpl + U.Parse.Url(url) + '.html';
            }
        },

        "Parse": {
            "Url": function (url) {
                return url.replace(/-/g, '/');
            },

            "Tmpl": function (url) {
                return url.replace(/-\d+/g, '-view');
            },

            "Json": function (url) {
                return url.replace(/-view/g, '');
            },

            "Undo": function (url) {
                if (typeof url == 'object') {
                    return url;
                } else {
                    url = url.replace(/^\//, "");
                    return url.replace(/\//g, '-');
                }
            }
        },

        "Update": function (url) {
            if (url) {
                D.log(['updateURL:', url]);
                var stateObj = {foo: "bar"};
                history.pushState(stateObj, "page 2", '/' + U.Parse.Url(url));
            }
        },

        "isAnchor": function (url) {
            return (url.indexOf('#') == 0);
        }

    };


    /* ========================================================= */
    /* ========================================================= */


    /* ========================================================= */
    //                        TAB AND CAT SWITCH
    /* ========================================================= */

    // variables
    $Tabs = '.content-box-tabs a';
    $Cats = '.content-box-cat a';

    // handlers
    $(document).on('click', $Tabs, switchTab);
    $(document).on('click', $Cats, switchCat);
    $(document).on('click', 'a', loadBlock);
    $(document).on('click', 'div.back', backBlock);
    $("header a").on('click', loadPage);

    R.init();

    // functions
    function loadPage(event) {

        if (!event.isPropagationStopped()) {

            event.stopPropagation();
            $Box = $('.content-top');
            $Tab = $(this);

            R.render({
                "json": {}
            });

        }
        return false;

    }

    function loadBlock(event) {

        if (!event.isPropagationStopped()) {

            event.stopPropagation();
            $Box = $(this).parents('.content-main');
            $Tab = $(this);
            D.log(['loadBlock:', $Tab.attr('href')]);

            R.render({
                "callback": function (rendered, findClass) {
                    $(findClass).addClass('slideInRight');
                }
            });

        }
        return false;

    }

    function backBlock(event) {

        if (!event.isPropagationStopped()) {

            event.stopPropagation();
            $Box = $(this).parents('.content-box');
            $Box.prev().addClass('slideInLeft').show().next().remove();
            $Tab = $(this);

            D.log(['backBlock:', $Tab.attr('href')]);
            $($Tabs + '.active').click();
        }

        return false;

    }

    function switchTab(event) {

        if (!event.isPropagationStopped()) {

            event.stopPropagation();
            $Box = $(this).parents('.content-box').find('.content-box-content');
            $Tab = $(this);

            D.log(['switchTab:', $Tab.attr('href')]);

            if (U.isAnchor($Tab.attr('href'))) {

                $($Tabs, $Tab.parents('.content-box-header')).removeClass('active');
                $(' > div', $Box).hide();
                $('.content-box-item.' + $Href, $Box).show();
                $Tab.addClass('active');

            } else {
                R.render();
            }
        }

        return false;
    }

    function switchCat(event) {

        if (!event.isPropagationStopped()) {

            event.stopPropagation();
            $Cat = $(this);
            D.log(['switchCat:', $Cat.attr('href')]);

            // with animation
            if ($($Cats, $Box).filter('.active').length) {

                $($Cats, $Box).removeClass('active');
                $('.content-box-item-content > div', $Box).fadeOut(200);
                setTimeout(function () {
                    $('.content-box-item-content > div.category-' + $Cat.data('category'), $Box).fadeIn(200);
                }, 200);

                // without animation
            } else {
                $('.content-box-item-content > div', $Box).hide();
                $('.content-box-item-content > div.category-' + $Cat.data('category'), $Box).show();
            }

            $Cat.addClass('active');

        }

        return false;

    }

    /* ========================================================= */
    /* ========================================================= */



    /* ========================================================= */
    //                        TICKETS
    /* ========================================================= */

    $TicketTabs = '.ticket-tabs li';
    $Tickets = {

        "selectedBalls": 6,
        "totalBalls": 49,
        "totalTickets": 8,

        "favorite": [],
        "balls": {
            "1": [1, 2, 3, 4, 5, 6],
            "2": [31, 22, 13, 44, 25, 9],
            "3": [3, 2, 14, 34, 15, 19],
            "4": [1, 2, 3, 4, 5, 6],
            "5": [31, 22, 13, 44, 25, 9],
            "6": [3, 2, 14, 34, 15, 19],
            "7": [31, 22, 13, 44, 25, 9],
        },

        "ballsHTML": function () {
            var html = '';
            for (i = 1; i <= this.totalBalls; i++) {

                html += "<li class='ball-number number-" + i + ($.inArray(i, this.balls[$($TicketTabs).filter('.active').data('ticket')]) == -1 ? '' : ' select') + "'>" + i + "</li>";
            }
            return html;
        },

        "tabsHTML": function () {
            var html = '';
            for (i = 1; i <= this.totalTickets; i++) {
                html += "<li data-ticket='" + i + "' class='" + (this.balls && this.balls[i] ? 'done' : '') + "'><span>" + M.i18n('title-ticket') + " </span>#" + i + "</li>";
            }
            return html;
        },

        "isDone": function () {
            return (this.balls && this.balls[$($TicketTabs).filter('.active').data('ticket')] && this.balls[$($TicketTabs).filter('.active').data('ticket')].length && this.balls[$($TicketTabs).filter('.active').data('ticket')].length == this.selectedBalls);
        },

        "isComplete": function () {
            return (this.balls && Object.keys(this.balls).length == this.totalTickets);
        },


        "completeHTML": function () {

            var html = '';

            $.each(this.balls, function (index, ticket) {
                html += "<ul class='ticket-result'><li class='ticket-number-result'><span>БИЛЕТ</span> #" + index + "";
                $.each(ticket, function (number, ball) {
                    html += "<li class='ball-number-result'>" + ball + "</li>";
                });
                html += "</ul>";
            });

            return html;
        }

    };

    $(document).on('click', $TicketTabs, switchTicket);
    $(document).on('click', '.ticket-item .add-ticket', addTicket);

    function addTicket() {

        if ($(this).hasClass('on') && !$(this).data('disabled')) {

            $Box = $('.ticket-items');
            $Button = $(this);
            $Button.data('disabled', true);

            var ticket = {
                "combination": [],
                "tickNum": null
            };

            $('.ticket-balls li.select').each(function (id, li) {
                ticket.combination.push(parseInt($(li).text()));
            });

            ticket.tickNum = $($TicketTabs + '.active').data('ticket');

            $.ajax({
                url: U.Generate.Ajax('ticket'),
                type: 'post',
                data: ticket,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        $Tickets.balls[ticket.tickNum] = ticket.combination;
                        $($TicketTabs + '.active').addClass('done');
                        switchTicket();
                    } else {
                        throw(data.message);
                    }
                }
            });

        }
    }

    function renderTicket() {

        D.log('renderTicket');

        if ($Tickets.isComplete()) {

            $Box = $('.ticket-items').parent();

            R.render({
                "template": 'ticket-complete',
                "json": $Tickets,
                "url": false
            });

        } else {

            $Box = $('.ticket-items');

            R.render({
                "template": 'ticket-tabs',
                "json": $Tickets,
                "url": false,
                "callback": function () {
                    $($TicketTabs).not('.done').first().click();
                }
            });

        }

    }

    function switchTicket() {

        D.log('switchTicket');

        if ($Tickets.isComplete()) {

            $Box = $('.ticket-items').parent();

            R.render({
                "template": 'ticket-complete',
                "json": $Tickets,
                "url": false
            });

        } else {

            $Box = $('.ticket-items');
            $Tab = $(this).is('li')
                ? $(this)
                : ($($TicketTabs).not('.done').first() || $($TicketTabs).first());


            $($TicketTabs).removeClass('active');
            $($Tab).addClass('active');

            R.render({
                "template": 'ticket-item',
                "json": $Tickets,
                "url": false,
                "callback": function () {
                    activateTicket();
                    if (detectDevice() === 'mobile') {
                        setBallsMargins();
                    }
                }
            });

        }
    }

    function activateTicket() {

        $('.ticket-random').off().on('click', function (e) {

            if ($(e.target).hasClass('after'))
                return false;

            if (!$(this).hasClass('select')) {

                var after = $(this).find('.after');
                after.fadeIn(300);

                setTimeout(function () {
                    after.fadeOut(300);
                }, 2000);

                if ($('.ticket-favorite .after:visible').length)
                    $('.ticket-favorite .after').fadeOut(150);

                if ($('.ticket-balls li.select').length > 0) {
                    $('.ticket-balls li.select').removeClass('select');
                }

                var lotInterval;
                lotInterval = window.setInterval(randomTicketBalls, 200);

                window.setTimeout(function () {
                    window.clearInterval(lotInterval);
                    if ($('.ticket-balls li.select').length == $Tickets.selectedBalls) {
                        $('.add-ticket').addClass('on');
                    }
                }, 1000);

                $('.balls-count').hide();
                $(this).addClass('select');

            } else {
                $('.ticket-actions, .ticket-item').find('li.select').removeClass('select');

                if ((6 - $('.ticket-balls li.select').length) > 0) {
                    $('.balls-count').show();
                    $('.balls-count b').html(6 - $('.ticket-balls li.select').length);
                    $('.add-ticket').removeClass('on');
                } else {

                    $('.balls-count').hide();
                    $('.add-ticket').addClass('on');
                }
            }

        });

        $('.ticket-favorite').off().on('click', function () {
            if (!$(this).hasClass('select')) {

                if ($('.ticket-random .after:visible').length)
                    $('.ticket-random .after').fadeOut(150);

                if ($('.ticket-item li.select').length > 0) {
                    $('.ticket-item li.select').removeClass('select');
                }

                if ($Tickets.favorite.length) {
                    for (var i = 0; i <= 5; ++i) {
                        $('.ticket-balls .number-' + $Tickets.favorite[i]).addClass('select');
                    }
                    $(this).addClass('select');
                    $('.balls-count b').html(0);
                    $('.add-ticket').addClass('on');
                } else {
                    if ($(this).find('.after:hidden').length) {
                        $(this).find('.after').fadeIn(200);
                    } else {
                        $(this).find('.after').fadeOut(200);
                    }
                }
            } else {
                $('.ticket-item li.select').removeClass('select');
            }

            if ((6 - $('.ticket-balls li.select').length) > 0) {
                $('.balls-count').show();
                $('.balls-count b').html(6 - $('.ticket-balls li.select').length);
                $('.add-ticket').removeClass('on');
            } else {

                $('.balls-count').hide();
                $('.add-ticket').addClass('on');
            }

        });

        $('.ticket-favorite .after i').off().on('click', function () {
            $('.profile .ul_li[data-link="profile-info"]').click();
        });

        $('.ball-number').off().on('click', function () {

            $('.ticket-favorite .after:visible').fadeOut(300);

            if ($('.tb-tabs_li[data-ticket="' + $('.ticket-balls').data('ticket') + '"]').hasClass('done')) {
                return;
            }

            if ($('.ticket-balls li.select').length == 6) {
                if (!$(this).hasClass('select')) {
                    return;
                }
            }

            if (!$(this).hasClass('ticket-random') && !$(this).hasClass('ticket-favorite')) {
                if (!$(this).hasClass('select')) {
                    var lim = $('.ticket-balls li.select').length;
                    var sel = 5 - lim;
                    if (lim < 6) {
                        $(this).addClass('select');
                        $('.balls-count b').html(sel);
                        if (lim == 5) {
                            $('.balls-count').hide();
                            $('.add-ticket').addClass('on');
                        }
                    }
                } else {
                    var lim = $('.ticket-balls li.select').length;
                    var sel = 6 - lim + 1;
                    $(this).removeClass('select');
                    $('.balls-count b').html(sel);
                    $('.balls-count').show();
                    $('.add-ticket').removeClass('on');
                }
            } else {
                var lim = $('.ticket-balls li.select').length;
                var sel = 6 - lim + 1;
                $(this).removeClass('select');
                $('.balls-count b').html(sel);
                $('.balls-count').show();
                $('.add-ticket').removeClass('on');
            }
        });
    }

    function randomTicketBalls() {

        if ($('.ticket-balls').find('li.select').length > 0) {
            $('.ticket-balls').find('li.select').removeClass('select');
        }

        var ticketCache = [];

        do {
            do {
                rand = Math.floor((Math.random() * 49) + 1);
            } while ($.inArray(rand, ticketCache) > -1);
            ticketCache.push(rand);

        } while (ticketCache.length != 6);

        $(ticketCache).each(function (id, num) {
            $('.ticket-balls').find('.number-' + num).addClass('select');
        });

    }


    /* ========================================================= */
    /* ========================================================= */

});