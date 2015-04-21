<html>
<head>
    <meta content="text/html" charset="utf-8">
    <title>PHPExcel</title>
</head>
<body>
<h1>Parse of XLS file</h1>
<?php

//include 'HTML/template.html';
include_once 'Core/function.php';
include_once 'Core/config.php';
include_once 'Core/action.php';

$prsObj = new actionParse("Document/doc.xls");

echo ($prsObj->searchPlanAndCourseX());
echo ($prsObj->searchDisciple());