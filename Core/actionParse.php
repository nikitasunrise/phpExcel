<?php
class actionParse {

    /**
     * @docSrc - source Excel document
     * @cntList - count list of source document
     * @objExcel - object of loaded Excel document
     * @objReader - reader's object of current Excel document
     *
     * @docSheets - list of all document's worksheet
     * @crsLst - list of sheets, contain Courses
     * @plnLst - list of sheets, contain Plan
     * @dsLst -  list of all Disciple
     * @dcLst - list of Disciple-Competition
     * @udcLst - list of pair Ds-Cp
     */
    private $docSrc, $cntList, $objExcel, $objReader;
    private $nArr = [   1 => 'Lection',
        2 => 'Laboratory',
        3 => 'Practice',
        4 => 'ControlWork',
        5 => 'Auditory',
        6 => 'IndepWork',
        7 => 'Study',
        8 => 'Examine',
        9 => 'Total',
        10 => 'TypeExamine'];
    private $hoursLst;
    protected $docSheets, $crsLst, $plnLst, $dsLst, $dcLst, $udcLst;//
    private $fileLog = 'input_log.html';

    public function __construct($document) {
        include_once('Classes/PHPExcel/IOFactory.php');
        include_once('Classes/PHPExcel.php');
        $this->docSrc = $document;

        (!file_exists($this->fileLog)) ? fopen($this->fileLog, 'x') : '';
        if(is_readable($this->fileLog)) {
            $h = fopen($this->fileLog, 'w');
            $ss = "<html><head><meta charset='utf-8'><link href='Style/style.css' rel='stylesheet'></head>\r\n";
            fwrite($h, $ss);
            fclose($h);
        }
        try {
            if (!empty($document)) {
                switch(pathinfo($document, PATHINFO_EXTENSION)) {
                    case 'xls':
                        $objR = PHPExcel_IOFactory::createReader('Excel5');
                        break;
                    case 'xlsx':
                        $objR = PHPExcel_IOFactory::createReader('Excel2007');
                        break;
                    default:
                        throw new PHPExcel_Reader_Exception;
                        break;
                }
                $objR->setReadDataOnly(true);
                $objXl = $objR->load($document);
                $cntList = $objXl->getSheetCount();

                $this->cntList = $cntList;
                $this->objReader = $objR;
                $this->objExcel = $objXl;
                $this->docSheets = $objXl->getSheetNames();
                $this->loggingWork("Расширение документа", 1);
            }
        } catch (PHPExcel_Reader_Exception $e) {
            $this->loggingWork("Расширение документа", -1);
        }
    }

    private function loggingWork($string, $status) {
        if(is_readable($this->fileLog)) {
            $h = fopen($this->fileLog, 'a');
            switch($status) {
                case 0:
                    # status empty
                    $st = '';
                    break;
                case 1:
                    $st = "<span class='scStat'>Success</span>";
                    break;
                case -1:
                    $st = "<span class='erStat'>Error</span>";
                    break;
            }
            try {
                $sS = "<p class='lg'>". $string. "... " . $st . "</p>";
                fwrite($h, $sS . "\r\n");
                fclose($h);
            } catch (Exception $e) {
                pprint($e);
            }
        }
    }

    /**
     * @return count of worksheet
     */
    public function getCntList() {
        return $this->cntList;
    }

    /**
     * @return lists of coures and
     * list with working plan
     */
    public function searchPlanAndCourseX() {
        $sheets = $this->docSheets;
        $re = "/курс[1-9]/iu";
        $re2 = "/план/iu";

        foreach ($sheets as $num => $sheet) {
            preg_match($re, $sheet, $match);
            preg_match($re2, $sheet, $match2);
            if (isset($match) && (!empty($match))) $crsLst[$num] = $sheet;
            if (isset($match2) && (!empty($match2))) $plnLst[$num] = $sheet;
        }
        if(!empty($plnLst) && (!empty($crsLst))) {

            $this->crsLst = $crsLst;
            $this->plnLst = $plnLst;
            $this->loggingWork("Поиск листов 'План' и 'Курс'", 1);
            return 1;
        } else {
            $this->loggingWork("Поиск листов 'План' и 'Курс'", -1);
            return 0;
        }
    }

