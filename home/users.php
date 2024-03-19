<?php
    require_once("../config.php");
    require_once("functions.php");
    
    $requestMethod = $_SERVER["REQUEST_METHOD"];

    if(strtoupper($requestMethod) == get) {
        if(isset($_GET["search"])) {
            $name = "%".$_GET["search"]."%";
            $sql = "SELECT `id`, `profile_picture`, `first_name`, `middle_name`, `last_name` FROM `accounts` WHERE first_name LIKE ? or middle_name LIKE ? or last_name LIKE ? ";
            $params = ["sss", $name, $name, $name];
            $response = array();
        
            $result = SelectExecuteStatement($con, $sql, $params);
            $flag = false;
            
            while($row = $result -> fetch_assoc()) {
                $flag = true;
                $response = array(
                    "type" => "found",
                    "data" => $row
                );
            }

            if($flag) {
                output(json_encode($response), array('Content-Type: application/json', Ok()));
            }
            else {
                output(json_encode(array("type" => "not found")), array('Content-Type: application/json', Ok()));
            }
        }
        else {
            error("Page not found", NotFound());
        }
    }

    else if(strtoupper($requestMethod) == options) {
        output(json_encode(array("type" => "success")), array('Content-Type: application/json', Ok()));
    }

    else {
        error("Method not supported", NotAllowed());
    }
?>