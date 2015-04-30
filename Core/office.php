<?php
//include 'Core/actionMySQL.php';
include 'Core/function.php';
//include 'Core/config.php';

require_once 'PhpWord/Autoloader.php';
require_once 'PhpWord/PhpWord.php';
\PhpOffice\PhpWord\Autoloader::register();

$doc = new \PhpOffice\PhpWord\TemplateProcessor('Document/program.docx');
$doc->cloneBlock('ccv', 3);
$doc->saveAs('p.docx');
include 'HTML/template.html';
