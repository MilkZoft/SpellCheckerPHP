<?php
if (isset($_POST["text"])) {
	if (isset($_POST["language"]) and $_POST["language"] == "english") {
		$language = "spanish";
	} else {
		$language = "spanish";
	}

	include "../spellchecker.php";

	if (isset($_POST["type"]) and $_POST["type"] == "json") {
		echo json_encode(
			array(
				"spellchecker" => array(
					"text" => spellChecker($_POST["text"], $language)
				)
			));
	} elseif (isset($_POST["type"]) and $_POST["type"] == "xml") {
		header("Content-type: text/xml; charset=utf-8");

		echo '<?xml version="1.0"?>
		<spellchecker>
			<text>'. spellChecker($_POST["text"], $language) .'</text>
		</spellchecker>';
	} else {
		header("Content-Type: text/html; charset=UTF-8");

		echo spellChecker($_POST["text"], $language);
	}
}