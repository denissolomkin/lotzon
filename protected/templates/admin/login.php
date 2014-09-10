<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Private area login page</title>

     <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="/theme/admin/bootstrap/css/bootstrap.min.css">
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  </head>
  <body>
    <div class="container-fluid">
        <div class="row-fluid" style="position:absolute; top:40%; left:40%; z-index:1">
        <? if (!empty($formdata['error'])) { ?>
          <div class="alert alert-danger"><?=$formdata['error']?></div>
        <? } ?>
        <form role="form" method="POST" action="/private/login">
          <div class="form-group">
            <input type="text" name="login" placeholder="Login" class="form-control" value="<?=@$formdata['authdata']['login']?>">
          </div>
          <div class="form-group">
            <input type="password" name="password"  placeholder="Password" class="form-control">
          </div>
          <div class="form-group text-center">
            <input type="submit" value="Sign in" class="btn btn-success">
          </div>
        </form>
        </div>
    </div>
    <!-- Latest compiled and minified JavaScript -->
    <script src="/theme/admin/bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>