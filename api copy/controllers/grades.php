<?php
require_once("../config.php");

$requestMethod = $_SERVER["REQUEST_METHOD"];

if(strtoupper($requestMethod) == get) {
    $key = array_keys($_GET);

    //GET METHOD FOR GETTING THE MOST RECENT GRADES
    if(count($key) == 0 ) {
        $student_number = $_SESSION["userName"];
        $date = date("Y");

        $sql = "SELECT s.subjdesc, g.first_grading, g.second_grading, g.third_grading, g.fourth_grading, g.final_grade FROM grades g LEFT JOIN subjects s ON g.subjCode = s.subjcode WHERE g.studentNumber = ? AND g.schoolYear = ?";
        $params = ["ss", $student_number, $date];
        
        $result = SelectExecuteStatement($con, $sql, $params);
        $grades = array();
    
        $count = 0;
        $flag = false;
    
        while($row = $result -> fetch_assoc()) {
            $flag = true;
    
            $grades[$count] = array (
                "description" => $row["subjdesc"],
                "first_grading" => $row["first_grading"] != null ?: "N/A",
                "second_grading" => $row["second_grading"] != null ?: "N/A",
                "third_grading" => $row["third_grading"] != null ?: "N/A",
                "fourth_grading" => $row["fourth_grading"] != null ?: "N/A",
                "final_grade" => $row["final_grade"] != null ?: "N/A",
            );
    
            $count++;
        }
    
        if($flag) {
            $result = array(
                "type" => "success",
                "content" => $grades
            );
        }
        else {
            $result = array(
                "type" => "empty",
                "message" => "No records available for this school year!"
            );
        }

        output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }
    if(isset($_GET["year"])) {
        $student_number = $_SESSION["userName"];
        $date = $_GET["year"];

        $sql = "SELECT s.subjdesc, g.first_grading, g.second_grading, g.third_grading, g.fourth_grading, g.final_grade FROM grades g LEFT JOIN subjects s ON g.subjCode = s.subjcode WHERE g.studentNumber = ? AND g.schoolYear = ?";
        $params = ["ss", $student_number, $date];
        
        $result = SelectExecuteStatement($con, $sql, $params);
        $grades = array();
    
        $count = 0;
        $flag = false;
    
        while($row = $result -> fetch_assoc()) {
            $flag = true;
    
            $grades[$count] = array (
                "description" => $row["subjdesc"],
                "first_grading" => $row["first_grading"] != null ? $row["first_grading"] : "N/A",
                "second_grading" => $row["second_grading"] != null ? $row["second_grading"] : "N/A",
                "third_grading" => $row["third_grading"] != null ? $row["third_grading"] : "N/A",
                "fourth_grading" => $row["fourth_grading"] != null ? $row["fourth_grading"] : "N/A",
                "final_grade" => $row["final_grade"] != null ? $row["final_grade"] : "N/A",
            );
    
            $count++;
        }
    
        if($flag) {
            $result = array(
                "type" => "success",
                "content" => $grades
            );
        }
        else {
            $result = array(
                "type" => "empty",
                "message" => "No records available for this school year!"
            );
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