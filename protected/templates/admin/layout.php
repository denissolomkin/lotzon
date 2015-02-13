<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=$title?></title>

      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

      <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/redmond/jquery-ui.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="/theme/admin/bootstrap/css/bootstrap.min.css">
      <!--link rel="stylesheet" href="/theme/admin/glyphicons/css/style-new.css"-->


      <link href="/theme/admin/bootstrap/css/bootstrap-toggle.min.css" rel="stylesheet">
      <script src="/theme/admin/bootstrap/js/bootstrap-toggle.min.js"></script>

      <!--link href="/theme/admin/jquery-ui/css/jquery-ui.min.css" rel="stylesheet">
      <link href="/theme/admin/jquery-ui/css/jquery-ui.structure.min.css" rel="stylesheet">
      <script src="/theme/admin/jquery-ui/js/jquery-ui.min.js"></script-->

      <!-- Include Summernote CSS files -->
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
    
    <link href="/theme/admin/lib/summernote/summernote.css" rel="stylesheet">

    <!-- Include Summernote JS file -->
    <script src="/theme/admin/lib/summernote/summernote.js"></script>
      <link href="/theme/admin/lib/admin.css" rel="stylesheet">

  </head>
  <body style="">
      <div class="container-fluid text-center">
      <div class="row-fluid">&nbsp;</div>
        <div class="row-fluid">
            <ul class="nav nav-pills" role="tablist">

                <li class="pull-right"><a href="/private/logout"><span class="glyphicon glyphicon-off" aria-hidden="true"></span></a></li>

                <? foreach(Admin::$PAGES as $key=>$pages) : ?>
                    <? if(is_array($pages['pages'])) {
                        $menu=array();
                        foreach($pages['pages'] as $link=>$page)
                            if(Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$link]) :
                                $menu[]='
                                <li'.($activeMenu == $link ? ' class="active"' : '').'><a href="/private/'.$link.'">'.
                                    (isset($page['icon'])?'<span class="glyphicon glyphicon-'.$page['icon'].'" aria-hidden="true"></span> ':'').
                                    ($page['name']?:'').'</a></li>';

                            endif ?>

                <? if(count($menu))
                            echo'
                        <li>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info" style="padding: 9px 12px;" data-toggle="dropdown" aria-expanded="false">'.
                                (isset($pages['icon'])? '<span class="glyphicon glyphicon-'.$pages['icon'].'" aria-hidden="true"></span> ':'').
                                $key.' <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                '.implode('',$menu).'
                                </ul>
                            </div>
                        </li>';
                    } elseif(Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$key]) { ?>

                        <li class="<?=($activeMenu == $key ? 'active ' : '')?><?=$pages['css']?:''?>"><a href="/private/<?=$key?>">
                                <? if($pages['icon']): ?> <span class="glyphicon glyphicon-<?=$pages['icon']?>" aria-hidden="true"></span>  <? endif ?>
                                <?=($pages['name']?:'')?>
                                <? if ($key=='users') : ?><span class="label label-warning"><?=PlayersModel::instance()->getProcessor()->getPlayersCount()?></span> <? endif ?>
                                <? if ($key=='reviews') : ?><span class="label label-warning"><?=ReviewsModel::instance()->getProcessor()->getCount(0)?></span> <? endif ?>
                                <? if ($key=='monetisation') : ?><span class="label label-warning"><?=ShopOrdersModel::instance()->getProcessor()->getOrdersToProcessCount()?> / <?=MoneyOrderModel::instance()->getProcessor()->getOrdersToProcessCount()?></span> <? endif ?>
                            </a></li>
                <? } ?>
                <? endforeach ?>
            </ul>
        </div>
      </div>
      <?=$yield?>
    <!-- Latest compiled and minified JavaScript -->
    <script src="/theme/admin/bootstrap/js/bootstrap.min.js"></script>
  <script>
      function assignByPath(obj,path,value){
          if (path.length == 1) {
              obj[path[0]] = value;
              return obj;
          } else if (obj[path[0]] === undefined) {
              obj[path[0]] = {};
          }
          return assignByPath(obj[path.shift()],path,value);
      }

      $.fn.serializeObject = function(){
          var obj = {};

          $.each( this.serializeArray(), function(i,o){
              var n = o.name,
                  v = o.value;
              path = n.replace(/\]\[/g,'.').replace(/\[/g,'.').replace(']','').split('.');

              assignByPath(obj,path,v);
          });

          return obj;
      };
      String.prototype.replaceArray = function (find, replace) {
          var replaceString = this;
          for (var i = 0; i < find.length; i++) {
              // global replacement
              var pos = replaceString.indexOf(find[i]);
              while (pos > -1) {
                  replaceString = replaceString.replace(find[i], replace[i]);
                  pos = replaceString.indexOf(find[i]);
              }
          }
          return replaceString;
      };
  </script>
  </body>
</html>
