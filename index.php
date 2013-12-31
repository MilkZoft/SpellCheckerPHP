<?php
include "spellchecker.php";

$text = 'Hola Codejobers.Me gustaria compartirles este pequeño codigo,es un script que sirve para <strong>corregir</strong> las faltas de <em>ortografia</em> y de <u>redaccion</u>.';

header("Content-Type: text/html; charset=UTF-8");

echo "<strong>Cadena original:</strong> ". $text . "<br />";
echo "<strong>Cadena corregida:</strong> ". spellChecker($text) ."<br />";
echo 'No olvides visitar <a href="http://www.codejobs.biz" target="_blank">www.codejobs.biz</a>';