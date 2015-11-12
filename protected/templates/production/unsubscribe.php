<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Page</title>
	<meta name="keywords" content="" />
	<link href="/tpl/css/unsubscribe.css" rel="stylesheet">
	<script src="/tpl/js/lib/jquery.min.js"></script>
</head>

<body>
	<div class="header">
		<a href="/" class="logo"></a>
	</div>
	<div>
		<p>Вы уверены, что хотите отписаться от рассылки?</p>
		<p>
			<button id="unsubscribe" class="bordered">Отписаться</button>
			<button id="cancel">Отмена</button>
		</p>
	</div>

	<script>
		$('#cancel').on('click', function() {
			document.location.href='/';
		});
		$('#unsubscribe').on('click', function() {
			$.ajax({
				url     : "/unsubscribe/",
				method  : 'POST',
				data    : {
					email : '<?=$email?>',
					hash  : '<?=$hash?>'
				},
				async   : false,
				dataType: 'json',
				success: function(data) {
					if (data.status == 1) {
						document.location.href='/';
					} else {
						showError('Данные отписки не верны');
					}
				},
				error: function() {
					showError('Произошла ошибака, попробуйте позднее');
				}
			});
		});
		function showError(message) {
			alert(message);
		}
	</script>

</body>

</html>
