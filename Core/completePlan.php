<?php
    session_start();
    include_once 'function.php';
    include_once 'config.php';
    include_once 'actionMySQL.php';
    include_once 'actionWSP.php';
    if (isset($_SESSION['currPln']) && !empty($_SESSION['currPln'])) {
        // file exist
        (($_POST['formSt'] == 'Очная') || ($_POST['formSt'] == 'Заочная')) ? $fs = $_POST['formSt'] : die();

        // сделать проверку на существование
        $ys = $_POST['yearSt'];
        $cs = $_POST['codeSt'];
        $ss = $_POST['specSt'];
        $a = $_POST['hi'];

        $qs = $_SESSION['mainLst']['qual'];
        $ls = $_SESSION['mainLst']['limit'];
        $ps = $_SESSION['mainLst']['profile'];

        $dc = $_SESSION['dcsLst'];
        $hr = $_SESSION['hrsLst'];
        $hash = $_SESSION['hashPln'];
        switch ($a) {
            case 'accept':
                $mysqlObj = new actionMySQL(DB_HOST, DB_USER, DB_PASS, DB_NAME, SQL_LOG);
                $mysqlObj->setCharset('utf8');
                $wspObj = new actionWSP($mysqlObj);

                $idWsp = $wspObj->addWsp($fs, $cs, $ss, $qs, $ls, $ps, $ys, $hash);
                $wspObj->addDiscAndComp($dc, 1);
                $wspObj->addHourList($idWsp, $hr);
                // insert WSP
                // insert HOURs
                break;
            case 'clear':
                unlink($_SESSION['currPln']);
                (!file_exists(INP_LOG)) ? fopen(INP_LOG, 'x') : '';
                if(is_readable(INP_LOG)) {
                    $h = fopen(INP_LOG, 'w');
                    $ss = "<html><head><meta charset='utf-8'><link href='Style/style.css' rel='stylesheet'></head>\r\n";
                    fwrite($h, $ss);
                    fclose($h);
                }
                break;
            default:
                //do nothing
                break;
        }
    }

    header("Location:". RQ_DEF);
    session_destroy();