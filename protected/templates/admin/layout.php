<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=$title?></title>

      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

      <!--link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/redmond/jquery-ui.css" -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="/theme/admin/bootstrap/css/bootstrap.min.css">
      <link rel="stylesheet" href="/theme/admin/datepicker/css/datepicker.css">
      <!--link rel="stylesheet" href="/theme/admin/glyphicons/css/style-new.css"-->


      <link href="/theme/admin/bootstrap/css/bootstrap-toggle.min.css" rel="stylesheet">
      <script src="/theme/admin/bootstrap/js/bootstrap-toggle.min.js"></script>

      <!--link href="/theme/admin/jquery-ui/css/jquery-ui.min.css" rel="stylesheet">
      <link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
      <link href="/theme/admin/jquery-ui/css/jquery-ui.structure.min.css" rel="stylesheet"-->
      <script src="/theme/admin/jquery-ui/js/jquery-ui.min.js"></script>

      <!-- Include Summernote CSS files -->
      <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    
    <link href="/theme/admin/lib/summernote/summernote.css" rel="stylesheet">

    <!-- Include Summernote JS file -->
    <script src="/theme/admin/lib/summernote/summernote.js"></script>
      <script src="/theme/admin/datepicker/js/bootstrap-datepicker.js"></script>
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
                                    (isset($page['icon'])?'<span class="fa fa-'.$page['icon'].'"></span> ':'').
                                    ($page['name']?:'').'</a></li>';

                            endif ?>

                <? if(count($menu))
                            echo'
                        <li>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info" style="padding: 9px 12px;" data-toggle="dropdown" aria-expanded="false">'.
                                (isset($pages['icon'])? '<span class="fa fa-'.$pages['icon'].'"></span> ':'').
                                $key.' <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                '.implode('',$menu).'
                                </ul>
                            </div>
                        </li>';
                    } elseif(Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$key]) { ?>

                        <li class="<?=($activeMenu == $key ? 'active ' : '')?><?=$pages['css']?:''?>"><a href="/private/<?=$key?>">
                                <? if($pages['icon']): ?> <span class="fa fa-<?=$pages['icon']?>"></span>  <? endif ?>
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
          console.log(path[0]);
          if (path.length == 1) {
              if(path[0])
                obj[path[0].replace(':','.')] = value;
              else obj[value]=value;
              return obj;
          } else if (obj[path[0]] === undefined) {
              obj[path[0].replace(':','.')] = {};
          }
          return assignByPath(obj[path.shift()],path,value);
      }


      $.fn.serializeObject = function(){
          var obj = {};

          $.each( this.serializeArray(), function(i,o){
              var n = o.name,
                  v = o.value;
              path = n.replace('.',':').replace(/\]\[/g,'.').replace(/\[/g,'.').replace(']','').split('.');

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

      function nl2br( str ) {	// Inserts HTML line breaks before all newlines in a string
          //
          // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)

          return str.replace(/([^>])\n/g, '$1<br/>');
      }

      jQuery.fn.sortElements = (function(){
          var sort = [].sort;

          return function(comparator, getSortable) {

              getSortable = getSortable || function(){return this;};

              var placements = this.map(function(){

                  var sortElement = getSortable.call(this),
                      parentNode = sortElement.parentNode,

                  // Since the element itself will change position, we have
                  // to have some way of storing its original position in
                  // the DOM. The easiest way is to have a 'flag' node:
                      nextSibling = parentNode.insertBefore(
                          document.createTextNode(''),
                          sortElement.nextSibling
                      );

                  return function() {

                      if (parentNode === this) {
                          throw new Error(
                              "You can't sort elements if any one is a descendant of another."
                          );
                      }

                      // Insert before flag:
                      parentNode.insertBefore(this, nextSibling);
                      // Remove flag:
                      parentNode.removeChild(nextSibling);

                  };

              });

              return sort.call(this, comparator).each(function(i){
                  placements[i].call(getSortable.call(this));
              });

          };


      });

  </script>
  </body>
</html>
