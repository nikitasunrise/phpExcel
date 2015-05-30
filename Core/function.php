<?php
/**
 * Created by PhpStorm.
 * User: Никита
 * Date: 30.03.15
 * Time: 23:15
 */

function bprint($arg) {
    if(is_array($arg)) print_r($arg);
    else print($arg);
    print("<br>");
}

function pprint($arg) {
    echo "<pre>";
    if(is_array($arg)) print_r($arg);
    else print($arg);
    echo "</pre>";
}

function showDir() {
    $listFile = 0;
    if ($handle = opendir('.')) {
        $listFile = [];
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") $listFile[] = $file;
        }
        closedir($handle);
    }
    return $listFile;
}

function flatArray($array = array()) {
    $arr2 = [];
    if (is_array($array)) {
        foreach($array as $key => $value) {
            foreach($value as $key2 => $value2) {
                //bprint($key2.'-'.$value2);
                array_push($arr2, $value2);
            }
        }
        return $arr2;
    }
}

function getSelect($type = '', $identify) {
    $arr = null;
    $ms = new actionMySQL(DB_HOST, DB_USER, DB_PASS, DB_NAME, SQL_LOG);
    $ms->setCharset('utf8');
    $event = NULL;
    switch($type) {
        case 'wsp':
            $sp = ['table' => 'mcd_wsp', 'what' => '*', 'exp' => 'WHERE TRUE'];
            if ($res = $ms->doSelectMySQL($sp)) {
                foreach ($res as $value) {
                    $arr = [
                    'wsp'.$value['id_wsp'] => $value['name_spec'].
                    ' ('.$value['code_group'].') - '.
                    $value['year_st'].', '.$value['qual_st'].', '.$value['form_st']
                    ];
                }
            }
            //$arr = ['id1' => '1', 'id2' => '2', 'id3' => '3'];
            $event = 1;
        case 'form_st':
            //$arr = ['очная', 'заочная'];
            break;
        case 'qual';
            //$arr = ['бакалавр', 'специалист'];
            break;
        case 'profile';
            //$arr = ['Программное обеспечение средств вычислителной техники',
              //      'Программная инженерия'];
            break;
        case 'disciple':
            isset($identify) ? $wsp = substr($identify, 3) : '';
            $_SESSION['idWSP'] = substr($identify, 3);
            //SELECT name_dis FROM `mcd_disciple` LEFT JOIN `mcd_wsp_ds` ON id_ds = id_dis WHERE id_wsp = 4
            $sd = ['table' => 'temp_wsp_ds', 'what' => 'DISTINCT id_dis, name, id_wsp', 'exp' => 'WHERE id_wsp = '.$wsp];
            if ($res = $ms->doSelectMySQL($sd)) {
                foreach ($res as $value) {
                    $arr['ds'.$value['id_dis']] = $value['name'];
                }
            }
            $event = 1;
            //$arr = flatArray($arr);
            break;
    }
    foreach($arr as $key => $value) {
        if ($event == 1) $str .= '<option value="'. $key. '">';
        else $str .= '<option>';
        $str .= $value;
        $str .= '</option>';
    }
    $arr = null;
    return $str;
}

function getWorkStudy($disciple, $wsp) {
    $ms = new actionMySQL(DB_HOST, DB_USER, DB_PASS, DB_NAME, SQL_LOG);
    $ms->setCharset('utf8');
    $sh = ['table' => 'mcd_wsp_ds', 'what' => '*', 'exp' => 'WHERE id_wsp = '.$wsp.' AND id_ds = '. $disciple];
    $res = $ms->doSelectMySQL($sh);
    $count = 0;
    foreach ($res as $value) {
        $audit = $value['h_lect'] + $value['h_lab'] + $value['h_pract'];
        if ($audit != 0) {
            $arr[$count]['Семестр'] = ($value['course'] * 2 - 1) + $value['trm'];
            $arr[$count]['Лекций'] = $value['h_lect'];
            $arr[$count]['Лaб.'] = $value['h_lab'];
            $arr[$count]['Практ.'] = $value['h_pract'];
            $arr[$count]['КСР'] = $value['h_control'];
            $arr[$count]['СРС'] = $value['h_indep'];
            $arr[$count]['Экзам'] = $value['h_exam'];
            $arr[$count]['Контроль'] = $value['type_exam'];
            $count++;
        }
    }
    return $arr;
}

