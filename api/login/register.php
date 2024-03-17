<?php
    require_once("../config.php");
    require_once("functions.php");

    if(isset($_GET["saveUser"])) {
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        $result = saveUser($database, $data);

        echo json_encode($result);
    }
?>