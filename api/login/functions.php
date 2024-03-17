<?php

    function checkUser() {
        if(isset($_SESSION["user"])) {
            return true;
        }

        return false;
    }

    function getUser($database) {
        if(!isset($_SESSION["user"])) {
            return Array (
                "type" => "error",
            );
        }

        $id = $_SESSION["id"];

		$query = "SELECT `first_name`, `middle_name`, `last_name`, `birthday` FROM `accounts` where `id` = '$id'";
		$result = $database -> query($query);
        $row["user"] = $result -> fetch_assoc();

		$query = "SELECT * FROM `images` where `user_id` = '$id'";
		$result = $database -> query($query);
        $row["image"] = $result -> fetch_assoc();

        if(!isset($row["image"])) {
            $query = "SELECT * FROM `images` where `id` = '696d616765731633661054'";
            $result = $database -> query($query);
            $rows = $result -> fetch_assoc();
            $row["image"] = 'data:image/jpeg;base64,'.base64_encode($rows["image"]);
        }

        $date = date("Y-M-d", strtotime($row["user"]["birthday"]));
        $splitted_date = explode("-", $date);

        $row["birthday"] = array (
            "day" => $splitted_date[2],
            "month" => $splitted_date[1],
            "year" => $splitted_date[0]
        );

        return $row;
    }

    function saveUser($database, $data_array) {
        $hashed_password = password_hash($data_array->password, PASSWORD_DEFAULT);
        // feature removed 
        // $id = idGenerator("accounts");

		$query = "SELECT * FROM `accounts` where `username` = '".$data_array->username."'";
		$database -> query($query);

		if($database -> affected_rows > 0) {
            return Array (
                "type" => "error",
                "text" => "Username already taken!"
            );
		}

        $query = "INSERT INTO `accounts`(`username`, `password`, `first_name`, `middle_name`, `last_name`, `email`, `birthday`, `date_created`) VALUES ('".$data_array -> username."','$hashed_password', '".$data_array -> firstname."', '".$data_array -> midname."', '".$data_array -> lastname."', '".$data_array -> email."', '".$data_array -> bday."', NOW())";
        if($database -> query($query)) {
            return Array (
                "type" => "success",
                "text" => "Registered successfully!"
            );
        }

        return Array (
            "type" => "error",
            "text" => "Server error!"
        );
    }

    function login($database, $credentials) {
		$query = "SELECT * FROM `accounts` where `username` = '".$credentials -> username."'";		
		$result = $database -> query($query);

		if($database -> affected_rows > 0) {
			$row = $result -> fetch_assoc();
			$id = $row["id"];
			$hash = $row["password"];
            $name = $row["first_name"]." ".$row["middle_name"]." ".$row["last_name"];

			if(password_verify($credentials -> password, $hash)) {
                $_SESSION["user"] = $credentials -> username;
                $_SESSION["id"] = $id;
                $_SESSION["name"] = $name;

                return Array (
                    "type" => "success",
                    "text" => "Login successful!",
                    "data" => array (
                        "id" => $id,
                        "first_name" => $row["first_name"],
                        "middle_name" => $row["middle_name"],
                        "last_name" => $row["last_name"]
                    )
                );
            }

            return Array (
                "type" => "error",
                "text" => "Incorrect password!"
            );
        }

        return Array (
            "type" => "error",
            "text" => "Account does not exist!"
        );
    }

    // this function is obsolete
    /*
    function idGenerator($data) {
        $second_part = intval(microtime(true));

        if($data == "accounts") {
            return "726f6c616e64" . $second_part;
        }
    }
    */
?>