<?php
/**
 * Created by PhpStorm.
 * User: Никита
 * Date: 24.04.15
 * Time: 19:05
 */
    session_start();
    include_once 'function.php';

    if (isset($_GET)) {
        !empty($_GET['action']) ? $action = $_GET['action'] : $action='';
        !empty($_GET['d']) ? $doc = $_GET['d'] : $doc='';
        !empty($_GET['t']) ? $t = $_GET['t'] : $t='';

        switch($action) {
            case 'load':
                switch ($t) {
                    case 'manual':
                        break;
                    case 'auto':
                        include 'HTML/loadPlan.html';
                        break;
                    default:
                        //pprint($_POST);
                        include 'completePlan.php';
                        break;
                }
                break;
            case 'create':
                switch ($doc) {
                    case 'programm':
                        //wordTest();
                        isset($_GET['dis']) ? $_SESSION['ds'] = $_GET['dis'] : '';
                        isset($_POST['ds']) ? $_SESSION['idDs'] = substr($_POST['ds'], 2) : '';
                        $_SESSION['work'] = getWorkStudy($_SESSION['idDs'], $_SESSION['idWSP']);
                        include 'HTML/createProgram.html';
                        break;
                    case 'mtt':
                        break;
                    case 'mts':
                        break;
                    default:
                        include 'HTML/createDocuments.html';
                        break;
                }
                break;
            case 'complete':
                switch ($doc) {
                    case 'programm':
                        break;
                    case 'mtt':
                        break;
                    case 'mts':
                        break;
                    default:
                        break;
                }
            case 'test':
                include 'actionWord.php';
                functionTest();
//                wordTest();
                break;
            default:
                include 'HTML/maincontent.html';
                break;
        }
    }