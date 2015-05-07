<?php
    session_start();
    include_once 'function.php';
    include_once 'config.php';
    include_once 'actionMySQL.php';
    include_once 'actionWSP.php';

    pprint($_POST);
    if (isset($_SESSION['currPln']) && !empty($_SESSION['currPln'])) {
        // file exist
        (($_POST['formSt'] == 'Очная') || ($_POST['formSt'] == 'Заочная')) ? $fs = $_POST['formSt'] : die();

        // сделать проверку на существование
        $cs = $_POST['codeSt'];
        $ss = $_POST['specSt'];
        $a = $_POST['hi'];

        $qs = $_SESSION['mainLst']['qual'];
        $ls = $_SESSION['mainLst']['limit'];
        $ps = $_SESSION['mainLst']['profile'];

        $dc = $_SESSION['dcsLst'];
        $hr = $_SESSION['hrsLst'];

        switch ($a) {
            case 'accept':
                $wspObj = new actionWSP();
                $mysqlObj = new actionMySQL(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                $mysqlObj->setCharset('utf8');

                $idWsp = $wspObj->addWsp($fs, $cs, $ss, $qs, $ls, $ps);

                $wspObj->addDiscAndComp($mysqlObj, $dc, 1);

                $wspObj->addHourList($idWsp, $hr, $mysqlObj);

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

    session_destroy();