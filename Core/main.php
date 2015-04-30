<?php
$mysqlObj = new actionMySQL(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$mysqlObj->setCharset('utf8');

$prsObj = new actionParse("Document/doc.xls");
$prsObj->searchPlanAndCourseX();
$prsObj->searchDisciple();
$prsObj->searchCompetition();

$dsc = $prsObj->getDcLst();
$hrs = $prsObj->getHoursLst();

$prsObj->addDiscAndComp($mysqlObj, $dsc, 1);

?>
