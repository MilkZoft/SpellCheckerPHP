<?php
/**
 * Project: Spell Checker PHP
 * Version: 1.0.1
 * Author: www.codejobs.biz
 */

include "config.php";

if (!function_exists("spellChecker")) {
	function spellChecker($wrongText, $language = SCPHP_LANGUAGE) 
	{				
		$text = fixCaps($wrongText);
		$text = fixOrthography($text, $language);
		$text = fixChars($text);		
		$text = fixParenthesis($text);
		$text = fixDots($text);
		$text = fixWords($text, $language);
		
		saveText($wrongText, $text, $language);
		
		return stripslashes(ucfirst($text));
	}
}

if (!function_exists("fixCaps")) {
	function fixCaps($text) 
	{ 
		$words = explode(" ", $text);
		
		if (count($words) >= 3) {
			if (strlen($words[0]) > 3) {
				$word = $words[0];
			} elseif (strlen($words[1]) > 3) {
				$word = $words[1];
			} else {
				$word = $words[2];
			}			
		} else {
			if (strlen($words[0]) > 3) {
				$word = $words[0];
			} else {
				$word = $words[1];
			}			
		}

		$upperText = strtoupper($text);

		if ((ctype_upper($word{0}) and ctype_lower($word{1}) and ctype_upper($word{2})) or (ctype_lower($word{0}) and ctype_upper($word{1}) and ctype_lower($word{2})) or ($upperText == $text)) {		
			$text = ucfirst(strtolower($text));
		}

		return $text;
	}
}

if (!function_exists("fixOrthography")) {
	function fixOrthography($text, $language) 
	{
		try {
			$db = new PDO('mysql:host='. SCPHP_DB_HOST .';dbname='. SCPHP_DB_NAME, SCPHP_DB_USER, SCPHP_DB_PWD);
		} catch (PDOException $ex) {
		    echo $ex->getMessage();
		    exit;
		} 

		header('Content-Type: text/html; charset=UTF-8');

		die(var_dump(removeChars($text)));
		$text = fixDots($text);

		$words = array_values(array_filter(array_unique(explode(" ", removeChars($text))), function ($word) {
			return strlen(stripAccents($word)) >= 4 and !ctype_upper($word);
		}));

		$count = count($words); 

		for ($i = 0; $i < $count; $i++) {
			$words[$i] = trim($words[$i]);
			
			$query = "SELECT IncorrectWord, CorrectWord FROM sc_spanish_dictionary 
					  WHERE MATCH (IncorrectWord, CommonMistakes) AGAINST('". stripAccents($words[$i]) ."') LIMIT 1";
			
			$result = $db->query($query);
			
			if ($result) {
				$data = $result->fetch(PDO::FETCH_ASSOC);
				
				if (isset($data["CorrectWord"])) {
					$length1 = strlen($words[$i]);
					$length2 = strlen($data["CorrectWord"]);

					if (($length2 - $length1) <= 1) {
						$words[$i] = removeChars($words[$i]);
						die(var_dump($words));
						$word = (ctype_upper($words[$i]{0})) ? ucfirst($data["CorrectWord"]) : $data["CorrectWord"];
						
						$text = preg_replace("/\b". $words[$i] ."\b/", utf8_encode($word), $text);
					}
				}
			}
		}

		return $text;
	}
}

if (!function_exists("fixChars")) {
	function fixChars($text)
	{
		$text = str_replace("&nbsp; ", " ", $text);
		$text = str_replace(".&nbsp;", ". ", $text);
		$text = str_replace(" &nbsp;", " ", $text);
		$text = str_replace("  ", " ", $text);
		$text = str_replace(" . ", ". ", $text);
		$text = str_replace(",", ", ", $text);
		$text = str_replace(" , ", ", ", $text);
		$text = str_replace(" ,", ", ", $text);
		$text = str_replace("  ,", ", ", $text);
		$text = str_replace(" :", ": ", $text);
		$text = str_replace("( ", "(", $text);
		$text = str_replace(": )", " :)", $text);
		$text = str_replace("Ñ", "ñ", $text);
		$text = str_replace("ii", "i", $text);
		$text = str_replace("0o", "o", $text);
		$text = str_replace("o0", "o", $text);

		return $text;
	}
}

if (!function_exists("fixParenthesis")) {
	function fixParenthesis($text) 
	{
		$pattern = '/\( \w+/i';

		preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

		$count = count($matches);

		if ($count > 0) {
			for ($i = 0; $i < $count; $i++) {
				$mistake = $matches[$i][0];

				$fixedWord = str_replace("( ", "(", $mistake);
				
				$text = str_replace($mistake, $fixedWord, $text);
			}
		}

		$pattern = '/\w+ \)/i';

		preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

		$count = count($matches);

		if ($count > 0) {
			for ($i = 0; $i < $count; $i++) {
				$mistake = $matches[$i][0];

				$fixedWord = str_replace(" )", ")", $mistake);
				
				$text = str_replace($mistake, $fixedWord, $text);
			}
		}

		$pattern = '/\)\w+/i';

		preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

		$count = count($matches);

		if ($count > 0) {
			for ($i = 0; $i < $count; $i++) {
				$mistake = $matches[$i][0];

				$fixedWord = str_replace(")", ") ", $mistake);
				
				$text = str_replace($mistake, $fixedWord, $text);
			}
		}

		return $text;
	}
}


