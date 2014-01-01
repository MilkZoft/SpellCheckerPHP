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
	} elseif (isset($_POST["text"])) {
		header("Content-Type: text/html; charset=UTF-8");

		echo 'Error: The length of the text is too short.';
	} else {
		echo '
			<h2>How to use it</h2>

			<p>You must send a POST request to http://spellcheckerphp.com/api/ with the follow variables:</p>

			<ul>
				<li><strong>text (mandatory):</strong> Here you will send the content that you want to fix the spelling (must be more than 10 characters).</li>
				<li><strong>type (optional):</strong> Can be "json" or "xml" if you do not define this variable you will receive your response in text plain.</li>
				<li><strong>language (optional):</strong> Can be "english" by default is "spanish" (English dictionary is not available yet).</li>
			</ul>

			<h3>Enjoy! :)</h3>

			<p>Powered by <a href="http://www.codejobs.biz" target="_blank">www.codejobs.biz</h3></p>
		';
	}
}