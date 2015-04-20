<html>
<head>
	<meta content="text/html" charset="utf-8">
	<title>PHPExcel</title>
</head>
<body>
	<h1>Parse of XLS file</h1>

	<?php
        //set_time_limit(60);

        include 'Core/function.php';
        include 'Core/config.php';
        include 'Core/classes.php';
        require_once 'Core/office.php';

        $mysqlObj = new actionMySQL(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $mysqlObj->setCharset('utf8');
        $parseObj = new actionParse("Document/doc.xls");
        $parseObj->searchPlanAndCourseX();
        $parseObj->searchDisciple();
        $parseObj->searchCompetition();
        $dc = $parseObj->getDcLst();

        if(isset($mysqlObj) && (isset($parseObj))) {
            foreach($dc as $dis => $cs) {
                $st = explode(" ", $cs);
                $s1 = ['table' => 'mcd_disciple', 'what' => 'id_dis', 'exp' => 'name_dis ="'.$dis .'"'];
                $s1res = $mysqlObj->doSelectMySQL($s1);

                if(count($s1res) > 0) {
                    $id_dis = $s1res[0]['id_dis'];
                } else {
                    $i1 = ['table' => 'mcd_disciple', 'name_dis' => $dis];
                    $mysqlObj->doInsertMySQL($i1);
                }

                foreach($st as $d => $c){
                    $s2 = ['table'=>'mcd_competition', 'what' => 'id_comp', 'exp' => 'name_comp="' . $c . '"'];
                    $s2res = $mysqlObj->doSelectMySQL($s2);

                    if(count($s2res) > 0) {
                        $id_comp = $s1res[0]['id_comp'];
                    } else {
                        $i2 = ['table' => 'mcd_competition', 'name_comp' =>  $c];
                        $mysqlObj->doInsertMySQL($i2);
                    }
                }
            }
        }

//        print($parseObj->addDsCompRel('Физика', 'ОК-1', $mysqlObj));
    ?>
	
</body>
</html>