<?php
    require_once("../config.php");
    require_once("functions.php");
    
    $requestMethod = $_SERVER["REQUEST_METHOD"];

    if(strtoupper($requestMethod) == get) {
        output(json_encode(array("type" => "success")), array('Content-Type: application/json', Ok()));
    }

    else if(strtoupper($requestMethod) == post) {
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        if(isset($data->sender) && isset($data->receiver) && isset($data->content)) {
            $result = array();

            $query = "INSERT INTO `notifications`(`context`, `recipient`, `sender`, `date`) VALUES (?, ?, ?, NOW())";
            $params = ["sss", $data->content, $data->receiver, $data->sender];

            if(ExecuteStatement($con, $query, $params)) {
                $result = array(
                    "type" => "success",
                    "message" => "Sent successfully!"
                );
            }
            else {
                $result = array(
                    "type" => "error",
                    "message" => "An error occured while sending!"
                );
            }

            output(json_encode($result), array('Content-Type: application/json', Ok()));
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