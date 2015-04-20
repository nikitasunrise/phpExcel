<?php
/**
 * Created by PhpStorm.
 * User: Никита
 * Date: 10.04.15
 * Time: 23:20
 */

require_once '../PhpWord/Autoloader.php';
\PhpOffice\PhpWord\Autoloader::register();
require_once '../PhpWord/PhpWord.php';

$phpWord = new \PhpOffice\PhpWord\PhpWord();
$doc = $phpWord->loadTemplate('');
$doc->setValue('qwe', '11');
$doc->saveAs('');
var_dump($doc);
