<?php

class actionMySQL extends mysqli {
    private $hostname, $username, $password, $dbname, $mysqli;
    private $fileLog = 'sql_log.html';

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
//class actionWord extends \PhpOffice\PhpWord\PhpWord\TemplateProcessor{
//   private $docWord;
//
//   public function __construct($document) {
//       $objWord = new \PhpOffice\PhpWord\TemplateProcessor($document);
//       if($objWord) {
//           $this->docWord = $objWord;
//       } else {
//           exit('Template processor error');
//       }
//   }
//
//   public function getDocument() {
//       return $this->docWord;
//   }
//
//}

?>