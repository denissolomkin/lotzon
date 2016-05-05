<div class="container-fluid">
    <div class="row-fluid">
        <h2>SEO</h2>
        <hr />
    </div>
    <div class="row-fluid">
        <form role="form" action="/private/seo" method="POST">
          <div class="form-group">
            <label for="Title">Title</label>
            <input type="text" class="form-control" id="Title" name="Title" placeholder="Page title" value="<?=$seo['Title']?>">
          </div>
          <div class="form-group">
            <label for="Description">Description</label>
            <textarea rows="3" class="form-control" name="Description" placeholder="Description"><?=$seo['Description']?></textarea>
          </div>
          <div class="form-group">
            <label for="Keywords">Keywords</label>
            <input type="text" class="form-control" id="Keywords" name="Keywords" placeholder="Keywords" value="<?=$seo['Keywords']?>">
          </div>
            <!--div class="form-group">
                <label for="Pages">Формат сайта</label>
                <select name="Pages" class="form-control">
                    <option value="0">Одностраничный</option>
                    <option value="1" <?=$seo['Pages']?' selected':''?>>Многостраничный</option>
                </select>
            </div>
            <div class="form-group">
                <label for="Multilanguage">Мультиязычность</label>
                <select name="Multilanguage" class="form-control">
                    <option value="0">Отключена</option>
                    <option value="1" <?=$seo['Multilanguage']?' selected':''?>>Включена</option>
                </select>
            </div>
            <div class="form-group">
                <label for="Debug">Режим отладки</label>
                <select name="Debug" class="form-control">
                    <option value="0">Отключен</option>
                    <option value="1" <?=$seo['Debug']?' selected':''?>>Включен</option>
                </select>
            </div-->
            <div class="form-group">
                <label for="WebSocketReload">Сброс вебсокета</label>
                <select name="WebSocketReload" class="form-control">
                    <option value="0">Нет</option>
                    <option value="1" <?=$seo['WebSocketReload']?' selected':''?>>Да</option>
                </select>
            </div>
            <div class="form-group">
                <label for="SiteVersionUpdate">Обновить версию сайта от <?=date('d.m.Y H:i',$seo['SiteVersion'])?></label>
                <select name="SiteVersionUpdate" class="form-control">
                    <option value="0">Нет</option>
                    <option value="1">Да</option>
                </select>
            </div>
          <button type="submit" class="btn btn-success">Сохранить</button>
        </form>
    </div>
</div>