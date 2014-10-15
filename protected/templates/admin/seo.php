<div class="container-fluid">
    <div class="row-fluid">
        <h2>SEO</h2>
        <hr />
    </div>
    <div class="row-fluid">
        <form role="form" action="/private/seo" method="POST">
          <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Page title" value="<?=$seo['title']?>">
          </div>
          <div class="form-group">
            <label for="title">Description</label>
            <textarea rows="3" class="form-control" name="description"><?=$seo['desc']?></textarea>
          </div>
          <div class="form-group">
            <label for="kw">Keywords</label>
            <input type="text" class="form-control" id="kw" name="kw" placeholder="Page title" value="<?=$seo['kw']?>">
          </div>
          <button type="submit" class="btn btn-success">Сохранить</button>
        </form>
    </div>
</div>