    /**
     * @return list of all Ds and
     * lists of working hour in DS
     */
    public function searchDisciple() {
        $crsLst = $this->crsLst;
        $nArr = $this->nArr;
        $this->loggingWork("Поиск дисциплин", 0);
        foreach ($crsLst as $num => $value) {
            $this->objExcel->setActiveSheetIndex($num);
            $objActSh = $this->objExcel->getActiveSheet();
            $cVal = $objActSh->getCellByColumnAndRow(0, 3)->getValue();

            if (substr($value, -1) == substr($cVal, -1)) {
                $currCrs = substr($cVal, -1);
                $this->loggingWork("Текущий курс: ".$value, 1);
                //bprint("Course is equal");
                $event = 1;
            } else {
                $this->loggingWork("Текущий курс: ".$value, -1);
                //bprint("Course is not equal");
            }
            // find the disciples
            if (isset($event)) {
                $hR = $objActSh->getHighestRow();
                $hC = $objActSh->getHighestColumn();
                $hCindex = PHPExcel_Cell::columnIndexFromString($hC);

                for ($row=1; $row <= $hR ; ++$row) {
                    for ($col=0; $col <= $hCindex; ++$col) {
                        $temp = $objActSh->getCellByColumnAndRow($col, $row);
                        if ($temp == 'Дисциплина') {
                            //$this->loggingWork("Поиск ячейки 'Дисциплина'", 1);
                            $dnRow = $row;
                            $dnCol = $col;
                            break;
                        }
                    }
                }
            }
            // find the contain disciples
            if (isset($dnCol) && isset($dnRow)) {

                $it = 3;
                $objActSh->getCellByColumnAndRow($dnCol, $dnRow+$it) == '' ? $sign = -1 : $sign = 1;
                $this->loggingWork("Наличие дисцилин", $sign);

                while ($objActSh->getCellByColumnAndRow($dnCol, $dnRow+$it) != '') {
                    $currDn = $objActSh->getCellByColumnAndRow($dnCol, $dnRow+$it);
                    $currBlDn = $objActSh->getCellByColumnAndRow($dnCol-1, $dnRow+$it);
                    $currDn = (string)$currDn;
                    $currDn == '' ? $sign = -1 : $sign = 1;
                    $this->loggingWork("Дисциплина " . "<b>". $currDn . "</b>", $sign);

                    for ($tC=0; $tC < 2; $tC++) {
                        for ($i=1; $i <= 10; $i++) {
                            $k = $tC*10+$i;
                            $c = $objActSh->getCellByColumnAndRow($dnCol+$k, $dnRow+$it);
                            $cVal = $c->getValue();

                            if (($cVal != 0) || ($cVal != '')) {
                                $hArr[$currDn][$currCrs . $tC][$nArr[$i]] = $cVal;
                            } elseif ($cVal == 0) {
                                $hArr[$currDn][$currCrs . $tC][$nArr[$i]] = 0;
                            }
                        }
                    }

                    for ($tC=0; $tC < 2; $tC++) {
                        $summ=0;
                        $st=0;
                        $tot=0;
                        ($tC == 0) ? ($t = 'Осень') : ($t = 'Весна');

                        for ($i=1; $i < 5; $i++) {
                            $summ += $hArr[$currDn][$currCrs.$tC][$nArr[$i]];
                        }
                        if ($summ != $hArr[$currDn][$currCrs.$tC][$nArr[5]]) {
                            $this->loggingWork("Аудиторные часы, семестр " . $t, -1);
                        }
                        $st = $hArr[$currDn][$currCrs.$tC][$nArr[5]] + $hArr[$currDn][$currCrs.$tC][$nArr[6]];

                        if ($st != $hArr[$currDn][$currCrs.$tC][$nArr[7]]) {
                            $this->loggingWork("Часов изучено, семестр " . $t, -1);
                        }
                        $tot = $hArr[$currDn][$currCrs.$tC][$nArr[7]] + $hArr[$currDn][$currCrs.$tC][$nArr[8]];

                        if ($tot != $hArr[$currDn][$currCrs.$tC][$nArr[9]]) {
                            $this->loggingWork("Итого, семестр " . $t, -1);
                        } else {
                            $this->loggingWork("Итого, семестр " . $t, 1);
                        }
                    }
                    $it++;
                }


            }
            else {
            }

        }

        if (!empty($hArr) && isset($hArr)) {
            $this->loggingWork("Проверка часов дисциплин", 1);
            $this->hoursLst = $hArr;
            return 1;
        } else {
            $this->loggingWork("Проверка часов дисциплин", -1);
            return 0;
        }
    }