if (!function_exists("fixDots")) {
	function fixDots($text) 
	{
		$text = html_entity_decode($text);

		$text = preg_replace("/\.([0-9A-Za-z]{4,15})/", ". $1", $text);
		$text = str_replace("www. ", "www.", $text);
		$text = preg_replace_callback("/\.\s[a-z]/", function ($matches) {
		     return strtoupper($matches[0]);
		}, $text);

		$text = str_replace(",", ", ", $text);
		$text = str_replace(" ,", ", ", $text);
		$text = str_replace(" , ", ", ", $text);

		return $text;
	}
}

if (!function_exists("fixWords")) {
	function fixWords($text, $language) 
	{ 
		$words = include SCPHP_DICTIONARIES_PATH . $language ."_fix_words.php";
			
		foreach ($words as $incorrect => $correct) {
			$text = str_replace($incorrect, $correct, $text);
		}

		return $text;
	}
}

if (!function_exists("saveText")) {
	function saveText($wrongText, $text, $language) 
	{ 
		if (strlen($text) < 1000) {
			$txtFile = SCPHP_PATH . SCPHP_DICTIONARIES_PATH . $language ."_texts.txt";			
			$txtContent = !file_exists($txtFile) ? null : file_get_contents($txtFile);

			$found = strstr($txtContent, $wrongText);	

			if ($found === false) {
				$txtContent = "Wrong Text:\n". utf8_decode($wrongText) ."\nCorrect Text:\n". utf8_decode($text) ."\n\n";
					
				file_put_contents($txtFile, $txtContent, FILE_APPEND | LOCK_EX);
			}
		}

		return true;
	}
}

if (!function_exists("stripAccents")) {
	function stripAccents($string) 
	{ 
		$characters = array(
            "Á" => "A", "Ç" => "c", "É" => "e", "Í" => "i", "Ñ" => "n", "Ó" => "o", "Ú" => "u", "á" => "a", "ç" => "c", 
            "é" => "e", "í" => "i", "ñ" => "n", "ó" => "o", "ú" => "u", "à" => "a", "è" => "e", "ì" => "i", "ò" => "o", 
            "ù" => "u", "ã" => "a", "¿" => "", "?" =>  "", "¡" =>  "", "!" =>  "", ": " => "-"
        );                
                
        return strtr($string, $characters); 
	}
}

if (!function_exists("suggestWords")) {
	function suggestWords($text, $language = SCPHP_LANGUAGE)
	{
		if ($text != '') {
			$pattern = '/([a-zA-Z]*[ÁÉÍÓÚÑáéíóúñ][a-zA-Z]*)/';

			preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);
			
			$count = count($matches);		

			if ($count > 0) {
				for ($i = 0; $i < $count; $i++) {
					if (isset($matches[$i][0]) and isset($matches[$i + 1][0])) {
						$suggestedWords[stripAccents($matches[$i][0] . $matches[$i + 1][0])] = $matches[$i][0] . $matches[$i + 1][0];
						$i++;
					}
				}
		
				$jsonFile = SCPHP_PATH . SCPHP_DICTIONARIES_PATH . $language ."_suggested.json";
				$jsonContent = !file_exists($jsonFile) ? null : file_get_contents($jsonFile);
				$alreadySuggestedWords = (array) json_decode($jsonContent, true);			

				$jsonContent = json_encode(array_merge($alreadySuggestedWords, $suggestedWords));
				
				if (!file_exists($jsonFile)) {
					file_put_contents($jsonFile, $jsonContent, FILE_APPEND | LOCK_EX);
				} else {
					file_put_contents($jsonFile, $jsonContent);
				}
			}
		}
	}
}

if (!function_exists("removeChars")) {
	function removeChars($text) 
	{
		$text = cleanHTML($text);
		$text = str_replace('"', "", $text);
		$text = str_replace("'", "", $text);
		$text = str_replace(".", "", $text);
		$text = str_replace(",", " ", $text);
		$text = str_replace(":", "", $text);
		$text = str_replace("(", "", $text);
		$text = str_replace(")", "", $text);
		$text = str_replace("¿", "", $text);
		$text = str_replace("?", "", $text);
		$text = str_replace("[", "", $text);
		$text = str_replace("]", "", $text);
		$text = str_replace("{", "", $text);
		$text = str_replace("}", "", $text);
		$text = str_replace("-", "", $text);
		$text = str_replace("\n", " ", $text);
		$text = str_replace("  ", "", $text);
		$text = str_replace("/ppimg", "", $text);
		$text = preg_replace('/[0-9]/', '', $text);

		return $text;
	}
}

if (!function_exists("cleanHTML")) {
	function cleanHTML($text)
	{
		$text = stripslashes($text);

		$search = array(
			'@<script[^>]*?>.*?</script>@si', '@<[\/\!]*?[^<>]*?>@si', '@([\r\n])[\s]+@', '@&(quot|#34);@i', '@&(amp|#38);@i', 
			'@&(lt|#60);@i', '@&(gt|#62);@i', '@&(nbsp|#160);@i', '@&(iexcl|#161);@i', '@&(cent|#162);@i', '@&(pound|#163);@i', 
			'@&(copy|#169);@i', '@&#(\d+);@e'
		);		
		
		$replace = array('', '', '\1', '"', '&', '<', '>', ' ', chr(161), chr(162), chr(163), chr(169), 'chr(\1)');		
		
		return trim(preg_replace($search, $replace, $text));
	}
}