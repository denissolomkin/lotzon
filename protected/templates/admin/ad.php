<div class="modal fade banners" id="banner-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Banner preview</h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid banners">

    <? $banners = array(
        'pages' => array(
            'default' => 'По умолчанию',
            'blog' => 'Блог',
            'lottery' => 'Лотерея',
            'games' => 'Игры',
            'community' => 'Общение',
            'friends' => 'Друзья',
            'prizes' => 'Витрина'
        ),
        'devices' => array(
            'desktop' => array(
                'brand' => 'Брендирование',
                'top' => 'Шапка',
                'right' => 'Боковой',
                'teaser' => 'Тизерка'
            ),
            'tablet' => array(
                'popup' => 'Всплывайка'
            ),
            'mobile' => array(
                'popup' => 'Всплывайка'
            )
        ),
        'context' => array(
            'lottery' => 'Лотерея',
            'ticket' => 'Билет',
            'prize' => 'Приз',
            'post' => 'Статья',
            'comment' => 'Комментарий'
        ),
        'games' => array()
    );

    foreach($games as $id => $game)
        $banners['games'][$id] = $game->getTitle(1);

    if (!is_array($list))
        $list = array();

    ?>

    <form role="form" action="/private/ad" method="POST">
        <input type="hidden" name="ad[]" value="">
        <div class="row-fluid">
            <h2>Баннеры
                <?php
                foreach ($banners['devices'] as $device => $zones) : ?>
                    <button type="button" onclick="$('.devices').hide();$('#'+this.value).show();"
                            class="btn btn-primary" value="<?php echo $device; ?>">
                        <span class="fa fa-<?php echo $device; ?>"></span> <?php echo ucfirst($device); ?>
                    </button>
                <?php endforeach; ?>
                <button type="submit" class="btn btn-success right">Сохранить</button>
            </h2>
        </div>

        <div id="ad-banners" class="row-fluid" style="margin-left: -15px;width:100%;padding: 0 0 5px 5px;">

            <?php
            foreach ($banners['devices'] as $device => $locations) { ?>

                <div class="devices" id="<?php echo $device; ?>">

                    <!-- device -->
                    <h2 style="text-align: center;">
                        <span class="fa fa-<?php echo $device; ?>"></span> <?php echo ucfirst($device); ?>
                    </h2>

                    <?php
                    $locations += array('context' => 'Контекстная');
                    $locations += array('games' => 'Игры');
                    foreach ($locations as $location => $name) { ?>

                        <!-- zone -->
                        <div class="col-md-12"
                             style="background: #ccc;border-radius: 10px;padding: 10px;margin: 10px 0;width: 100%;">
                            <h3>
                                <button type="button" class="btn btn-primary"
                                        onclick="
                                        $(this).find('i').toggleClass('fa-eye-slash');
                                        $(this.parentNode.parentNode).children('div').filter(function( index ) {return $( '.groups', this ).children().length === 0;}).toggleClass('hidden');
                                        ">
                                    <i class="fa fa-eye"></i></button>
                                <?php echo ucfirst($name); ?>
                            </h3>

                            <?php
                            $array = $banners[$location]?:$banners['pages'];
                            foreach ($array as $page => $title) {

                                $sector = $list[$device][$location][$page];
                                ?>


                                <!-- page or context -->
                                <div class="col-md-3 row-banner <?php echo is_array($sector) ? '' : 'hidden'; ?>">
                                    <div class="sector">
                                        <div class="title">
                                            <button data-page="<?=$page?>" data-location="<?=$location?>" data-device="<?=$device?>" type="button" class="btn btn-success btn-xs add-group">
                                                <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>
                                            </button>
                                            <span class="glyphicon glyphicon-filter" aria-hidden="true"></span>
                                            <small><?php echo ucfirst($title); ?></small>
                                        </div>

                                        <div class="groups" id="<?= $device ?>-<?= $location ?>-<?= $page ?>">
                                            <? $gid = 0;
                                            if (is_array($sector))
                                                foreach ($sector as $group) { ?>

                                                    <div id="<?= $device ?>-<?= $location ?>-<?= $page ?>-<?= $gid ?>" data-gid="<?= $gid ?>" class='group' style="clear: both;">

                                                        <div class="row-fluid" style="">
                                                            <input type="hidden" name="ad[<?= $device ?>][<?= $location ?>][<?= $page ?>][]" value="">
                                                            <button type="button" data-group="<?= $gid ?>" data-page="<?=$page?>" data-location="<?=$location?>" data-device="<?=$device?>" class="btn btn-success add-banner btn-xs">
                                                                <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>
                                                            </button>
                                                            <button type="button" class="btn btn-danger del-group btn-xs">
                                                                <span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span>
                                                            </button>
                                                            <small>Группа баннеров №<?= ($gid + 1) ?></small>
                                                        </div>

                                                        <? $bid = 0;
                                                        if (is_array($group))
                                                            foreach ($group as $banner) { ?>
                                                                <div class="row-banner banner" data-bid="<?= $bid ?>">
                                                                    <div class="col-md-3" style="display: flex;">
                                                                        <button type="button" style="margin-top: 0px;" class="btn btn-danger del-banner btn-xs">
                                                                            <span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span>
                                                                        </button>
                                                            <textarea placeholder="Title" rows=1
                                                                      class="form-control-banner input-md"
                                                                      name="ad[<?= $device ?>][<?= $location ?>][<?= $page ?>][<?= $gid ?>][<?= $bid ?>][title]"><?= $banner['title']; ?></textarea>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                            <textarea placeholder="Div" rows=1
                                                                      class="form-control-banner input-md div"
                                                                      name="ad[<?= $device ?>][<?= $location ?>][<?= $page ?>][<?= $gid ?>][<?= $bid ?>][div]"><?= $banner['div']; ?></textarea>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                            <textarea placeholder="Script" rows=1
                                                                      class="form-control-banner input-md script"
                                                                      name="ad[<?= $device ?>][<?= $location ?>][<?= $page ?>][<?= $gid ?>][<?= $bid ?>][script]"><?= $banner['script']; ?></textarea>
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <input placeholder="Chance"
                                                                               class="form-control-banner input-md"
                                                                               name="ad[<?= $device ?>][<?= $location ?>][<?= $page ?>][<?= $gid ?>][<?= $bid ?>][chance]"
                                                                               value="<?= $banner['chance']; ?>">
                                                                    </div>
                                                                    <div class="col-md-2" style="display: flex;">
                                                                        <select
                                                                            name="ad[<?= $device ?>][<?= $location ?>][<?= $page ?>][<?= $gid ?>][<?= $bid ?>][countries][]"
                                                                            size="1" multiple="multiple"
                                                                            class="form-control-banner input-sm" value=""
                                                                            placeholder="Страны"/>
                                                                        <? foreach ($supportedCountries as $country) { ?>
                                                                            <option <?= (is_array($banner['countries']) && array_search($country, $banner['countries']) !== false ? ' selected ' : ''); ?>
                                                                                value="<?= $country ?>"><?= $country ?></option>
                                                                        <? } ?>
                                                                        </select>
                                                                        <button type="button" class="btn btn-info btn-xs view-banner right">
                                                                <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <? $bid++; ?>
                                                            <? } ?>
                                                    </div>
                                                    <? $gid++; ?>
                                                <? } ?>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>

                        </div>

                    <?php } ?>

                </div>

            <?php } ?>

        </div>



    </form>
</div>

<script>

    $(document).on("mouseover", "select", function (event) {
        $(this).attr('size', 3).css('z-index', 10);
    });

    $(document).on("mouseleave", "select", function (event) {
        $(this).attr('size', 1).css('z-index', 1);
    });

    $(document).on("click", ".add-group", function (event) {

        var device = $(this).data('device'),
            location = $(this).data('location'),
            page = $(this).data('page'),
            id = device+'-'+location+'-'+page,
            gid = $('#' + id).children('.group').last().data('gid') + 1 || 0;

        $('#' + id).append(
            '<div class="group" style="clear: both;" id="' + device + '-' + location + '-' + page + '-' + gid +'" data-gid="' + gid + '">' +
            '   <div class="row-fluid">' +
            '       <input type="hidden" name="ad[' + device + '][' + location + '][' + page + '][' + gid + ']" value="">' +
            '       <button type="button" data-device="' + device + '" data-location="' + location + '" data-page="' + page + '" data-group="' + gid + '" class="btn btn-success add-banner btn-xs"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>' +
            '       <button type="button" class="btn btn-danger del-group btn-xs"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>' +
            '       <small> Группа баннеров №' + (gid + 1) + '</small>' +
            '   </div>' +
            '</div>');
    });


    $(document).on("click", ".add-banner", function (event) {

        var device = $(this).data('device'),
            location = $(this).data('location'),
            page = $(this).data('page'),
            gid = $(this).data('group'),
            id = device+'-'+location+'-'+page+'-'+gid,
            bid = $('#' + id).children('.banner').last().data('bid') + 1 || 0;

        $('#' + id).append(
            '<div class="row-banner banner" data-bid="' + bid + '">' +
            '   <div class="col-md-3" style="display: flex;">' +
            '       <button type="button" class="btn btn-xs btn-danger del-banner" style="margin-top: 0px;"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>' +
            '       <textarea placeholder="Title" rows=1 class="form-control-banner input-md" name="ad[' + device + '][' + location + '][' + page + '][' + gid + '][' + bid + '][title]"></textarea>' +
            '   </div>' +
            '   <div class="col-md-3">' +
            '       <textarea placeholder="Div" rows=1 class="form-control-banner input-md div" name="ad[' + device + '][' + location + '][' + page + '][' + gid + '][' + bid + '][div]"></textarea>' +
            '   </div>' +
            '   <div class="col-md-3">' +
            '       <textarea placeholder="Script" rows=1 class="form-control-banner input-md script" name="ad[' + device + '][' + location + '][' + page + '][' + gid + '][' + bid + '][script]"></textarea>' +
            '   </div>' +
            '   <div class="col-md-1">' +
            '       <input placeholder="Chance" class="form-control-banner input-md" name="ad[' + device + '][' + location + '][' + page + '][' + gid + '][' + bid + '][chance]">' +
            '   </div>' +
            '   <div class="col-md-2">' +
            '       <select size=1 name="ad[' + device + '][' + location + '][' + page + '][' + gid + '][' + bid + '][countries][]"  multiple="multiple" class="form-control-banner input-sm" value="" placeholder="Страны">' +
            <? foreach ($supportedCountries as $country) { ?>'<option value="<?=$country?>"><?=$country?></option>' +
            <? } ?>
            '       </select>' +
            '       <button type="button" class="btn btn-info btn-xs view-banner right"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>' +
            '   </div>' +
            '</div>');
    });

    $(document).on("click", ".del-group", function (event) {
        $(this).parent().parent().remove();
    });

    $(document).on("click", ".del-banner", function (event) {
        $(this).parent().parent().remove();
    });

    $(document).on("click", ".view-banner", function (event) {

        var dat = {
            div: $(this).parent().parent().find('.div').text(),
            script: $(this).parent().parent().find('.script').text(),
        };

        $.ajax({
            url: "/private/banner/",
            method: 'POST',
            data: dat,
            async: true,
            dataType: 'json',
            success: function (data) {
                $("#banner-holder").find('.modal-body').empty().append(data.res);
                $("#banner-holder").modal();
                $("#banner-holder").find('.cls').on('click', function () {
                    $("#banner-holder").modal('hide');
                })
            },
            error: function () {}
        });

        return;

        el = document.getElementsByClassName("modal-body");

        $("#banner-holder").find('.modal-body').append($($(this).parent().parent().find('.div').text()));
        eval($(this).parent().parent().find('.div').text());
        $.each($($(this).parent().parent().find('.div,.script').text()), function (id, val) {
            if ($(val).prop("tagName") == 'SCRIPT') {
                if (url = $(val).attr('src')) {
                    var script = document.createElement("script");
                    script.type = "text/javascript";
                    script.src = url;
                    el[0].appendChild(script);
                } else {
                    var script = document.createElement("script");
                    script.type = "text/javascript";
                    script.text = $(val).text();
                    el[0].appendChild(script);
                }
            }
        })

        $("#banner-holder").modal();
        $("#banner-holder").find('.cls').on('click', function () {
            $("#banner-holder").modal('hide');
        })
    });


</script>

<?php /*

<div class="row-fluid"
             style="background: #ccc;margin-left: -15px;position: absolute;display:none;padding: 0 0 5px 5px;">

            <?php unset($list['settings']);

if (is_array($list))
    foreach ($list as $sid => $sector) : ?>

                    <div class="col-md-3 row-banner">
                        <div class="sector">
                            <div>
                                <button type="button" data-sector="<?= $sid ?>"
                                        class="btn btn-success btn-xs add-group"><span
                                        class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
                                <span class="glyphicon glyphicon-filter" aria-hidden="true"></span>
                                <small><?= (strstr($sid, 'game') ? $games[(str_replace('game', '', $sid))]->getTitle('RU') : $sid); ?></small>
                                <input type="hidden" name="ad[<?= $sid ?>]" value="">
                            </div>
                            <div id="<?= $sid ?>">
                                <? $gid = 0;
        if (is_array($sector))
            foreach ($sector as $group) : ?>

                                        <div id="group<?= $gid ?>" class='group' data-gid="<?= $gid ?>"
                                             style="clear: both;">

                                            <div class="row-fluid" style="">
                                                <input type="hidden" name="ad[<?= $sid ?>][<?= $gid ?>]" value="">
                                                <button type="button" data-group="<?= $gid ?>" data-sector="<?= $sid ?>"
                                                        class="btn btn-success  add-banner btn-xs">
                                                    <span class="glyphicon glyphicon-plus-sign"
                                                          aria-hidden="true"></span>
                                                </button>
                                                <button type="button" class="btn btn-danger del-group btn-xs">
                                                    <span class="glyphicon glyphicon-minus-sign"
                                                          aria-hidden="true"></span>
                                                </button>
                                                <small>Группа баннеров №<?= ($gid + 1) ?></small>
                                            </div>

                                            <? $bid = 0;
                if (is_array($group))
                    foreach ($group as $banner) : ?>
                                                    <div class="row-banner banner" data-bid="<?= $bid ?>">
                                                        <div class="col-md-3" style="display: flex;">
                                                            <button type="button" style="margin-top: 0px;"
                                                                    data-sector="<?= $key ?>"
                                                                    class="btn btn-danger del-banner btn-xs">
                                                                <span class="glyphicon glyphicon-minus-sign"
                                                                      aria-hidden="true"></span>
                                                            </button>
                                                            <textarea placeholder="Title" rows=1
                                                                      class="form-control-banner input-md"
                                                                      name="ad[<?= $sid ?>][<?= $gid ?>][<?= $bid ?>][title]"><?= $banner['title']; ?></textarea>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <textarea placeholder="Div" rows=1
                                                                      class="form-control-banner input-md div"
                                                                      name="ad[<?= $sid ?>][<?= $gid ?>][<?= $bid ?>][div]"><?= $banner['div']; ?></textarea>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <textarea placeholder="Script" rows=1
                                                                      class="form-control-banner input-md script"
                                                                      name="ad[<?= $sid ?>][<?= $gid ?>][<?= $bid ?>][script]"><?= $banner['script']; ?></textarea>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <input placeholder="Chance"
                                                                   class="form-control-banner input-md"
                                                                   name="ad[<?= $sid ?>][<?= $gid ?>][<?= $bid ?>][chance]"
                                                                   value="<?= $banner['chance']; ?>">
                                                        </div>
                                                        <div class="col-md-2" style="display: flex;">
                                                            <select
                                                                name="ad[<?= $sid ?>][<?= $gid ?>][<?= $bid ?>][countries][]"
                                                                size="1" multiple="multiple"
                                                                class="form-control-banner input-sm" value=""
                                                                placeholder="Страны"/>
                                                            <? foreach ($supportedCountries as $country) { ?>
                                                                <option <?= (is_array($banner['countries']) && array_search($country, $banner['countries']) !== false ? ' selected ' : ''); ?>
                                                                    value="<?= $country ?>"><?= $country ?></option>
                                                            <? } ?>
                                                            </select>
                                                            <button type="button"
                                                                    class="btn btn-info btn-xs view-banner right">
                                                                <span class="glyphicon glyphicon-eye-open"
                                                                      aria-hidden="true"></span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <? $bid++; ?>
                                                <? endforeach ?>
                                        </div>
                                        <? $gid++; ?>
                                    <? endforeach ?>
                            </div>
                        </div>
                    </div>
                <? endforeach ?>
        </div>
*/ ?>