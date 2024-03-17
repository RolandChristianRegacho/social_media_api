<?php
require_once("../config.php");

$requestMethod = $_SERVER["REQUEST_METHOD"];

if(strtoupper($requestMethod) == get) {
    $key = array_keys($_GET);

    //SERVER SIDE GET METHOD FOR SUBJECTS TO BE DISPLAYED IN A TABLE
    if(isset($_GET["page"])) {
        $page = $_GET["page"];

        $page--;
        $page *= max_page_count;

        $sql = "SELECT s.subjID, s.subjcode, s.subjdesc, s.year, s.teacher, t.first_name, t.last_name FROM subjects s LEFT JOIN teachers t on s.teacher = t.id WHERE s.is_deleted = ? ORDER BY s.subjID Limit $page, ".max_page_count;
        $params = ["i", 0];
        
        $result = SelectExecuteStatement($con, $sql, $params);
        $subject = array();
    
        $count = 0;
        $flag = false;
    
        while($row = $result -> fetch_assoc()) {
            $flag = true;
    
            $subject[$count] = array (
                "id" => $row["subjID"],
                "code" => $row["subjcode"],
                "description" => $row["subjdesc"],
                "year" => $row["year"],
                "teacher" => $row["first_name"] . " " . $row["last_name"],
            );
    
            $count++;
        }
        
        $sql = "SELECT COUNT(subjID) AS max_count FROM subjects WHERE is_deleted = ?";
        $result = SelectExecuteStatement($con, $sql, $params);
        $length = 0;

        while($row = $result -> fetch_assoc()) {
            $length = $row["max_count"];
        }
    
        if($flag) {
            $result = array(
                "type" => "success",
                "length" => $length,
                "content" => $subject
            );
        }
        else {
            $result = array(
                "type" => "empty",
                "length" => 5,
                "message" => "No subject to display!"
            );
        }

        output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }
    //GET METHOD FOR SUBJECTS USING SPECIFIC ID
    if(isset($_GET["id"])) {
        $id = $_GET["id"];

        $sql = "SELECT subjcode, subjdesc, year, teacher FROM subjects WHERE subjID = ?";
        $params = ["i", $id];
        $subject = array();
        
        $result = SelectExecuteStatement($con, $sql, $params);
        $flag = false;

        while($row = $result -> fetch_assoc()) {
            $flag = true;

            $subject = array(
                "code" => $row["subjcode"],
                "description" => $row["subjdesc"],
                "year" => $row["year"],
                "teacher" => $row["teacher"]
            );
        }

        if($flag) {
            $result = array(
                "type" => "success",
                "content" => $subject
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

    //INSERT SUBJECTS
    if($data->action_type == "ADD") {
        $sql = "INSERT INTO `subjects`(`subjcode`, `subjdesc`, `year`, `teacher`) VALUES (?,?,?,?)";
        $params = ["ssss", $data->subject_code, $data->subject_description, $data->subject_year, $data->subject_teacher];

        if(ExecuteStatement($con, $sql, $params)) {
            $result = array(
                "type" => "success",
                "message" => "Subject added successfully!"
            );
        }
        else {
            $result = array(
                "type" => "error",
                "message" => "An error occured while adding the subject!"
            );
        }

        output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }

    error("Page not found", "HTTP/1.1 404 Not Found");
}

else if(strtoupper($requestMethod) == put) {
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body);

    //UPDATE SUBJECTS
    if(isset($data->subject_id)) {
        $sql = "UPDATE subjects SET subjcode = ?, subjdesc = ?, year = ?, teacher = ? WHERE subjID = ?";
        $params = ["ssssi", $data->subject_code, $data->subject_description, $data->subject_year, $data->subject_teacher, $data->subject_id];

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

    //DELETE SUBJECTS
    if(isset($data->id)) {
        $sql = "UPDATE subjects SET is_deleted = ? WHERE subjID = ?";
        $params = ["ii", 1, $data->id];

        if(ExecuteStatement($con, $sql, $params)) {
            $result = array(
                "type" => "success",
                "message" => "Subject deleted successfully!"
            );
        }
        else {
            $result = array(
                "type" => "error",
                "message" => "An error occured while deleting the subject!"
            );
        }

        output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }
    
    error("Page not found", "HTTP/1.1 404 Not Found");
}

else {
    error("Method not supported", "HTTP/1.1 405 Method Not Allowed");
}