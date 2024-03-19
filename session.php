<?php
require_once("config.php");

if(isset($_SESSION["userName"])) {
    if($_SESSION["userRole"] === 0 && !preg_match("/backoffice/", $actual_link)) {
        if(preg_match("/teacher/", $actual_link) || preg_match("/student/", $actual_link)) {
            header("location: ../backoffice/?dashboard");
            return;
        }
        header("location: ./backoffice/?dashboard");
    }
    if($_SESSION["userRole"] === 1 && !preg_match("/teacher/", $actual_link)) {
        if(preg_match("/backoffice/", $actual_link) || preg_match("/student/", $actual_link)) {
            header("location: ../teacher/?dashboard");
            return;
        }
        header("location: ./teacher/?dashboard");
    }
    if($_SESSION["userRole"] === 2 && !preg_match("/student/", $actual_link)) {
        if(preg_match("/backoffice/", $actual_link) || preg_match("/teacher/", $actual_link)) {
            header("location: ../student/?dashboard");
            return;
        }
        header("location: ./student/?dashboard");
    }
}
else {
    if(preg_match("/backoffice/", $actual_link) || preg_match("/teacher/", $actual_link) || preg_match("/student/", $actual_link)) {
        header("location: ../");
    }
}