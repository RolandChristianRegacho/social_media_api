<?php
require_once("../config.php");

$requestMethod = $_SERVER["REQUEST_METHOD"];

if(strtoupper($requestMethod) == post) {
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body);

    //POST LOGIN
    if(isset($data->username) && isset($data->password)) {
        $sql = "SELECT * FROM accounts WHERE username = ? ";
        $params = ["s", $data->username];
        
        $result = SelectExecuteStatement($con, $sql, $params);
        $flag = false;
        
        while($row = $result -> fetch_assoc()) {
            $flag = true;
            if (password_verify($data->password, $row["password"])){
    
                $_SESSION["userID"] = $row["id"];
                $_SESSION["userName"] = $row["username"];
                $_SESSION["userRole"] = $row["accountType"];
    
                $result = array(
                    "type" => "success",
                    "role" => $row["accountType"]
                );
                break;
            }

            $result = array(
                "type" => "error",
                "message" => "Wrong password. Try again or click Forgot password to reset it."
            );
            break;
        }
        
        if(!$flag) {
            $result = array(
                "type" => "error",
                "message" => "Account does not exist. Try again or signup an account!"
            );
        }

        output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }

    error("Page not found", "HTTP/1.1 404 Not Found");
}

else {
    error("Method not supported", "HTTP/1.1 405 Method Not Allowed");
}