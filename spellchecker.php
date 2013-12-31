<?php
/**
 * Project: Spell Checker PHP
 * Version: 1.0
 * Author: www.codejobs.biz
 */

if(!function_exists("spellChecker")) {
	function spellChecker($text) 
	{		
		$text = fixOrthography($text);
		$text = fixSpaces($text);		
		$text = fixParenthesis($text);
		$text = fixPoints($text);
		$text = fixImagesAttributes($text);
		$text = fixTags($text);

		return $text;
	}
}

if (!function_exists("fixOrthography")) {
	function fixOrthography($text, $language = "spanish") 
	{
		$words = include "dictionaries/$language.php";
			
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
		$text = preg_replace("/<". $tag ."[^>]+\>/i", "", $text);
		$text = str_replace("<span>", "", $text);
		$text = str_replace("</span>", "", $text);
		$text = str_replace('<p>&nbsp;</p>', "", $text);
		$text = str_replace('<strong> ', "<strong>", $text);
		$text = str_replace(' </strong> ', "</strong>", $text);
		$text = str_replace('<h3><strong>', "<h3>", $text);
		$text = str_replace('</strong></h3>', "</h3>", $text);
		
		return $text;
	}
}

if (!function_exists("fixImagesAttributes")) {
	function fixImagesAttributes($text) 
	{			
		return preg_replace("/<([a-z][a-z0-9]*)(?:[^>]*(\ssrc=['\"][^'\"]*['\"]))?[^>]*?(\/?)>/i", '<$1$2$3>', $text);
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