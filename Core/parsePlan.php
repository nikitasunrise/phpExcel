<?php
    include_once 'function.php';
    include_once 'config.php';
    include_once 'actionParse.php';
    if (isset($_SESSION['currPln'])) {
        $currPln = $_SESSION['currPln'];
        $prsObj = new actionParse($currPln, INP_LOG);
        print($prsObj->searchMainInformation());
        print($prsObj->searchPlanAndCourseX());
        print($prsObj->searchDisciple());
        print($prsObj->searchCompetence());

        $_SESSION['dcsLst'] = $prsObj->getDcLst();
        $_SESSION['hrsLst'] = $prsObj->getHoursLst();
        $_SESSION['mainLst'] = $prsObj->getMainLst();

        count($_SESSION['dcsLst']) > 0 ? $cntDcs = true : $cntDcs = false;
        count($_SESSION['hrsLst']) > 0 ? $cntHrs = true : $cntHrs = false;
        !empty($_SESSION['mainLst']) ? $cntMain = true : $cntMain = false;
    }
?>
<script>
    var dcs = "<?php echo $cntDcs?>";
    var hrs = "<?php echo $cntHrs?>";
    var main = "<?php echo $cntMain?>";
</script>