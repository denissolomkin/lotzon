<div class="content-top">
	<div class="content-main">

		<div class="content-box">

            <a href="javascript:history.back()"><div class="content-box-header back">Настройки</div></a>

			<div class="content-box-content clearfix">
				<div class="content-box-item settings">

					<form action="settings" method="post">
						<div class="s-pass">
							<div class="title">Пароль</div>
							<ul>
								<li class="s-pass-names">
									<ul>
										<li>Старый пароль</li>
										<li>Новый пароль</li>
										<li>Новый еще раз</li>
									</ul>
								</li>
								<li class="s-pass-values">
									<ul>
										<li><input type="password" name=""></li>
										<li><input type="password" name=""></li>
										<li><input type="password" name=""></li>
									</ul>
								</li>
							</ul>
						</div>

						<div class="s-lang">
							<div class="title">Язык сайта</div>
							<label class="radio-text">
								<input type="radio" name="site-lang" value="1" checked>
								<span>Русский</span>
							</label>
							<label class="radio-text">
								<input type="radio" name="site-lang" value="2">
								<span>Украинский</span>
							</label>
							<label class="radio-text">
								<input type="radio" name="site-lang" value="3">
								<span>English</span>
							</label>
						</div>

						<div class="s-email">
							<div class="title">Получать email письма</div>
							<label class="radio-text">
								<input type="radio" name="email" value="1" checked>
								<span>Да</span>
							</label>
							<label class="radio-text">
								<input type="radio" name="email" value="2">
								<span>Нет</span>
							</label>
						</div>
						
						<button type="submit" class="settings-btn">Сохранить</button>
					</form>

				</div>
			</div>

		</div>

	</div><!-- .content-main -->
</div><!-- .content-top -->