    /**
     * @return list ds-cpt
     */
    public function searchCompetition() {
        foreach ($this->plnLst as $num => $value) {
            $this->objExcel->setActiveSheetIndex($num);
            $objActSh = $this->objExcel->getActiveSheet();
            $hoursLst = $this->hoursLst;
        }
        foreach($hoursLst as $num => $val) {
            $dsArr[] = $num;
        }
        // unique array
        $dsArrU = array_unique($dsArr);
        $this->dsLst = $dsArr;

        $re3 = '/\S*дисципл\S*/iu';
        $re4 = '/\S*компетен\S*/iu';

        for($i = 1;$i < 5;$i++) {
            $j = 0;
            while($j < 200) {
                $cVal = $objActSh->getCellByColumnAndRow($j, $i)->getValue();
                $j++;
                preg_match($re3, $cVal, $match3);
                preg_match($re4, $cVal,$match4);
                if (!empty($match3)) {
                    array_push($match3, $j, $i);
                    $m3 = $match3;
                }
                if (!empty($match4)) {
                    array_push($match4, $j, $i);
                    $m4 = $match4;
                }
            }
        }
        // search competention with discliple
        $jD = $m3[count($m3) - 2];
        $iD = $m3[count($m3) - 1];
        $jC = $m4[count($m4) - 2];
        $iC = $m4[count($m4) - 1];
        $dcArr = [];

        foreach($dsArrU as $num => $value) {
            if (!empty($m3) && !empty($m4)) {
                do {
                    $dVal = $objActSh->getCellByColumnAndRow($jD - 1, $iD)->getValue();
                    $iD++;
                } while ($dVal != $value);
                if ($dVal == $value) {
//                    print ("Disciple: ".$dVal. " " .$jD."x".$iD."<br>");
                    $cVal = $objActSh->getCellByColumnAndRow($jC - 1, $iD - 1)->getValue();
//                    print("Competition: ".$cVal." ".$jC."x".$iD."<br><br>");
                    $iD = $m3[count($m3) - 2];
                    $cVal = str_replace("\n", " ", $cVal);
                    $dcArr[$dVal] = $cVal;
                }
            }
        }

        if (isset($dcArr) && (count($dcArr) > 0)) {
            $this->dcLst = $dcArr;
            return 1;
        } else return 0;
    }

