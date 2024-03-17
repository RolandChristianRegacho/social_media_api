<?php
require_once("../config.php");

$requestMethod = $_SERVER["REQUEST_METHOD"];

if(strtoupper($requestMethod) == get) {
    $key = array_keys($_GET);

    //GET METHOD FOR GETTING ALL SECTIONS
    if(count($key) == 0 ) {
        $sql = "SELECT sectionID, section, year FROM sections WHERE is_deleted = ? ORDER BY year";
        $params = ["i", 0];
        
        $result = SelectExecuteStatement($con, $sql, $params);
        $sections = array();
    
        $count = 0;
        $flag = false;
    
        while($row = $result -> fetch_assoc()) {
            $flag = true;
    
            $sections[$count] = array (
                "id" => $row["sectionID"],
                "section" => $row["section"],
                "year" => $row["year"]
            );
    
            $count++;
        }

        $result = array(
            "type" => "success",
            "content" => $sections
        );

        output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }
    //GET METHOD FOR GETTING ALL SECTIONS BY YEAR
    if(isset($_GET["year"])) {
        $sql = "SELECT sectionID, section FROM sections WHERE year = ? AND is_deleted = ?";
        $params = ["si", $_GET["year"], 0];
        
        $result = SelectExecuteStatement($con, $sql, $params);
        $sections = array();
    
        $count = 0;
        $flag = false;
    
        while($row = $result -> fetch_assoc()) {
            $flag = true;
    
            $sections[$count] = array (
                "id" => $row["sectionID"],
                "section" => $row["section"]
            );
    
            $count++;
        }

        $result = array(
            "type" => "success",
            "content" => $sections
        );

        output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }
    //SERVER SIDE GET METHOD FOR SECTIONS TO BE DISPLAYED IN A TABLE
    if(isset($_GET["page"])) {
        $page = $_GET["page"];

        $page--;
        $page *= max_page_count;

        $sql = "SELECT sectionID, section, year FROM sections WHERE is_deleted = ? Limit $page, ".max_page_count;
        $params = ["i", 0];
        
        $result = SelectExecuteStatement($con, $sql, $params);
        $subject = array();
    
        $count = 0;
        $flag = false;
    
        while($row = $result -> fetch_assoc()) {
            $flag = true;
    
            $subject[$count] = array (
                "id" => $row["sectionID"],
                "section" => $row["section"],
                "year" => $row["year"]
            );
    
            $count++;
        }
        
        $sql = "SELECT COUNT(sectionID) AS max_count FROM sections WHERE is_deleted = ?";
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
                "length" => 3,
                "message" => "No section to display!"
            );
        }

        output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }
    //GET METHOD FOR SECTION USING SPECIFIC ID
    if(isset($_GET["id"])) {
        $id = $_GET["id"];

        $sql = "SELECT section, year FROM sections WHERE sectionID = ?";
        $params = ["i", $id];
        $section = array();
        
        $result = SelectExecuteStatement($con, $sql, $params);
        $flag = false;

        while($row = $result -> fetch_assoc()) {
            $flag = true;

            $section = array(
                "section_name" => $row["section"],
                "section_year" => $row["year"]
            );
        }

        if($flag) {
            $result = array(
                "type" => "success",
                "content" => $section
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

    //INSERT SECTIONS
    if($data->action_type == "ADD") {
        $sql = "INSERT INTO `sections`(`section`, `year`) VALUES (?,?)";
        $params = ["ss", $data->section_name, $data->section_year];

        if(ExecuteStatement($con, $sql, $params)) {
            $result = array(
                "type" => "success",
                "message" => "Section added successfully!"
            );
        }
        else {
            $result = array(
                "type" => "error",
                "message" => "An error occured while adding the section!"
            );
        }

        output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }

    error("Page not found", "HTTP/1.1 404 Not Found");
}

else if(strtoupper($requestMethod) == put) {
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body);

    //UPDATE SECTIONS
    if(isset($data->section_id)) {
        $sql = "UPDATE sections SET section = ?, year = ? WHERE sectionID = ?";
        $params = ["ssi", $data->section_name, $data->section_year, $data->section_id];

        if(ExecuteStatement($con, $sql, $params)) {
            $result = array(
                "type" => "success",
                "message" => "Section updated successfully!"
            );
        }
        else {
            $result = array(
                "type" => "error",
                "message" => "An error occured while updating the section!"
            );
        }

        output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }
    
    error("Page not found", "HTTP/1.1 404 Not Found");
}

else if(strtoupper($requestMethod) == delete) {
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body);

    //DELETE SECTIONS
    if(isset($data->id)) {
        $sql = "UPDATE sections SET is_deleted = ? WHERE sectionID = ?";
        $params = ["ii", 1, $data->id];

        if(ExecuteStatement($con, $sql, $params)) {
            $result = array(
                "type" => "success",
                "message" => "Section deleted successfully!"
            );
        }
        else {
            $result = array(
                "type" => "error",
                "message" => "An error occured while deleting the section!"
            );
        }

        output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }
    
    error("Page not found", "HTTP/1.1 404 Not Found");
}

else {
    error("Method not supported", "HTTP/1.1 405 Method Not Allowed");
}