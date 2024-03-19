<?php
	require_once("sql/DB.php");
	require_once("Constant.php");
	require_once("output.php");
	require_once("sql/statements.php");

	//$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    session_start();
    date_default_timezone_set('Asia/Manila');

	/*
	if(isset($_SESSION["username"]) && !preg_match("/Home/", $actual_link)) {
		header("location: ./home/index.php");
	}
	if(!isset($_SESSION["username"]) && preg_match("/Home/", $actual_link)) {
		header("location: ./");
	}
	*/
?>