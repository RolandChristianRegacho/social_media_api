<?php
    require_once("../config.php");

    $requestMethod = $_SERVER["REQUEST_METHOD"];

    if(strtoupper($requestMethod) == post) {
        $request_body = file_get_contents('php://input');
        $data_array = json_decode($request_body);
        $result = array();

        $hashed_password = password_hash($data_array->password, PASSWORD_DEFAULT);

		$query = "SELECT * FROM `accounts` where `username` = '".$data_array->username."'";
		$database -> query($query);

		if($database -> affected_rows > 0) {
            $result = Array (
                "type" => "error",
                "text" => "Username already taken!"
            );
		}

        $query = "INSERT INTO `accounts`(`username`, `password`, `first_name`, `middle_name`, `last_name`, `email`, `birthday`, `date_created`) VALUES ('".$data_array -> username."','$hashed_password', '".$data_array -> firstname."', '".$data_array -> midname."', '".$data_array -> lastname."', '".$data_array -> email."', '".$data_array -> birthday."', NOW())";
        if($database -> query($query)) {
            $result = Array (
                "type" => "success",
                "text" => "Registered successfully!"
            );
        }

        $result = Array (
            "type" => "error",
            "text" => "Server error!"
        );

        output(json_encode($result), array('Content-Type: application/json', Ok()));
    }

    else if(strtoupper($requestMethod) == options) {
        output(json_encode(array("type" => "success")), array('Content-Type: application/json', Ok()));
    }

    else {
        error("Method not supported", NotAllowed());
    }
?>