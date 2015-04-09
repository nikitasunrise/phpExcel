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