<?php

class actionMySQL extends mysqli {
    private $hostname, $username, $password, $dbname, $mysqli;
    private $fileLog = '../sql_log.html';

    public function __construct($hostname, $username, $password, $dbname)
    {
        $mysqli = new mysqli($hostname, $username, $password, $dbname);
        if ($mysqli->connect_error) {
            die('Ошибка подключения (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
        }
        $this->hostname = $hostname;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->mysqli = $mysqli;

        (!file_exists($this->fileLog)) ? fopen($this->fileLog, 'x') : '';
    }

    public function setCharset($encoding) {
        $mysqli = $this->mysqli;
        if (!$mysqli->set_charset($encoding)) {
            printf("Charset load failed: %s\n", $mysqli->error);
        }
        if(is_readable($this->fileLog)) {
            $h = fopen($this->fileLog, 'w');
            $ss = "<html><head><meta charset='utf-8'></head>\r\n";
            fwrite($h, $ss);
            fclose($h);
        }
    }

    private function addLog($type, $query, $result) {
        if(is_readable($this->fileLog)) {
            $h = fopen($this->fileLog, 'a');
            switch($type) {
                case 'select':
                    if(is_array($result)) $a = count($result);
                    else $a = 0;
                    $r = "Select was return " . $a . " rows.";
                    break;
                case 'insert':
                case 'update':
                case 'delete':
                    if($result == 1) $r = "Success returned";
                    else $r = "Error returned";
                    break;
            }
            $sS = date('Y-m-d H:i:s') .": ". $query. "\r\n<br> Row result: " . $r;

            fwrite($h, $sS. "\r\n\r\n<br><br>");
            fclose($h);
        }
    }

    /**
     * @param array $tableVal
     * ('table' => 'name_table', 'what' => 'values_of_query', 'exp' => 'expression')
     * @return array
     *
     */
    public function doSelectMySQL($tableVal = array()) {
        $mysqli = $this->mysqli;
        #check function parameter
        if (!(isset($tableVal))) die("Value does not exist!");

        #generate query string with all parameter
        $query = "SELECT " . $tableVal['what'] . " FROM " . $tableVal['table'] . " WHERE " . $tableVal['exp'];

        #test print of ready query
        //echo $query . "<br>";

        #print of results row
        if ($result = $mysqli->query($query)) {
            while ($row = $result->fetch_assoc()){
                $arrData[] = $row;
                //echo "<br>";
            }
            if (isset($arrData))
            {
                $this->addLog('select', $query, $arrData);
                return $arrData;
            } else
            {
                $this->addLog('select', $query, '');
            }
        }
    }

    public function doInsertMySQL($tableVal = array()) {
        $mysqli = $this->mysqli;
        #check function parameter
        if (!(isset($tableVal))) {
            #error
            die("Value does not exist!");
        } else {
            #test print of parameter
            //print_r($tableVal);
            #unset first part of parameter - table name
            $tableName = $tableVal['table'];
            unset($tableVal['table']);
        }

        #separate fetch array on subarray for query generation
        foreach ($tableVal as $k => $v) {
            $keys[] = "`". $k . "`";
            $values[] = "'". $v . "'";
        }
        #join all part of subarray
        $strKey = implode(',', $keys);
        $strVal = implode(',', $values);
        #test print
        //echo $strKey . "<br>" . $strVal . "<br>";
        #generate query string with all parameters
        $query = "INSERT INTO " . $tableName . " (" . $strKey . ")" . " VALUES " . "(" . $strVal. ")";
        //echo $query;

        if ($result = $mysqli->query($query)) {
            $this->addLog('insert', $query, $result);
            //$mysqli->close();
            // возвращаем посл ID
            return $mysqli->insert_id;
        }
    }

    public function doUpdateMySQL($tableVal = array()) {
        $mysqli = $this->mysqli;
        #check function parameter
        if (!(isset($tableVal))) {
            #error
            die("Value does not exist!");
        } else {
            #unset first part of parameter - table name
            $tableName = $tableVal['table'];
            $tableCond = $tableVal['where'];
            unset($tableVal['table']);
            unset($tableVal['where']);
        }

        #separate fetch array on subarray for query generation
        foreach ($tableVal as $k => $v) $setVal[] = "`". $k . "`" . "=" . "'". $v . "'";

        #join all part of subarray
        $strVal = implode(",", $setVal);

        #generate query string with all parameters
        $query = "UPDATE " . $tableName . " SET " . $strVal . " WHERE " . $tableCond;
        //echo $query;
        if ($result = $mysqli->query($query)) {
            $this->addLog('update', $query, $result);
            //$mysqli->close();
            // возвращаем посл ID
            return $mysqli->insert_id;
        }
    }

    public function doDeleteMySQL($tableVal = array()) {
        $mysqli = $this->mysqli;
        #check function parameter
        if (!(isset($tableVal))) die("Value does not exist!");

        #generate query string with all parameter
        $query = "DELETE FROM " . $tableVal['table'] . " WHERE " . $tableVal['exp'];

        #test print of ready query
        //echo $query . "<br>";

        #print of results row
        if ($result = $mysqli->query($query)) {
            $this->addLog('delete', $query, $result);
            //$mysqli->close();
            return $result;
        }
    }

    public function getHostname() {
        return $this->hostname;
    }

    public function getMySQL() {
        return $this->mysqli;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getDBname() {
        return $this->dbname;
    }

    public function getListTables() {
        $mysqli = $this->mysqli;
        $query = "SHOW TABLES IN `".$this->dbname."`";
        #print of results row
        if ($result = $mysqli->query($query)) {
            while ($row = $result->fetch_row()) $arrData[] = $row;

            if (isset($arrData)) {
                for($i=0;$i<count($arrData);$i++) $res[] = $arrData[$i][0];
                return $res;
            }
        }
    }
}

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
    public  $fileLog = './input_log.html';

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
            $sS = "<p class='lg'>". $string. "... " . $st . "</p>";
            //fwrite($h, $sS. "\r\n");
            fclose($h);
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
                $this->loggingWork("Курс ".$value, 1);
                //bprint("Course is equal");
                $event = 1;
            } else {
                $this->loggingWork("Курс ".$value, -1);
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
                            $this->loggingWork("Поиск ячейки 'Дисциплина'", 1);
                            $dnRow = $row;
                            $dnCol = $col;
                            break;
                        }
                    }
                }
            }
            // find the contain disciples
            if (isset($dnCol) && isset($dnRow)) {
                //bprint($objActSh->getCellByColumnAndRow($dnCol, $dnRow));
                //bprint("(".$dnCol."x".$dnRow.")");
                $it = 3;

                while ($objActSh->getCellByColumnAndRow($dnCol, $dnRow+$it) != '') {
                    $currDn = $objActSh->getCellByColumnAndRow($dnCol, $dnRow+$it);
                    $currBlDn = $objActSh->getCellByColumnAndRow($dnCol-1, $dnRow+$it);
                    $currDn = (string)$currDn;
                    $this->loggingWork("Дисциплина " . $currDn, 1);

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

//                    $hArr[$currDn]
                    // check hour
//					for ($i=1; $i <= 5; $i++) {
//						$c = $objActSheet->getCellByColumnAndRow($dnCol+20+$i, $dnRow+$it);
//						$s = $hArr[0][$nArr[5-1+$i]] + $hArr[1][$nArr[5-1+$i]];
//
//						if ($s == $c->getValue()) {
//							bprint("Summ is right:" . $nArr[5-1+$i] . "\r\n");
//						}
//					}
                    $it++;
                }
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

    public function addDsCompRel($disciple, $competiton, $mysqlObj) {
        // test connection
        if($mysqlObj) {
            // it work
            $sc1 = ['table' => 'mcd_disciple', 'what' => 'id_dis', 'exp' => 'name_dis="' . $disciple . '"'];
            $sc2 = ['table' => 'mcd_competition', 'what' => 'id_comp', 'exp' => 'name_comp="' . $competiton . '"'];
            if(count($r1 = $mysqlObj->doSelectMySQL($sc1)) > 0) {
                // disciple unique
                $id_dis = $r1[0]['id_dis'];
                bprint($id_dis);
            }
            if(count($r2 = $mysqlObj->doSelectMySQL($sc2)) > 0) {
                // disciple unique
                $id_comp = $r2[0]['id_comp'];
                bprint($id_comp);
            }
            if(isset($id_dis) && isset($id_comp)) {
                $i = ['table' => 'mcd_dc', 'id_dis' => $id_dis, 'id_comp' => $id_comp];
                if($r = $mysqlObj->doInsertMySQL($i)) return 1;
                else return 0;
            }
        } else return 0;
    }
}
?>