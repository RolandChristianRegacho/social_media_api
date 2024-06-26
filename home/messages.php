<?php
    require_once("../config.php");
    require_once("functions.php");
    
    $requestMethod = $_SERVER["REQUEST_METHOD"];

    $headers = apache_request_headers();
    $token = explode(" ", $headers["Authorization"])[1];
        
    if(strtoupper($requestMethod) == options) {
        output(json_encode(array("type" => "success")), array('Content-Type: application/json', Ok()));
    }
    if(password_verify(TOKEN, $token)) {
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
            else if(isset($_GET["user_id"])) {
                $response = array();
                $unread = 0;
                $flag = false;
    
                $params = ["s", $_GET["user_id"]];
    
                $result = SelectExecuteStatement($con, getunreadmessagecountquery, $params);
    
                while($row = $result -> fetch_assoc()) {
                    $unread++;
                    $flag = true;
                }
    
                if($flag) {
                    $response = array(
                        "type" => "found",
                        "unread_count" => $unread
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
    
        else if(strtoupper($requestMethod) == post) {
            $request_body = file_get_contents('php://input');
            $data = json_decode($request_body);
    
            if(isset($data->sender_id) && isset($data->receiver_id) && isset($data->content)) {
                $params = ["ssss", $data->sender_id, $data->receiver_id, $data->content, time()];
                $response = array();
    
                if(ExecuteStatement($con, createmessagequery, $params)) {
                    $response = array(
                        "type" => "success",
                        "data" => "Message sent!"
                    );
                }
                else {
                    $response = array(
                        "type" => "error",
                        "data" => "Server Error!"
                    );
                }
    
                output(json_encode($response), array('Content-Type: application/json', Ok()));
            }
            else {
                error("Page not found", NotFound());
            }
        }
    
        else if(strtoupper($requestMethod) == put) {
            $request_body = file_get_contents('php://input');
            $data = json_decode($request_body);
    
            if(isset($data->sender_id) && isset($data->receiver_id)) {
                $response = array();
                $params = ["ss", $data->sender_id, $data->receiver_id];
    
                if(ExecuteStatement($con, readmessagequery, $params)) {
                    $response = array(
                        "type" => "success",
                    );
                }
                else {
                    $response = array(
                        "type" => "error"
                    );
                }
    
                output(json_encode($response), array('Content-Type: application/json', Ok()));
            }
            else {
                error("Page not found", NotFound());
            }
        }
    
        else {
            error("Method not supported", NotAllowed());
        }
    }
    else {
        error("Invalid Credentials", NotAuthorized());
    }