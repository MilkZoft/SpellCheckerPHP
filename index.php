<?php
include "spellchecker.php";

header("Content-Type: text/html; charset=UTF-8");
?>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>SpellCheckerPHP: El corrector ortográfico en Español para tu Web</title>

		<style>	
			body {
				background-color: #FF5;
				color: #333;
				font-size: 1em;
				font-family: Arial, Verdana;
			}
		</style>
	</head>

	<body>
		<h1>SpellCheckerPHP: El corrector ortográfico en Español para tu Web</h1>

		<p>
			Puedes corregir tus textos en línea:
		</p>

		<form action="api/index.php" method="post">
			<h3>Text a corregir</h3>

			<p>
				<textarea name="text" style="border: 1px solid #CCC; background-color: #EEE; width: 700px; height: 500px;"></textarea>
			</p>

			<p>
				<input name="Corregir" style="border: 1px solid #CCC; font-size: 1.5em;" value="Corregir Ortografía" type="submit" />
			</p>
		</form>
	</body>
</html>