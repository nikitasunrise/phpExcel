<html>
<head>
	<meta content="text/html" charset="utf-8">
	<title>PHPExcel</title>
</head>
<body>
	<h1>Parse of XLS file</h1>

	<?php
        set_time_limit(60);

        //include 'parse.php';
        include 'function.php';
        include 'config.php';
        include 'classes.php';
        $mysqlObj = new actionMySQL(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $mysqlObj->setCharset('utf8');

        $parseObj = new actionParse("doc.xls");

        $parseObj->searchPlanAndCourseX();

        $parseObj->searchDisciple();

        $parseObj->searchCompetition();

        $dc = $parseObj->getDcLst();

        if(isset($mysqlObj) && (isset($parseObj))) {
            // comp = 1, disc = 1
            // if you want ADD ФИЗИКА - ОК1
            foreach($dc as $dis => $comp) {
                $st = explode(" ", $comp);
                bprint($dis ." : ". $comp);
                // select on disciple
                // new function
//                function ()
                $s1 = ['table' => 'mcd_disciple', 'what' => 'id_dis', 'exp' => 'name_dis ="'.$dis .'"'];
                $sRes = $mysqlObj->doSelectMySQL($s1);
                if(count($sRes) == 1) {
                    $sRes[0]['id_dis'];
                        foreach($st as $k2 => $val2){
                            print($dis .":". $val2);
                            echo("<br>");
                        }
                    pprint($sRes);
                } else {

                }
            }
        }
    ?>
	
</body>
</html>