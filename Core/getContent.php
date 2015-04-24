<?php
/**
 * Created by PhpStorm.
 * User: Никита
 * Date: 24.04.15
 * Time: 19:05
 */

    if (isset($_GET)) {
        !empty($_GET['action']) ? $action = $_GET['action'] : $action='';

        switch($action) {
            case 'load':
                include 'HTML/loadPlan.html';
                break;
            case 'create':
                include 'HTML/createPlan.html';
                break;
            default:
                include 'HTML/maincontent.html';
                break;
        }
    }