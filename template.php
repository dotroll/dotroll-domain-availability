<?php
/**
 * template, Domain Search template
 * 
 * @copyright Copyright (c) 2007 DotRoll Kft. (http://www.dotroll.com)
 * @author Zoltán Istvanovszki <zoltan.istvanovszki@dotroll.com>
 * @since 2016.09.20.
 * @package dotroll-domain-availability
 * @license https://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3
 */
?>
<!DOCTYPE html>
<html lang="hu">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Domain Kereső</title>
		<script src="https://code.jquery.com/jquery-3.1.0.min.js" integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		<script type="text/javascript">
			$(document).ready(function () {
				$('#domain').keypress(function (e) {
					if (e.which === 13) {
						$("#ajax").trigger("click");
					}
				});
				$("#ajax").click(function () {
				$.ajax({
					type: "POST",
					url: "",
					data: {domain: $("#domain").val(), ajax: true},
					beforeSend: function () {
						$("#result").html('<div class="loader">Loading...</div>');
					},
					success: function (data) {
						$("#result").html(data);
					}
				});
			});
			});
		</script>
		<style type="text/css">
			.loader,
			.loader:before,
			.loader:after {
				background: #5cb85c;
				-webkit-animation: load1 1s infinite ease-in-out;
				animation: load1 1s infinite ease-in-out;
				width: 1em;
				height: 4em;
			}
			.loader:before,
			.loader:after {
				position: absolute;
				top: 0;
				content: '';
			}
			.loader:before {
				left: -1.5em;
				-webkit-animation-delay: -0.32s;
				animation-delay: -0.32s;
			}
			.loader {
				color: #5cb85c;
				text-indent: -9999em;
				margin: 88px auto;
				position: relative;
				font-size: 11px;
				-webkit-transform: translateZ(0);
				-ms-transform: translateZ(0);
				transform: translateZ(0);
				-webkit-animation-delay: -0.16s;
				animation-delay: -0.16s;
			}
			.loader:after {
				left: 1.5em;
			}
			@-webkit-keyframes load1 {
				0%,
				80%,
				100% {
					box-shadow: 0 0;
					height: 4em;
				}
				40% {
					box-shadow: 0 -2em;
					height: 5em;
				}
			}
			@keyframes load1 {
				0%,
				80%,
				100% {
					box-shadow: 0 0;
					height: 4em;
				}
				40% {
					box-shadow: 0 -2em;
					height: 5em;
				}
			}
		</style>
	</head>
	<body>
		<div class="container">
			<h1>TALÁLJA MEG A TÖKÉLETES DOMAIN NEVET!</h1>
			<form method="post">
				<div class="row">
					<div class="col-md-8"><input type="text" name="domain" class="form-control" placeholder="Írja be a domain nevet..." id="domain" autocomplete="off" /></div>
					<div class="col-md-2"><button type="button" class="btn btn-primary" id="ajax">Keresés</button></div>
					<div class="col-md-2"></div>
				</div>
			</form>
			<div id="result">
				<?= $content ?>
			</div>
		</div>
	</body>
</html>
</body>
</html>
