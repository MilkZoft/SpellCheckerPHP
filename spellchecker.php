<?php
/**
 * Project: Spell Checker PHP
 * Version: 1.0.1
 * Author: www.codejobs.biz
 */

include "config.php";

if (!function_exists("spellChecker")) {
	function spellChecker($text, $language = SCPHP_LANGUAGE) 
	{		
		$text = fixOrthography($text, $language);
		$text = fixCaps($text);
		$text = fixSpaces($text);		
		$text = fixParenthesis($text);
		$text = fixPoints($text);
		$text = fixTags($text);

		return $text;
	}
}

if (!function_exists("fixCaps")) {
	function fixCaps($text) 
	{ 
		$matches = preg_match('#^[A-Z]+$#', $text);
		die(var_dump($matches));
		if (preg_match('#^[A-Z]+$#', $text)) {
			$text = ucfirst(strtolower($text));
			$text = preg_replace_callback('/[.!?].*?\w/', create_function('$matches', 'return strtoupper($matches[0]);'), $text);
		} 

		return $text;
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
		$pattern = '/([a-zA-Z]*[ÁÉÍÓÚáéíóú][a-zA-Z]*)/';

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

if (!function_exists("fixOrthography")) {
	function fixOrthography($text, $language) 
	{
		$words = include SCPHP_DICTIONARIES_PATH . $language .".php";
			
		return preg_replace(array_keys($words), array_values($words), $text);
	}
}

if (!function_exists("fixSpaces")) {
	function fixSpaces($text)
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

		return $text;
	}
}

if (!function_exists("fixPoints")) {
	function fixPoints($text)
	{
		$text{0} = strtoupper($text{0});

		$pattern = '/\.\w+ /i';

		preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

		$count = count($matches);

		if ($count > 0) {
			for ($i = 0; $i < $count; $i++) {
				$mistake = $fixedWord = $matches[$i][0];
				$fixedWord{1} = strtoupper($fixedWord{1});

				$fixedWord = str_replace(".", ". ", $fixedWord);
				
				$text = str_replace($mistake, $fixedWord, $text);
			}
		}

		return $text;
	}
}

if (!function_exists("fixTags")) {
	function fixTags($text, $tag = "span") 
	{
		$text = str_replace("<span>", "", $text);
		$text = str_replace("</span>", "", $text);
		$text = str_replace('<p>&nbsp;</p>', "", $text);
		$text = str_replace('<strong> ', "<strong>", $text);
		$text = str_replace(' </strong> ', "</strong>", $text);
		$text = str_replace('<h3><strong>', "<h3>", $text);
		$text = str_replace('</strong></h3>', "</h3>", $text);
		$text = str_replace("<div>&nbsp;</div>", '<div style="page-break-after: always;"><span style="display: none;">&nbsp;</span></div>', $text);
		
		return $text;
	}
}

if (!function_exists("fixParenthesis")) {
	function fixParenthesis($text) 
	{
		$pattern = '/\w+\(/i';

		preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

		$count = count($matches);

		if ($count > 0) {
			for ($i = 0; $i < $count; $i++) {
				$mistake = $matches[$i][0];

				$fixedWord = str_replace("(", " (", $mistake);
				
				$text = str_replace($mistake, $fixedWord, $text);
			}
		}

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
