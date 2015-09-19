<div class="content-top">
	<div class="content-main">

		<div class="content-box">

            <a href="javascript:history.back()"><div class="content-box-header back">Вывод денег</div></a>

			<div class="content-box-content clearfix">
				<div class="content-box-item cabinet-cashout">

					<div class="cco-title clearfix">
						<div>Выберите способ вывода денег</div>
						<a href="cabinet_cashout_inf" class="cco-edit"><span>редактировать</span></a>
					</div>

					<div class="cco-choose-method">
						<form action="cabinet_cashout_blocked" method="post">
							<div class="cco-methods">
								<div class="cco-method clearfix">
									<label>
										<input id="cco-mb" class="input-radio" name="method" type="radio" value="1" checked="checked">
										<label for="cco-mb" class="input-radio"></label>
										<div class="cco-method-name">Мобильный телефон</div>
										<div class="cco-method-value">+380 50 715 60 50</div>
									</label>
								</div>
								<div class="cco-method clearfix">
									<label>
										<input id="cco-ym" class="input-radio" name="method" type="radio" value="2">
										<label for="cco-ym" class="input-radio"></label>
										<div class="cco-method-name">Яндекс Деньги</div>
										<div class="cco-method-value">4100 1144 3782 361</div>
									</label>
								</div>
								<div class="cco-method clearfix">
									<label>
										<input id="cco-wm" class="input-radio" name="method" type="radio" value="3">
										<label for="cco-wm" class="input-radio"></label>
										<div class="cco-method-name">WebMoney</div>
										<div class="cco-method-value">R 333289102947</div>
									</label>
								</div>
								<div class="cco-method clearfix">
									<label>
										<input id="cco-qw" class="input-radio" name="method" type="radio" value="4">
										<label for="cco-qw" class="input-radio"></label>
										<div class="cco-method-name">QIWI</div>
										<div class="cco-method-value">+380 50 715 60 50</div>
									</label>
								</div>
							</div>

							<div class="cco-enter-sum clearfix">
								<div class="cco-enter-sum-box clearfix">
									<div class="title">Введите сумму</div>
									<input type="text" value="12"><span class="cco-currency">грн</span>
								</div>
								<button type="submit" class="cco-cashout-btn">Вывести</button>
							</div>
						</form>
					</div>

					<div class="cco-inf">
						<p>Все заявки на вывод денег обрабатываются в течении 7-ми рабочих дней.</p>
						<p>Информация о состоянии заявки можно посмотреть в <a href="cabinet_payments_history">истории выплат</a></p>
						<p>Если Вы не получили выигрыш в указанный срок напишите нам используя <a href="communication_new_message_selected">эту форму</a></p>
					</div>

				</div>
			</div>

		</div>

	</div><!-- .content-main -->
</div><!-- .content-top -->