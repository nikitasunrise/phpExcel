<?php
    require_once ('function.php');

	if ($countList) bprint("Count of workSheets: " . $countList . "<br>");

	$resCrs = '';
	$nArr = [   1 => 'Lection',
				2 => 'Laboratory',
				3 => 'Practice',
				4 => 'ControlWork',
				5 => 'Auditory',
				6 => 'IndepWork',
				7 => 'Study',
				8 => 'Examine',
				9 => 'Total',
				10 => 'TypeExamine'];

	$sheets = $objPHPExcel->getSheetNames();

	if (isset($sheets) && count($sheets) > 0) {
		bprint($sheets);
		bprint ("<br><br>");
		$re = "/курс[1-9]/iu";
        $re2 = "/план/iu";

		foreach ($sheets as $num => $sheet) {
			preg_match($re, $sheet, $match);
	        preg_match($re2, $sheet, $match2);

			if (isset($match) && (!empty($match))) {
				$resCrs[$num] = $sheet;
			}
            if (isset($match2) && (!empty($match2))) {
                $resPln[$num] = $sheet;
            }
		}

		if (isset($resCrs)) {
			bprint("List of courses: <br>");
			bprint($resCrs);
		}
	}

    bprint ("<hr>");

	if (isset($resCrs)) {
		foreach ($resCrs as $num => $value) {
			// active sheet
			$objPHPExcel->setActiveSheetIndex($num);
			$objActSheet = $objPHPExcel->getActiveSheet();
			//$val = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
			$cVal = $objActSheet->getCellByColumnAndRow(0, 3)->getValue();
			bprint ("1x3 content: ". $cVal);
			bprint(" [");

			if (substr($value, -1) == substr($cVal, -1)) {
				$currCrs = substr($cVal, -1);
				bprint("Course is equal");
				$ev = '';
			} else {
				bprint("Course is not equal");
			}
			bprint("] <br>");

			// find the disciples
			if (isset($ev)) {
				$hR = $objActSheet->getHighestRow();
				$hC = $objActSheet->getHighestColumn();
				$hCindex = PHPExcel_Cell::columnIndexFromString($hC);

				for ($row=1; $row <= $hR ; ++$row) {
					for ($col=0; $col <= $hCindex; ++$col) {
						$temp = $objActSheet->getCellByColumnAndRow($col, $row);
						if ($temp == 'Дисциплина') {
							$dnRow = $row;
							$dnCol = $col;
						}
					}
				}
			}
            // find the contain disciples
			if (isset($dnCol) && isset($dnRow)) {
				bprint($objActSheet->getCellByColumnAndRow($dnCol, $dnRow));
				bprint("(".$dnCol."x".$dnRow.")");
				$it = 3;
				bprint("<ul>");

				while ($objActSheet->getCellByColumnAndRow($dnCol, $dnRow+$it) != '') {
					$currDn = $objActSheet->getCellByColumnAndRow($dnCol, $dnRow+$it);
					$currBlDn = $objActSheet->getCellByColumnAndRow($dnCol-1, $dnRow+$it);

                    $currDn = (string)$currDn;
					bprint("<li>" . $currDn . "</li>");

					for ($tC=0; $tC < 2; $tC++) {
						$empty = 0;
						if ($tC == 0) bprint("Spring: ");
						if ($tC == 1) bprint("Autumn: ");

						for ($i=1; $i <= 10; $i++) {
							$k = $tC*10+$i;
							$c = $objActSheet->getCellByColumnAndRow($dnCol+$k, $dnRow+$it);
							$cVal = $c->getValue();

							if (($cVal != 0) || ($cVal != '')) {
								bprint($nArr[$i]. ": ". $c);
								$hArr[$currDn][$currCrs. $tC][$nArr[$i]] = $cVal;
								bprint("&nbsp&nbsp&nbsp");
							} elseif ($cVal == 0) {
								$hArr[$currDn][$currCrs . $tC][$nArr[$i]] = $cVal;
							}
						}
						bprint ("<br>");
					}
					bprint("<br>");
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
				bprint("</ul>");
			}

			bprint("<hr>");
		}
	}

    if (isset($resPln)) {
        foreach ($resPln as $num => $value) {
            // active sheet
            $objPHPExcel->setActiveSheetIndex($num);
            $objActSheet = $objPHPExcel->getActiveSheet();
        }
        foreach($hArr as $num => $val) {
            $dsArr[] = $num;
        }
        // unique array
        $dsArrU = array_unique($dsArr);

        $re3 = '/\S*дисципл\S*/iu';
        $re4 = '/\S*компетен\S*/iu';

        for($i = 1;$i < 5;$i++) {
            //$objActSheet
            $j = 0;
            while($j < 200) {
                $cVal = $objActSheet->getCellByColumnAndRow($j, $i)->getValue();
                $j++;
                preg_match($re3, $cVal, $match3);
                preg_match($re4, $cVal,$match4);
                if (!empty($match3)) {
                    array_push($match3, $j, $i);
                    $m3 = $match3;
//                    print_r($match3);
//                    print("<br>" . $j."-".$i . "</br>");
                }
                if (!empty($match4)) {
                    array_push($match4, $j, $i);
                    $m4 = $match4;
//                    print_r($match4);
//                    print("<br>" . $j."-".$i . "</br>");
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
                    $dVal = $objActSheet->getCellByColumnAndRow($jD - 1, $iD)->getValue();
                    $iD++;
                } while ($dVal != $value);

                if ($dVal == $value) {
//                    print ("Disciple: ".$dVal. " " .$jD."x".$iD."<br>");
                    $cVal = $objActSheet->getCellByColumnAndRow($jC - 1, $iD - 1)->getValue();
//                    print("Competition: ".$cVal." ".$jC."x".$iD."<br><br>");
                    $iD = $m3[count($m3) - 2];
                    $cVal = str_replace("\n", " ", $cVal);
                    $dcArr[$dVal] = $cVal;
                }
            }
        }
        // disciple with competition
        if (isset($dcArr)) {
            //pprint($dcArr);
            foreach($dcArr as $k => $val) {
                $st = explode(" ", $val);
                foreach($st as $k2 => $val2){
                    print($k .":". $val2);
                    echo("<br>");
                }
            }
        }

    }

	/*
	$currCrs - current course
	$currDn - current disciple
	$currBlDn - current block of disciple
	$hArr - value of all hours in term
	$dnCol - cell which contain Дисциплина
	$dnRow - row  - || -
	$dsArr - list of disciple
	$dsArrU - unique list of disciple
	$dcArr - disciple-competiton array
	*/
?>


