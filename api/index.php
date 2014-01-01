<?php
if (isset($_POST["text"]) and strlen($_POST["text"]) > 10) {
	if (isset($_POST["language"]) and $_POST["language"] == "english") {
		$language = "spanish"; // This will change once we create the english dictionary.
	} else {
		$language = "spanish";
	}

	include "../spellchecker.php";

	suggestWords($_POST["text"], $language);

	$text = spellChecker($_POST["text"], $language);

	if (isset($_POST["type"]) and $_POST["type"] == "json") {
		echo json_encode(
			array(
				"spellchecker" => array(
					"text" => $text
				)
			));
	} elseif (isset($_POST["type"]) and $_POST["type"] == "xml") {
		header("Content-type: text/xml; charset=utf-8");

		echo '<?xml version="1.0"?>
		<spellchecker>
			<text>'. $text .'</text>
		</spellchecker>';
	} else {
		header("Content-Type: text/html; charset=UTF-8");

		echo $text;
	}
} else {
	if (isset($_POST["type"]) and $_POST["type"] == "json") {
		echo json_encode(
			array(
				"spellchecker" => array(
					"error" => 'Error: The length of the text is too short.'
				)
			));
	} elseif (isset($_POST["type"]) and $_POST["type"] == "xml") {
		header("Content-type: text/xml; charset=utf-8");

		echo '<?xml version="1.0"?>
		<spellchecker>
			<error>Error: The length of the text is too short.</error>
		</spellchecker>';
	} else {
		header("Content-Type: text/html; charset=UTF-8");

		echo 'Error: The length of the text is too short.';
	}
}