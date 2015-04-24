<?php
    $mysqlObj = new actionMySQL(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $mysqlObj->setCharset('utf8');
    $prsObj = new actionParse("../Document/doc.xls");
    echo ($prsObj->searchPlanAndCourseX());
    echo ($prsObj->searchDisciple());
    echo ($prsObj->searchCompetition());
    echo ($prsObj->addDiscAndComp());

    $h = $prsObj->getHoursLst();
//    $prsObj->addHourList($h, $mysqlObj);
?>
