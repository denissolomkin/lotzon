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
            <textarea rows="3" class="form-control" name="description" placeholder="Description"><?=$seo['desc']?></textarea>
          </div>
          <div class="form-group">
            <label for="kw">Keywords</label>
            <input type="text" class="form-control" id="kw" name="kw" placeholder="Keywords" value="<?=$seo['kw']?>">
          </div>
            <div class="form-group">
                <label for="kw">Формат сайта</label>
                <select name="pages" class="form-control">
                    <option value="0">Одностраничный</option>
                    <option value="1" <?=$seo['pages']?' selected':''?>>Многостраничный</option>
                </select>
            </div>
            <div class="form-group">
                <label for="multilanguage">Мультиязычность</label>
                <select name="multilanguage" class="form-control">
                    <option value="0">Отключена</option>
                    <option value="1" <?=$seo['multilanguage']?' selected':''?>>Включена</option>
                </select>
            </div>
          <button type="submit" class="btn btn-success">Сохранить</button>
        </form>
    </div>
</div>