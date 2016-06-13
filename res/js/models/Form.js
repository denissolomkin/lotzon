(function () {

	Form = {

		websocketConnection: null,

		timeout: {
			remove : 3000,
			fadeout: 200,
			submit : 0
		},

		get: function (form) {
			form = Form.parseForm(form);
			form.method = 'get';
			Form.send.call(this, form)
		},

		post: function (form) {
			form = Form.parseForm(form);
			form.method = 'post';
			Form.send.call(this, form)
		},

		ws: function (form) {
			form = Form.parseForm(form);
			form.method = 'ws';
			Form.send.call(this, form)
		},

		put: function (form) {
			form = Form.parseForm(form);
			form.method = 'put';
			Form.send.call(this, form)
		},

		delete: function (form) {
			form = Form.parseForm(form);
			form.method = 'delete';
			Form.send.call(this, form)
		},

		parseForm: function (form) {
			if (typeof form === 'string') {
				form = {
					action: form,
					method: 'get',
					data  : null
				};
			} else if (form.hasOwnProperty('method')) {
				form.method = form.method.toLowerCase();
			}

			return form;
		},

		send: function (form) {

			var that = this;
			form = Form.parseForm(form);
			if (form.href) {
				form.action = form.href;
			}

			form.callback = U.parse(U.parse(form.action), 'tmpl');
			form.url = U.generate(form.action, form.method);

			if (Callbacks.submit[form.callback]) {
				D.log(['C.submit.callback']);
				form = Callbacks.submit[form.callback].call(that, form);
				console.log(form.data.text)
			}
			/* FORM
			 * method: GET | POST | PUT | DELETE | WS
			 * url: ACTION | HREF
			 * data: JSON
			 * callback: FUNCTION
			 * */

			setTimeout(function () {

				if (form.method === 'ws' && !Config.websocketEmulation) {

					var conn = Form.websocketConnection,
						path = form.url,
						data = form.data,
						json = JSON.stringify({'path': path, 'data': data});

					if (!conn || conn.readyState !== 1) {

						conn = new WebSocket(Config.websocketUrl);
						conn.onopen = function (e) {
							console.info('Socket open');
							Form.websocketConnection = conn;
							conn.send(json);
						};

						conn.onerror = function (e) {
							var message = 'There was an un-identified Web Socket error';
							Form.stop.call(that)
								.message.call(that, message);
							console.error(message);
						};

						conn.onmessage = function (e) {
							data = JSON.parse(e.data);
							WebSocketAjaxResponse(data);
						};

					} else {
						conn.send(json);
					}

				} else {

					if (form.method === 'ws') {
						form.url = '/' + form.url;
					}

					$.ajax({
						url     : form.url,
						method  : form.method === 'ws'
							? "post"
							: form.method,
						data    : form.data,
						dataType: 'json',
						success : function (data) {

							if (data.hasOwnProperty('responseText')) {

								Form.stop.call(that);
								D.error.call(that, ['SERVER RESPONSE ERROR', form.action]);

							} else {

								if (form.method === 'ws') {
									WebSocketAjaxResponse.call(that, data);
									return;
								}

								form.json = data;

								Form.stop.call(that)
									.message.call(that, data.message);

								Cache.init(data);

								if (Callbacks[form.method.toLowerCase()][form.callback]) {
									D.log(['C.' + form.method.toLowerCase() + '.callback']);
									Callbacks[form.method.toLowerCase()][form.callback].call(that, form);
								}

								if (form.hasOwnProperty('after') && typeof form.after === 'function') {
									form.after.call(that, form);
								}
							}

						},

						error: function (data) {

							if (data.status == 401) {
								if (Player.is.unauthorized) {
									Content.popup.enter();
								} else {
									document.location.href = "/";
									return false;
								}
								return;
							}

							form.json = data.responseJSON || data;
							Cache.init(form.json);

							if (Callbacks['error'][form.callback]) {
								D.log(['C.error.callback']);
								if (!Callbacks['error'][form.callback].call(that, form)) {
									return;
								}
							}

							console.log(data);
							Form.stop.call(that);
							D.error.call(that, [data && (data.message || data.responseJSON && data.responseJSON.message || data.statusText) || 'NOT FOUND', form.action, data.status]);

						}
					})
				}
			}, Form.timeout.submit);

		},

		do: {

			validate: function (event) {

				var form = this;

				while (form.nodeName !== 'FORM')
					form = form.parentNode;

				var submit = form.elements['submit'],
					valid = true,
					incompleteElements = form.querySelectorAll('.incomplete'),
					errorElements = form.querySelectorAll('.error'),
					requiredElements = form.querySelectorAll('.required'),
					filterRequiredElements = Array.prototype.filter.call(requiredElements, Form.filterRequired),
					callback = U.parse(U.parse(form.getAttribute('action')), 'tmpl');

				D.log(['C.validate.' + callback]);

				if (form.nodeName === 'FORM') {

					if (errorElements.length) {
						$.each($(errorElements), function (index, element) {
							// $(element).removeClass('error');
						});
						valid = false;
					}

					if (filterRequiredElements.length) {

						$.each($(filterRequiredElements), function (index, element) {
							// $(element).parent().addClass('error');
						});
						valid = false;
					}

					if (incompleteElements.length) {
						$.each($(incompleteElements), function (index, element) {
							// $(element).parent().addClass('error');
						});
						valid = false;
					}

					if (Callbacks.validate[callback]) {
						valid = !Callbacks.validate[callback].call(this, event) ? false : valid;
					}

				}

				if (submit) {
					valid ? submit.classList.add('on') : submit.classList.remove('on');
				}

				return valid;
			},

			submit: function (event) {

				var form = this;

				while (form.nodeName !== 'FORM')
					form = form.parentNode;

				var button = form.elements['submit'],
					ajax = {
						action: form.getAttribute('action'),
						method: form.getAttribute('method'),
						data  : $(form).serializeObject()
					},
					formContenteditable = form.querySelectorAll("div[contenteditable='true']");

				if (event) {
					event.preventDefault();
					event.stopPropagation();
				}

				if (button && button.classList.contains('loading') || form.classList.contains('loading')) {
					return false;
				}

				D.log(['Form.submit.', ajax.action]);

				for (var i = 0; i < formContenteditable.length; i++) {
					ajax.data[formContenteditable[i].getAttribute('name')] = formContenteditable[i].innerHTML;
				}

				Form.start.call(form, event);

				if (!button || button.classList.contains('on')) {
					D.log('button.submit', 'info');
					Form.send.call(form, ajax);
				}


			}
		},

		filterRequired: function (node) {

			var filter = true;

			switch (node.tagName) {

				case 'INPUT':

					switch (node.type) {

						case 'text':
						case 'hidden':
							filter = node.value === '' || (node.classList.contains('float') && parseFloat(node.value) <= 0) || (node.classList.contains('int') && parseInt(node.value) <= 0);
							break;
						case 'radio':
							filter = node.form.querySelectorAll('[name="' + node.name + '"]:checked').length !== 1;
							break;
						case 'checkbox':
							filter = node.form.querySelectorAll('[name="' + node.name + '"]:checked').length === 0;
							break;
					}

					break;

				case 'DIV':
					filter = node.innerHTML === '' || (node.getAttribute('data-default') && node.innerHTML.replace(node.getAttribute('data-default'), '').length < 10);
					break;
			}

			return filter;

		},

		start: function (event) {

			var button = this.elements && this.elements['submit'];

			button && button.classList.add('loading') || this.classList.add('loading');

			if (Form.do.validate.call(this, event)) {
				D.log('button.loading', 'info');
				button && button.classList.contains('on') && button.classList.add('loading') || this.classList.add('loading');
			} else {
				button && button.classList.remove('loading') || this.classList.remove('loading');
			}

			return Form;
		},

		stop: function () {

			if ('nodeType' in this) {
				var button = this.elements && this.elements['submit'];
				button && button.classList.contains('loading') && button.classList.remove('loading') || this.classList.remove('loading');
			} else {
				// DOM.all('button.loading').removeClass('loading');
			}

			return Form;
		},

		getTimeout: function (name) {
			return name
				? (this.timeout.hasOwnProperty(name) ? this.timeout[name] : 0)
				: this.timeout.fadeout + this.timeout.remove;
		},

		message: function (message) {

			var form = this,
				formContenteditable;

			if (!DOM.isNode(form)) {
				return Form;
			}

			// clear form after adding new entities
			if (form.tagName === 'FORM' && form.getAttribute('method').toLowerCase() === 'post' && !form.classList.contains('reset-disabled')) {
				formContenteditable = form.querySelectorAll("div[contenteditable='true']");
				form.reset();
				for (var i = 0; i < formContenteditable.length; i++) {
					formContenteditable[i].innerHTML = '';
				}
			}

			if (!message) {
				return Form;
			}

			var modal = DOM.create('<div class="modal-message"><div>' + Cache.i18n(message) + '</div></div>');
			form.appendChild(modal);

			setTimeout(
				function () {
					DOM.fadeOut(modal);
					setTimeout(function () {
						DOM.remove(modal);
					}, Form.timeout.fadeout);
				},
				Form.timeout.remove);
		}

	};

	WebSocketAjaxClient = function (url, data) {

		if (!url) {
			url = 'app/' + App.key + (App.uid ? '/' + App.uid : '');
		}

		Form.send({
			action: url,
			data  : data,
			method: 'ws'
		})
	};

	WebSocketAjaxResponse = function (data) {

		if (data.error) {
			switch (data.error) {

				case 'UNFINISHED_CAPTCHA':
					Content.captcha.render();
					break;

				default:
					if (!DOM.isNode(this)) {
						D.error(data.error);
					} else {
						Form.stop.call(this).message.call(this, data.error);
					}
					break;
			}

		} else {

			Apps.sample = null;
			path = data.path;
			if (data.res) {

				if (path == 'stack' || (data.res.app && data.res.app.hasOwnProperty('uid') && data.res.app.uid != App.uid)) {
					App = {};
				} else if (App.winner) {
					App['winner'] = null;
					App['fields'] = null;
				}

				for (var key in data.res) {
					if (key !== 'app') {
						App[key] = data.res[key]
					}
				}

				if ('app' in data.res) {
					for (var key in data.res['app'])
						App[key] = data.res['app'][key]
					data = null;
				}

				Apps.playAudio([App.key, App.action]);
			}

			action = data && (data.res && data.res.action || path) || App.action;

			switch ('function') {

				/* Common Game Callback Action */
				case data && !Apps[path] && typeof Game.callback[action]:
					console.log('Game.callback.' + action);
					Game.callback[action](data);
					break;

				/* Specific App action */
				case App.key && typeof Apps[App.key]['action'][action]:
					console.log(App.key + '.action.' + action);
					Apps[App.key]['action'][action](data);
					break;

				/* Common Game Action */
				case (!data || App.action) && typeof Game.action[action]:
					console.log('Game.action.' + action);
					Game.action[action](data);
					break;

				/* Default App Action */
				case App.key && typeof Apps[App.key].action.default:
					console.log(App.key + '.action.default');
					Apps[App.key].action.default(data);
					break;
			}

			Form.stop.call(this)
				.message.call(this, data && data.message);
		}
	}

})();