function getWorkContent($res = array()) {
    foreach ($res as $el) {
        foreach ($el as $key => $value) {
            $str .= '<label class="leftB" style="margin-left: 10px">';
            $str .= $key . ': <u>' . $value . '</u><br>';
            $str .= '</label><br>';
        }
        $str .= '<hr>';
    }
    return $str;
}

function getPractCount($res = array(), $type = string) {
    switch ($type) {
        case 'exam':
            $examCnt = [];
            foreach ($res as $el) {
                if ($el['Контроль'] == 'Э' || $el['Контроль'] == 'ЭР') array_push($examCnt, $el['Семестр']);
            }
            $result = $examCnt;
            break;
        case 'pass':
            $passCnt = [];
            foreach ($res as $el) {
                if ($el['Контроль'] == 'З') array_push($passCnt, $el['Семестр']);
            }
            $result = $passCnt;
            break;
        case 'pract':
            $practCnt = [];
            foreach ($res as $el) {
                if ($el['Практ.'] != 0) array_push($practCnt, $el['Семестр']);
            }
            $result = $practCnt;
            break;
        case 'home':
            $homeCnt = [];
            foreach ($res as $el) {
                if ($el['СРС'] != 0) array_push($homeCnt, $el['Семестр']);
            }
            $result = $homeCnt;
        default:
            $result = null;
            break;
    }
    return $result;
}

function wordTest() {
    $header = ['Заголовок 1', 'Заголовок 2'];
    $texts = ['текст текст текст текст текст текст текст текст текст текст текст текст текст 1',
        'текст текст текст текст текст текст текст текст текст текст текст текст текст 2'];


    $word = new COM("word.application") or die("Unable to instantiate Word");
    echo "Loaded Word, version {$word->Version}\n\n";
    $word->Documents->Open(RQ_DIR . '\\Document\\PROGRAM.dot');
    $word->visible = 1;
    $bkm="item_list";

    $word->ActiveDocument->Bookmarks[$bkm]->Select();
    $sel = $word->Selection;

    $word->Selection->TypeText(iconv('utf-8', 'windows-1251', $texts[1]));
    $sel->TypeParagraph();
    $word->Selection->TypeText(iconv('utf-8', 'windows-1251', $header[1]));
    $sel->Style = $word->ActiveDocument->Styles[18];
    $sel->TypeParagraph();

    $fname="".uniqid("w").".doc";
    $word->Documents[1]->SaveAs(RQ_DIR."\\Upload\\".$fname);
    $word->Quit();
    $word = null;
    echo 'done';
}

function functionTest() {
    $header = ['Заголовок 1', 'Заголовок 2', 'Заголовок 3', 'Заголовок 4'];
    $texts = ['текст текст текст текст текст текст текст текст текст текст текст текст текст 1',
            'текст текст текст текст текст текст текст текст текст текст текст текст текст 2',
            'текст текст текст текст текст текст текст текст текст текст текст текст текст 3',
            'текст текст текст текст текст текст текст текст текст текст текст текст текст 4'];

    $word = new actionWord('word', '\\Document\\PROGRAM.dot');
    bprint($word->wordVers);
    for ($k = count($header) - 1; $k >= 0; $k--){
    }
    $word->insertHeaderAndText('item_list', $header[3], $texts[3]);
    $word->insertBreakRow('item_list');
    $word->insertHeaderAndText('item_list', $header[2], $texts[2]);


    $word->saveDocument('doc.doc');
}
