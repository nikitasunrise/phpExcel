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
        echo("<pre>");
        print_r($parseObj->getDcLst());
        echo("</pre>");
    ?>
	
</body>
</html>