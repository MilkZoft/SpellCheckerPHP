<?php
/**
 * Project: Spell Checker PHP
 * Version: 1.0.1
 * Author: www.codejobs.biz
 */

define("SCPHP_LANGUAGE", "spanish");
define("SCPHP_DICTIONARIES_PATH", "dictionaries/");
define("SCPHP_URL", "http://localhost/SpellCheckerPHP/");
define("SCPHP_DIRECTORY_SEPARATOR", "/");
define('SCPHP_PATH', str_replace($_SERVER['DOCUMENT_ROOT'], $_SERVER['SERVER_NAME'] . '/', dirname(__FILE__)));