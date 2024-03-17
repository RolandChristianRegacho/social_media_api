<?php
require_once("../config.php");

$requestMethod = $_SERVER["REQUEST_METHOD"];

if(strtoupper($requestMethod) == get) {
    $key = array_keys($_GET);

    //SERVER SIDE GET METHOD FOR STUDENTS TO BE DISPLAYED IN A TABLE
    if(isset($_GET["page"])) {
        $page = $_GET["page"];

        $page--;
        $page *= max_page_count;

        $sql = "SELECT studentNumber, first, middle, last, year, section, schoolYear FROM students WHERE is_deleted = ? Limit $page, ".max_page_count;
        $params = ["i", 0];
        
        $result = SelectExecuteStatement($con, $sql, $params);
        $subject = array();
    
        $count = 0;
        $flag = false;
    
        while($row = $result -> fetch_assoc()) {
            $flag = true;
    
            $subject[$count] = array (
                "id" => $row["studentNumber"],
                "student_number" => $row["studentNumber"],
                "full_name" => $row["first"] . " " . $row["middle"] . " " . $row["last"],
                "year" => $row["year"],
                "section" => $row["section"],
                "school_year" => $row["schoolYear"]
            );
    
            $count++;
        }
        
        $sql = "SELECT COUNT(studentNumber) AS max_count FROM students WHERE is_deleted = ?";
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
                "length" => 6,
                "message" => "No student to display!"
            );
        }

        output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }
    //GET METHOD BY STUDENT_NUMBER
    if(isset($_GET["getYearLevel"])) {
        $student_number = $_SESSION["userName"];

        $sql = "SELECT first, middle, last, year, section, schoolYear FROM students WHERE studentNumber = ? ";
        $params = ["s", $student_number];
        
        $result = SelectExecuteStatement($con, $sql, $params);
        $student = array();
    
        $count = 0;
        $flag = false;
    
        while($row = $result -> fetch_assoc()) {
            $flag = true;
    
            $student = array (
                "full_name" => $row["first"] . " " . $row["middle"] . " " . $row["last"],
                "year" => $row["year"],
                "section" => $row["section"],
                "school_year" => $row["schoolYear"]
            );
    
            $count++;
        }

        if($flag) {
            $result = array(
                "type" => "success",
                "content" => $student
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

    error("Page not found", "HTTP/1.1 404 Not Found");
}

else if(strtoupper($requestMethod) == put) {
    
    error("Page not found", "HTTP/1.1 404 Not Found");
}

else if(strtoupper($requestMethod) == delete) {
    
    error("Page not found", "HTTP/1.1 404 Not Found");
}

else {
    error("Method not supported", "HTTP/1.1 405 Method Not Allowed");
}