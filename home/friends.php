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
        if(strtoupper($requestMethod) == post) {
            $request_body = file_get_contents('php://input');
            $data = json_decode($request_body);
    
            if(isset($data->sender_id) && isset($data->receiver_id)) {
                $response = array();
                $params = ["ss", $data->sender_id, $data->receiver_id];
    
                if(ExecuteStatement($con, createfriendquery, $params)) {
                    $params = ["ss", $data->receiver_id, $data->sender_id];
        
                    if(ExecuteStatement($con, createfriendquery, $params)) {
                        $result = SelectExecuteStatement($con, getnotificationidbysenderandreceiverquery, $params);
    
                        $row = $result -> fetch_assoc();
    
                        if(deleteNotif($con, $row["id"])) {
                            $response = array (
                                "type" => "success",
                                "message" => "Friend Accepted!"
                            );
                        }
                    }
                    else {
                        $response = array (
                            "type" => "error",
                            "message" => "Server Error!"
                        );
                    }
                }
                else {
                    $response = array (
                        "type" => "error",
                        "message" => "Server Error!"
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
?>