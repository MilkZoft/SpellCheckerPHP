<?php
include "spellchecker.php";

header("Content-Type: text/html; charset=UTF-8");

if (isset($_POST["text"])) {
	$text = trim($_REQUEST["text"]);

	if ($text == "") {
		$fixedText = "Error: You must write a text to fix."
	} else {  
		suggestWords($_REQUEST["text"], $language);

		$text = stripslashes($_REQUEST["text"]);
		$fixedText = spellChecker($_REQUEST["text"], "spanish");
	}
} else {
	$fixedText = "";
	$text = "";
}

?>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>SpellCheckerPHP (Beta): El corrector ortográfico en Español para tu Web</title>

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
		<h1>SpellCheckerPHP (Beta): El corrector ortográfico en Español para tu Web</h1>

		<p>
			Puedes corregir tus textos en línea:
		</p>

		<form action="index.php" method="post">
			<h3>Texto a corregir 
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
			Texto corregido</h3>

			<p>
				<textarea name="text" style="border: 1px solid #CCC; background-color: #EEE; width: 300px; height: 300px; float: left; margin-right: 10px;"><?php echo $text; ?></textarea> &nbsp;
				<textarea name="fixedText" style="border: 1px solid #CCC; background-color: #EEE; width: 300px; height: 300px; float: left;"><?php echo $fixedText; ?></textarea>
			</p>

			<p style="clear: both; margin-top: 15px;">
				<input name="Corregir" style="border: 1px solid #CCC; font-size: 1.5em;" value="Corregir Ortografía" type="submit" />
			</p>
		</form>
	</body>
</html>