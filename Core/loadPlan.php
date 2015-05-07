<?php
    header('Content-Type: text/html; charset=utf-8');
    // check file load
    session_start();
    $fName = basename($_FILES['filename']['name']);
    $uploadDir = '../Upload/';
    $uploadFile = $uploadDir . $fName;

    $ext = pathinfo($fName, PATHINFO_EXTENSION);

    if(($ext == 'xls' || $ext == 'xlsx') && ($_FILES['filename']['size'] < 1024*1024*5)) {
        // valid ext
        //print $ext;
        if(is_uploaded_file($_FILES['filename']['tmp_name'])) {
            move_uploaded_file($_FILES['filename']['tmp_name'], $uploadFile);
            $newFile =  md5($fName) . "." . $ext;
            rename($uploadFile, $uploadDir . $newFile);

            $_SESSION['currPln'] = $uploadDir . $newFile;
            require 'main.php';
            die();
            //print $newFile;
        } else {
            // error msg - not upload
        }
    } else {
        print 'not valid';
    }