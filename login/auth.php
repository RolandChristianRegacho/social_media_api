<?php
require_once("../config.php");

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
    
        //POST LOGIN
        if(isset($data->username) && isset($data->password)) {
            $params = ["s", $data->username];
            
            $result = SelectExecuteStatement($con, loginquery, $params);
            $flag = false;
            
            while($row = $result -> fetch_assoc()) {
                $flag = true;
                if (password_verify($data->password, $row["password"])){
                    $result = array(
                        "type" => "success",
                        "message" => "Login Success!",
                        "data" => array (
                            "id" => $row["id"],
                            "first_name" => $row["first_name"],
                            "middle_name" => $row["middle_name"],
                            "last_name" => $row["last_name"]
                        )
                    );
                    break;
                }
                //wrong password
                $result = array(
                    "type" => "error",
                    "message" => "Invalid Credentials!"
                );
                break;
            }
            //account does not exist
            if(!$flag) {
                $result = array(
                    "type" => "error",
                    "message" => "Invalid Credentials!"
                );
            }
    
            output(json_encode($result), array('Content-Type: application/json', Ok()));
        }
    
        error("Page not found", NotFound());
    }
    
    else {
        error("Method not supported", NotAllowed());
    }
}
else {
    error("Method not supported", NotAllowed());
}