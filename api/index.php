<?php
if (isset($_REQUEST["benchmark"])) {
    $time = microtime(true);
}

if (isset($_REQUEST["text"]) and strlen($_REQUEST["text"]) > 2) {
	if (strpos($_REQUEST["text"], " ") === false) {
		header("Content-Type: text/html; charset=UTF-8");

		echo 'Error: You need to write a sentence not only a single word!';
	} else {
		if (isset($_REQUEST["language"]) and $_REQUEST["language"] == "english") {
			$language = "spanish"; // This will change once we create the english dictionary.
		} else {
			$language = "spanish";
		}

		include "../spellchecker.php";

		suggestWords($_REQUEST["text"], $language);

		$text = spellChecker($_REQUEST["text"], $language);

		if (isset($_REQUEST["type"]) and $_REQUEST["type"] == "json") {
			echo json_encode(
				array(
					"spellchecker" => array(
						"text" => $text
					)
				));
		} elseif (isset($_REQUEST["type"]) and $_REQUEST["type"] == "xml") {
			header("Content-type: text/xml; charset=utf-8");

			echo '<?xml version="1.0"?>
			<spellchecker>
				<text>'. $text .'</text>
			</spellchecker>';
		} else {
			header("Content-Type: text/html; charset=UTF-8");

			echo $text;
		}
	}
} else {
	if (isset($_REQUEST["type"]) and $_REQUEST["type"] == "json") {
		echo json_encode(
			array(
				"spellchecker" => array(
					"error" => 'Error: The length of the text is too short.'
				)
			));
	} elseif (isset($_REQUEST["type"]) and $_REQUEST["type"] == "xml") {
		header("Content-type: text/xml; charset=utf-8");

		echo '<?xml version="1.0"?>
		<spellchecker>
			<error>Error: The length of the text is too short.</error>
		</spellchecker>';
	} elseif (isset($_REQUEST["text"])) {
		header("Content-Type: text/html; charset=UTF-8");

		echo 'Error: The length of the text is too short.';
	} else {
		echo '
			<h2>How to use it</h2>

			<p>You must send a POST or GET request to http://spellcheckerphp.com/api/ with the follow variables:</p>

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

if (isset($_REQUEST["benchmark"])) {
	$benchmark = microtime(true) - $time;
	
	echo '<br /><p><strong>Benchmark:</strong> '. $benchmark .'</p>';
}