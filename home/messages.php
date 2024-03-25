<?php
    require_once("../config.php");
    require_once("functions.php");
    
    $requestMethod = $_SERVER["REQUEST_METHOD"];

    
    if(strtoupper($requestMethod) == get) {
        if(isset($_GET["user_id"]) && isset($_GET["message_user_id"])) {
            $params = ["ssss", $_GET["user_id"], $_GET["message_user_id"], $_GET["user_id"], $_GET["message_user_id"]];

            $result = SelectExecuteStatement($con, getmessagebyuser, $params);
            $response = array();
            $message_response = array();
            $count = 0;
            $flag = false;

            while($row = $result -> fetch_assoc()) {
                $flag = true;

                $message_response[$count] = $row;
                $count++;
            }

            if($flag) {
                $response = array(
                    "type" => "found",
                    "data" => $message_response
                );
            }
            else {
                $response = array(
                    "type" => "not found"
                );
            }

            output(json_encode($response), array('Content-Type: application/json', Ok()));
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