    /**
     *  function of parse array with ds-cpt
     *  and implode this on two uniq part
     *  one disciple - one competition
     *
     * unused...
     */
    public function splitDsCpt() {
        // disciple with competition
        if (isset($dcLts)) {
            //pprint($dcArr);
            foreach($dcLts as $k => $val) {
                $st = explode(" ", $val);
                foreach($st as $k2 => $val2){
                    //print($k .":". $val2);
                    //echo("<br>");
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getCrsLst()
    {
        return $this->crsLst;
    }

    /**
     * @return mixed
     */
    public function getDocSheets()
    {
        return $this->docSheets;
    }

    /**
     * @return mixed
     */
    public function getDcLst()
    {
        return $this->dcLst;
    }

    /**
     * @return mixed
     */
    public function getHoursLst()
    {
        return $this->hoursLst;
    }

    /**
     * @param $disciple
     * @param $competiton
     * @param $mysqlObj
     * @return int
     */
    public function addDsCompRel($disciple, $competiton, $mysqlObj, $itsIdFlag) {
        // test connection
        if($mysqlObj) {
            // it work
            if ($itsIdFlag == 0) {
                $sc1 = ['table' => 'mcd_disciple', 'what' => 'id_dis', 'exp' => 'name_dis="' . $disciple . '"'];
                $sc2 = ['table' => 'mcd_competition', 'what' => 'id_comp', 'exp' => 'name_comp="' . $competiton . '"'];
            } elseif ($itsIdFlag == 1) {
                $sc1 = ['table' => 'mcd_disciple', 'what' => '*', 'exp' => 'id_dis="' . $disciple . '"'];
                $sc2 = ['table' => 'mcd_competition', 'what' => '*', 'exp' => 'id_comp="' . $competiton . '"'];
            }
            if(count($r1 = $mysqlObj->doSelectMySQL($sc1)) > 0) {
                $id_dis = $r1[0]['id_dis'];
            }
            if(count($r2 = $mysqlObj->doSelectMySQL($sc2)) > 0) {
                $id_comp = $r2[0]['id_comp'];
            }
            if(isset($id_dis) && isset($id_comp)) {
                $i = ['table' => 'mcd_dc', 'id_dis' => $id_dis, 'id_comp' => $id_comp];
                if($r = $mysqlObj->doInsertMySQL($i)) return 1;
                else return 0;
            }
        } else return 0;
    }

    /**
     * @param $mysqlObj
     * @param $disciples
     * @return int
     */
    public function addDiscAndComp($mysqlObj, $disciples, $relation) {
        if(isset($mysqlObj)) {
            foreach($disciples as $dis => $cs) {
                $st = explode(" ", $cs);
                $s1 = ['table' => 'mcd_disciple', 'what' => 'id_dis', 'exp' => 'name_dis ="'.$dis .'"'];
                $s1res = $mysqlObj->doSelectMySQL($s1);

                if(count($s1res) > 0) {
                    $id_dis = $s1res[0]['id_dis'];
                } else {
                    $i1 = ['table' => 'mcd_disciple', 'name_dis' => $dis];
                    $li1 = $mysqlObj->doInsertMySQL($i1);
                }
                foreach($st as $d => $c){
                    $s2 = ['table'=>'mcd_competition', 'what' => 'id_comp', 'exp' => 'name_comp="' . $c . '"'];
                    $s2res = $mysqlObj->doSelectMySQL($s2);

                    if(count($s2res) > 0) {
                        $id_comp = $s1res[0]['id_comp'];
                    } else {
                        $i2 = ['table' => 'mcd_competition', 'name_comp' =>  $c];
                        $li2 = $mysqlObj->doInsertMySQL($i2);
                    }
                    if ($relation == 1) {
                        $this->addDsCompRel($li1, $li2, $mysqlObj, 1);
                    } else {/* do nothing */ }
                }
            }
            return 1;
        } else return 0;
    }

    public function addHourList($hourList, $mysqlObj) {
        //query dis
        if (!empty($hourList) && isset($mysqlObj)) {
            foreach ($hourList as $dis => $cont) {
                $s1 = ['table' => 'mcd_disciple', 'what' => 'id_dis', 'exp' => 'name_dis ="'. $dis .'"'];
                $s1res = $mysqlObj->doSelectMySQL($s1);

                if(count($s1res) > 0) {
                    $id_dis = $s1res[0]['id_dis'];
                    foreach ($cont as $term => $hours) {
                        // 'Auditory' == 'Lection' + 'Laboratory' + 'Practice' + 'ControlWork'
                        // 'Study' == 'Auditory' + 'IndepWork'
                        // 'Total' == 'Study' + 'Examine'
                        $i1 = ['table' => 'mcd_wsp_ds',
                            'id_wsp' => '',
                            'id_ds' => '',
                            'term' => $term[1],
                            'course' => $term[0],
                            'h_lect' => $hours['Lection'],
                            'h_lab' => $hours['Laboratory'],
                            'h_pract' => $hours['Practice'],
                            'h_control' => $hours['ControlWork'],
                            'h_indep' => $hours['IndepWork'],
                            'h_exam' => $hours['Examiine'],
                            'type_exam' => $hours['TypeExamine']
                        ];
                        if ($mysqlObj->doInsertMySQL($i1)) return 1;
                        else return 0;
                    }
                } else {
                    //disciple does not exist
                    return 0;
                }
            }
        } else return 0;
        //check and add
    }

    public function getSelect($type = '') {
        echo "<option>" . "gf" . "</option>";
    }
}
