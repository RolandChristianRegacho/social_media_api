<?php
require_once("../config.php");

$requestMethod = $_SERVER["REQUEST_METHOD"];

if(strtoupper($requestMethod) == get) {
    $key = array_keys($_GET);

    //GET METHOD FOR GETTING ALL TEACHERS
    if(count($key) == 0 ) {
        $sql = "SELECT id, first_name, middle_name, last_name FROM teachers WHERE is_deleted = ?";
        $params = ["i", 0];
        
        $result = SelectExecuteStatement($con, $sql, $params);
        $teachers = array();
    
        $count = 0;
        $flag = false;
    
        while($row = $result -> fetch_assoc()) {
            $flag = true;
    
            $teachers[$count] = array (
                "id" => $row["id"],
                "first_name" => $row["first_name"],
                "middle_name" => $row["middle_name"],
                "last_name" => $row["last_name"],
            );
    
            $count++;
        }
    
        if($flag) {
            $result = array(
                "type" => "success",
                "content" => $teachers
            );
        }
        else {
            $result = array(
                "type" => "empty",
                "message" => "No teacher available!"
            );
        }

        output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }
    //SERVER SIDE GET METHOD FOR TEACHERS TO BE DISPLAYED IN A TABLE
    if(isset($_GET["page"])) {
        $page = $_GET["page"];

        $page--;
        $page *= max_page_count;

        $sql = "SELECT t.id, a.username, t.first_name, t.middle_name, t.last_name FROM teachers t LEFT JOIN accounts a ON t.id = a.record_id WHERE t.is_deleted = ? and a.accountType = ? Limit $page, ".max_page_count;
        $params = ["ii", 0, 1];
        
        $result = SelectExecuteStatement($con, $sql, $params);
        $teacher = array();
    
        $count = 0;
        $flag = false;
    
        while($row = $result -> fetch_assoc()) {
            $flag = true;
    
            $teacher[$count] = array (
                "id" => $row["id"],
                "username" => $row["username"] == null ? "No account" : $row["username"],
                "full_name" => $row["first_name"] . " " . $row["middle_name"] . " " . $row["last_name"],
            );
    
            $count++;
        }
        
        $sql = "SELECT COUNT(id) AS max_count FROM teachers WHERE is_deleted = ?";
        $params = ["i", 0];

        $result = SelectExecuteStatement($con, $sql, $params);
        $length = 0;

        while($row = $result -> fetch_assoc()) {
            $length = $row["max_count"];
        }
    
        if($flag) {
            $result = array(
                "type" => "success",
                "length" => $length,
                "content" => $teacher
            );
        }
        else {
            $result = array(
                "type" => "empty",
                "length" => 3,
                "message" => "No teacher to display!"
            );
        }

        output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }
    //GET METHOD FOR TEACHERS USING SPECIFIC ID
    if(isset($_GET["id"])) {
        $id = $_GET["id"];

        $sql = "SELECT first, middle, last FROM accounts WHERE id = ?";
        $params = ["i", $id];
        $teacher = array();
        
        $result = SelectExecuteStatement($con, $sql, $params);
        $flag = false;

        while($row = $result -> fetch_assoc()) {
            $flag = true;

            $teacher = array(
                "first_name" => $row["first"],
                "middle_name" => $row["middle"],
                "last_name" => $row["last"]
            );
        }

        if($flag) {
            $result = array(
                "type" => "success",
                "content" => $teacher
            );
        }
        else {
            error("Bad Request", "HTTP/1.1 403 Bad Request");
        }

        output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }

    error("Page not found", "HTTP/1.1 404 Not Found");
}

else if(strtoupper($requestMethod) == post) {
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body);

    //INSERT TEACHERS
    if($data->action_type == "ADD") {
        if($data->middle_name == "") {
            $data->middle_name = null;
        }

        $sql = "INSERT INTO `teachers`(`first_name`, `middle_name`, `last_name`) VALUES (?,?,?)";
        $params = ["sss", $data->first_name, $data->middle_name, $data->last_name];

        if(ExecuteStatement($con, $sql, $params)) {
            $result = array(
                "type" => "success",
                "message" => "Teacher added successfully!"
            );
        }
        else {
            $result = array(
                "type" => "error",
                "message" => "An error occured while adding the teacher!"
            );
        }

        output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }

    error("Page not found", "HTTP/1.1 404 Not Found");
}

else if(strtoupper($requestMethod) == put) {
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body);

    //UPDATE TEACHERS
    if(isset($data->teacher_id)) {
        $sql = "UPDATE accounts SET first = ?, middle = ?, last = ? WHERE id = ?";
        $params = ["sssi", $data->first_name, $data->middle_name, $data->last_name, $data->teacher_id];

        if(ExecuteStatement($con, $sql, $params)) {
            $result = array(
                "type" => "success",
                "message" => "Subject updated successfully!"
            );
        }
        else {
            $result = array(
                "type" => "error",
                "message" => "An error occured while updating the subject!"
            );
        }

        output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }
    
    error("Page not found", "HTTP/1.1 404 Not Found");
}

else if(strtoupper($requestMethod) == delete) {
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body);

    //DELETE TEACHERS
    if(isset($data->id)) {
        $sql = "UPDATE accounts SET is_deleted = ? WHERE id = ?";
        $params = ["ii", 1, $data->id];

        if(ExecuteStatement($con, $sql, $params)) {
            $result = array(
                "type" => "success",
                "message" => "Teacher deleted successfully!"
            );
        }
        else {
            $result = array(
                "type" => "error",
                "message" => "An error occured while deleting the teacher!"
            );
        }

        output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }
    
    error("Page not found", "HTTP/1.1 404 Not Found");
}

else {
    error("Method not supported", "HTTP/1.1 405 Method Not Allowed");
}