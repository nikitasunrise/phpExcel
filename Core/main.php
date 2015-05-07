<?php
    include_once 'function.php';
    include_once 'config.php';
    include_once 'actionMySQL.php';
    include_once 'actionParse.php';

    if (isset($_SESSION['currPln'])) {
        $currPln = $_SESSION['currPln'];
        $prsObj = new actionParse($currPln, INP_LOG);
        $prsObj->searchMainInformation();

        $prsObj->searchPlanAndCourseX();
        $prsObj->searchDisciple();
        $prsObj->searchCompetition();

        $_SESSION['dcsLst'] = $prsObj->getDcLst();
        $_SESSION['hrsLst'] = $prsObj->getHoursLst();
        $_SESSION['mainLst'] = $prsObj->getMainLst();
    }
//$prsObj->addDiscAndComp($mysqlObj, $dsc, 1);
?>