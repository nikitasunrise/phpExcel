<?php
    header('Content-Type: text/html; charset=utf-8');
    // check file load
    require 'function.php';
    include 'actionMySQL.php';
    $fName = basename($_FILES['filename']['name']);
    $uploadDir = '../Document/';
    $uploadFile = $uploadDir . $fName;

    if($_FILES['filename']['size'] > 1024*1024*5) {
        // size more than 5MB
        header('Location: ');
    } else {
        pprint($_FILES);
    }

    if(is_uploaded_file($_FILES['filename']['tmp_name'])) {
        move_uploaded_file($_FILES['filename']['tmp_name'], $uploadFile);
        $newFile =  md5($fName) . "." . pathinfo($uploadFile, PATHINFO_EXTENSION);
        rename($uploadFile, $uploadDir . $newFile);
    } else {
        exit();
    }