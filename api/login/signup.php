<?php
    require_once("../config.php");

    $requestMethod = $_SERVER["REQUEST_METHOD"];

    if(strtoupper($requestMethod) == post) {
        $request_body = file_get_contents('php://input');
        $data_array = json_decode($request_body);
        $response = array();
        $flag = false;

        $hashed_password = password_hash($data_array->password, PASSWORD_DEFAULT);

		$query = "SELECT * FROM `accounts` where `username` = ?";
        $params = ["s", $data_array->email];
        $result = SelectExecuteStatement($con, $query, $params);

		while($row = $result -> fetch_assoc()) {
            $response = Array (
                "type" => "error",
                "text" => "Username already taken!"
            );
            $flag = true;
            break;
		}

        if(!$flag) {
            $query = "INSERT INTO `accounts`(`username`, `password`, `first_name`, `middle_name`, `last_name`, `email`, `birthday`, `date_created`) VALUES ( ?, ?, ?, ?, ?, ?, ?, NOW())";
            $params = ["sssssss", $data_array->email, $hashed_password, $data_array->first_name, $data_array->middle_name, $data_array->last_name, $data_array->email, $data_array->birthday];
    
            if(ExecuteStatement($con, $query, $params)) {
                $response = Array (
                    "type" => "success",
                    "text" => "Registered successfully!"
                );
            }
            else{

                $response = Array (
                    "type" => "error",
                    "text" => "Server error!"
                );
            }
        }

        output(json_encode($response), array('Content-Type: application/json', Ok()));
    }

    else if(strtoupper($requestMethod) == get) {
        output(json_encode(array("type" => "success")), array('Content-Type: application/json', Ok()));
    }

    else if(strtoupper($requestMethod) == options) {
        output(json_encode(array("type" => "success")), array('Content-Type: application/json', Ok()));
    }

    else {
        error("Method not supported", NotAllowed());
    }
?>