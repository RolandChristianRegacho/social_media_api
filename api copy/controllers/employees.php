<?php
require_once("../config.php");

$requestMethod = $_SERVER["REQUEST_METHOD"];

if(strtoupper($requestMethod) == get) {
    $key = array_keys($_GET);

    //SERVER SIDE GET METHOD FOR EMPLOYEES TO BE DISPLAYED IN A TABLE
    if(isset($_GET["page"])) {
        $page = $_GET["page"];

        $page--;
        $page *= max_page_count;

        $sql = "SELECT e.id, a.username, e.first_name, e.middle_name, e.last_name FROM employee e LEFT JOIN accounts a ON e.id = a.record_id WHERE e.is_deleted = ? and a.accountType = ? Limit $page, ".max_page_count;
        $params = ["ii", 0, 0];
        
        $result = SelectExecuteStatement($con, $sql, $params);
        $employee = array();
    
        $count = 0;
        $flag = false;
    
        while($row = $result -> fetch_assoc()) {
            $flag = true;
    
            $employee[$count] = array (
                "id" => $row["id"],
                "username" => $row["username"] == null ? "No account" : $row["username"],
                "full_name" => $row["first_name"] . " " . $row["middle_name"] . " " . $row["last_name"],
            );
    
            $count++;
        }
        
        $sql = "SELECT COUNT(id) AS max_count FROM employee WHERE is_deleted = ?";
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
                "content" => $employee
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