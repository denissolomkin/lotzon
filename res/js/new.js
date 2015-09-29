$(function () {

    /* ========================================================= */
    //                        SYSTEM FUNCTIONS
    /* ========================================================= */


    $.ajaxSetup({
        error: function (xhr, status, message) {
            throw('An AJAX error occured: ' + status + "\nError: " + message);
        }
    });

    function loading(options) {
        if (options.box)
            $('.loading', options.box).length ? $('.loading', options.box).show() : options.box.append('<div class="loading"><div></div></div>');
    }

    window.onerror = function (message, url, line, col, error) {

        if ($Button) {
            $Button.data('disabled', false);
            $Button = null;
        }

        $(".error").remove();
        $(".loading").remove();

        $Error = $('<div class="error"><div><span>' + i18n('title-error') + '</span>' + message + '</div></div>');


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

        empty();

        return true;
    }

    function i18n(key) {
        return key ? ($MUI[key] ? $MUI[key] : key) : (function (key) {
            return i18n(key);
        });
    }

    function log(log, type) {

        type = type || 'log';
        var d = new Date();

        if ($Debug) {

            var output = '';

            if (typeof log == 'object' && log.length) {

                $.each(log, function (index, obj) {
                    if (obj)
                        output += JSON.stringify(obj).replace(/"/g, "").substring(0, 40) + ' ';
                });

            } else {
                output = JSON.stringify(log).replace(/"/g, "").substring(0, 40);
            }


            console[type](d.toLocaleTimeString('ru-RU') + ' ' + output);
        }
    }

    function isAnchor() {
        return ($Tab.attr('href').indexOf('#') == 0);
    }

    $Url = {

        "Path": {
            "Json": "/res/json/",
            "Ajax": "/res/json/",
            "Tmpl": "/res/tmpl/"
        },

        "Generate": {
            "Ajax": function (url) {
                return $Url.Path.Ajax + $Url.Parse.Url(url);
            },

            "Json": function (url) {
                return $Url.Path.Json + $Url.Parse.Url(url);
            },

            "Tmpl": function (url) {
                console.log(self);
                return $Url.Path.Tmpl + $Url.Parse.Url(url) + '.html';
            }
        },

        "Parse": {
            "Url": function (url) {
                return url.replace(/-/g, '/');
            },

            "Tmpl": function (url) {
                return url.replace(/-\d+/g, '');
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
                log(['updateURL:', url]);
                var stateObj = {foo: "bar"};
                history.pushState(stateObj, "page 2", '/' + $Url.Parse.Url(url));
            }
        }

    };


    /* ========================================================= */
    /* ========================================================= */


    /* ========================================================= */
    //                        TAB AND CAT SWITCH
    /* ========================================================= */

    // variables
    $Cache = {};
    $Templates = {};
    $Render = [];
    $IsRendering = false;
    $Debug = true;
    $MUI = {
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
        "title-error": "Ошибка",

    };

    empty();

    // classes
    $Tabs = '.content-box-tabs a';
    $Cats = '.content-box-cat a';

    // handlers
    $(document).on('click', $Tabs, switchTab);
    $(document).on('click', $Cats, switchCat);
    $(document).on('click', 'a', loadBlock);
    $(document).on('click', 'div.back', backBlock);
    $("header a").on('click', loadPage);

    // functions
    function loadPage(event) {

        if (!event.isPropagationStopped()) {

            event.stopPropagation();
            $Box = $('.content-top');
            $Tab = $(this);

            render({
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
            log(['loadBlock:', $Tab.attr('href')]);

            render({
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

            log(['backBlock:', $Tab.attr('href')]);
            $($Tabs + '.active').click();
        }

        return false;

    }

    function switchTab(event) {

        if (!event.isPropagationStopped()) {

            event.stopPropagation();
            $Box = $(this).parents('.content-box').find('.content-box-content');
            $Tab = $(this);

            log(['switchTab:', $Tab.attr('href')]);

            if (isAnchor()) {

                $($Tabs, $Tab.parents('.content-box-header')).removeClass('active');
                $(' > div', $Box).hide();
                $('.content-box-item.' + $Href, $Box).show();
                $Tab.addClass('active');

            } else {
                render();
            }
        }

        return false;
    }

    function switchCat(event) {

        if (!event.isPropagationStopped()) {

            event.stopPropagation();
            $Cat = $(this);
            log(['switchCat:', $Cat.attr('href')]);

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
    //                        MUSTACHE
    /* ========================================================= */

    function render(options) {

        try {
            if (!options) options = {};

            if (!options.template)
                options.template = $Url.Parse.Undo($Template || $Href || $Tab.attr('href'));
            if (!options.href)
                options.href = $Url.Parse.Undo($Href || options.template);
            if (!options.json)
                options.json = $JSON || false;
            if (!options.callback)
                options.callback = $Callback;

            empty('soft');

            log(['render.push:', options.template, options.href, options.json], 'info');

            $Render.push({
                'options': {
                    'box': $Box,
                    'tab': $Tab,
                    'callback': options.callback,
                    "this": options.template,
                },
                'url': options.url,
                'template': options.template,
                'href': options.href,
                'json': options.json,
            });

            if (!$IsRendering)
                rendering();

        } catch (error) {
            throw(error);
        }
    }

    function rendering() {

        try {
            while ($Render.length) {

                $IsRendering = true;

                var render = $Render.shift();

                loading(render.options);

                log(['rendering.run:', render.template, render.href, render.json], 'info');

                if (render.url !== false)
                    $Url.Update(typeof render.href != 'object' ? render.href : render.template);

                if (typeof render.json == 'object') {
                    log(['JSON from Object:', render.json]);
                    renderTMPL(render.template, render.json, render.options);
                } else {
                    renderJSON(render.href, render.template, render.options);
                }
            }

            stop();

        } catch (error) {
            throw(error);
        }
    }

    function renderJSON(href, template, options) {

        try {
            log(['renderJSON:', href]);

            var json = null;

            if (json = cache(href)) {

                log(['JSON from Cache:', json]);
                renderTMPL(template, json, options);

            } else {

                $.getJSON($Url.Generate.Json(href), function (response) {
                    if (response.status == 1) {

                        json = cache(href, response.res);
                        log(['JSON from AJAX:', json], 'warn');
                        renderTMPL(template, json, options);

                    } else {

                        error(response.message);

                    }
                });
            }

        } catch (error) {
            throw(error);
        }
    }

    function renderTMPL(template, json, options) {

        try {
            template = $Url.Parse.Tmpl(template);
            log(['renderTMPL:', template]);

            if ($('.template.' + template).length) {

                log(['TMPL already in DOM', template]);
                renderHTML(template, json, options);

            } else if ($Templates[template]) {

                template = $Templates[template];
                log(['TMPL from Cache', template]);
                renderHTML(template, json, options);

            } else if ($('#tmpl-' + template).length) {

                $Templates[template] = $('#tmpl-' + template).html();
                template = $Templates[template];
                log(['TMPL from HTML:', template]);
                renderHTML(template, json, options);

            } else {
                $.get($Url.Generate.Tmpl(template), function (data) {

                    if (!$(data).attr('class')) {
                        throw("Format Template Error");
                    } else {
                        template = $Templates[template] = data;
                        log(['TMPL from AJAX:', template], 'warn');
                        renderHTML(template, json, options);
                    }

                });
            }
        } catch (error) {
            throw(error);
        }
    }

    function renderHTML(template, json, options) {

        try {
            log(['renderHTML:', template, json]);
            var rendered = null;

            if (typeof json != 'object') {

                log('Rendered with HTML');
                rendered = $($('.template.' + template)[0].outerHTML).html(json);

            } else {

                log('Rendered with Template');
                Mustache.parse(template);   // optional, speeds up future uses
                rendered = Mustache.render(template, $.extend({"i18n": i18n}, json));

            }

            inputHTML(rendered, options);
        } catch (error) {
            throw(error);
        }
    }

    function inputHTML(rendered, options) {

        try {
            log(['inputHTML into:', (typeof options.box == 'object' ? options.box.attr('class') : options.box)]);

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
                log(['callback']);
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
                        log(['clickTab:', $($Tabs, options.box).not(".active").filter(":visible").first().attr('href')]);
                        $($Tabs, options.box).not(".active").filter(":visible").first().click();
                    }

                }

                /* tab functionality after click on tab */
                if (options.tab) {

                    $('.active', options.tab.parent().parent()).removeClass('active');

                    if ($($Cats, options.tab.parents('.content-box')).filter(":visible").length) {
                        log(['clickCat:', $($Cats, options.box).first().attr('href')]);
                        $($Cats, options.tab.parents('.content-box')).first().click();
                    }

                    options.tab.addClass('active');
                }


            }

            empty();

        } catch (error) {
            throw(error);
        }
    }

    function cache(key, data) {

        if (key && $Cache[key]) {
            return $Cache[key];
        } else if (data && !data['nocache']) {
            return $Cache[key] = data;
        } else if (data) {
            return data;
        } else
            return false;
    }

    function empty(mode) {

        log('empty.' + (mode ? mode : 'hard'));
        $Template = $Href = $JSON = $Callback = null;
        if (!mode) $Tab = $Box = $Button = null;
    }

    function stop() {
        log('rendering.stop');
        $IsRendering = false;
        $(".loading").remove();
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
                html += "<li data-ticket='" + i + "' class='" + (this.balls && this.balls[i] ? 'done' : '') + "'><span>" + i18n('title-ticket') + " </span>#" + i + "</li>";
            }
            return html;
        },

        "isDone": function () {
            console.log($($TicketTabs).filter('.active').data('ticket'));
            return (this.balls && this.balls[$($TicketTabs).filter('.active').data('ticket')] && this.balls[$($TicketTabs).filter('.active').data('ticket')].length && this.balls[$($TicketTabs).filter('.active').data('ticket')].length == this.selectedBalls);
        },

        "isComplete": function () {
            return (this.balls && Object.keys(this.balls).length == this.totalTickets);
        },


        "completeHTML": function () {

            var html = '';

            $.each(this.balls, function (index, ticket) {
                html += "<li><span>БИЛЕТ</span> #" + index + "<ul class=balls-box>";
                $.each(ticket, function (number, ball) {
                    html += "<li class=ball-circle>" + ball + "</li>";
                });
                html += "</ul></li>";
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
                url: $Url.Generate.Ajax('ticket'),
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

        log('renderTicket');

        if ($Tickets.isComplete()) {

            $Box = $('.ticket-items').parent();
            render({
                "template": 'ticket-complete',
                "json": $Tickets,
                "url": false
            });

        } else {

            $Box = $('.ticket-items');
            $Callback = function () {
                $($TicketTabs).not('.done').first().click();
            };
            render({
                "template": 'ticket-tabs',
                "json": $Tickets,
                "url": false
            });

        }

    }

    function switchTicket() {

        log('switchTicket');

        if ($Tickets.isComplete()) {

            $Box = $('.ticket-items').parent();
            render({
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

            $Callback = function () {
                activateTicket();
                if (detectDevice() === 'mobile') {
                    setBallsMargins();
                }
            };

            render({
                "template": 'ticket-item',
                "json": $Tickets,
                "url": false
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
    };

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