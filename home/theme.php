<?php
    require_once("../config.php");
    require_once("functions.php");
    
    $requestMethod = $_SERVER["REQUEST_METHOD"];

    try {
        $headers = apache_request_headers();
        $token = explode(" ", $headers["Authorization"])[1];

        if(strtoupper($requestMethod) == options) {
            output(json_encode(array("type" => "success")), array('Content-Type: application/json', Ok()));
        }
        if(password_verify(TOKEN, $token)) {
            if(strtoupper($requestMethod) == get) {
                if(isset($_GET["user_id"])) {
                    $params = ["s", $_GET["user_id"]];
                    $response = array();
                
                    $result = SelectExecuteStatement($con, getcolorthemequery, $params);
                    $row = $result -> fetch_assoc();
        
                    $response = array(
                        "type" => "found",
                        "data" => $row
                    );
                    
                    output(json_encode($response), array('Content-Type: application/json', Ok()));
                }
                else {
                    error("Page not found", NotFound());
                }
            }
            else if(strtoupper($requestMethod) == post) {
                $request_body = file_get_contents('php://input');
                $data = json_decode($request_body);
                
                if(isset($data->user_id) && isset($data->color)) {
                    $response = array();
                    $params = ["ss", $data->color, $data->user_id];
        
                    if(ExecuteStatement($con, updatecolorthemequery, $params)) {
                        $response = array (
                            "type" => "success",
                            "message" => "Color theme changed!"
                        );
                    }
                    else {
                        $response = array (
                            "type" => "error",
                            "message" => "Server Error!"
                        );
                    }
        
                    output(json_encode($response), array('Content-Type: application/json', Ok()));
                }
            }
            else {
                error("Method not supported", NotAllowed());
            }
        }
        else {
            error("Invalid Credentials", NotAuthorized());
        }
    }
    catch(Exception $ex) {
        error("Invalid Credentials", NotAuthorized());
    }