<?php
require_once("../config.php");

$requestMethod = $_SERVER["REQUEST_METHOD"];

if(strtoupper($requestMethod) == get) {
    //SERVER SIDE GET METHOD FOR ACCOUNTS TO BE DISPLAYED IN A TABLE
    if(isset($_GET["page"])) {
        $page = $_GET["page"];

        $page--;
        $page *= max_page_count;

        $sql = "SELECT id, accountType, record_id, username FROM accounts WHERE is_deleted = ? ORDER BY accountType Limit $page, ".max_page_count;
        $params = ["i", 0];
        
        $result = SelectExecuteStatement($con, $sql, $params);
        $account = array();
    
        $count = 0;
        $flag = false;
    
        while($row = $result -> fetch_assoc()) {
            $flag = true;
            $name = "";

            if($row["accountType"] == 0) {
                $sql = "SELECT first_name, last_name FROM employee WHERE is_deleted = ? and id = ? Limit $page, ".max_page_count;
                $params = ["ii", 0, $row["record_id"]];
        
                $employee = SelectExecuteStatement($con, $sql, $params);

                $employee_details = $employee -> fetch_assoc();

                $name = $employee_details["first_name"] . " " . $employee_details["last_name"];
            }

            if($row["accountType"] == 1) {
                $sql = "SELECT first_name, last_name FROM teachers WHERE is_deleted = ? and id = ? Limit $page, ".max_page_count;
                $params = ["ii", 0, $row["record_id"]];
        
                $teacher = SelectExecuteStatement($con, $sql, $params);

                $teacher_details = $teacher -> fetch_assoc();

                $name = $teacher_details["first_name"] . " " . $teacher_details["last_name"];
            }

            if($row["accountType"] == 2) {
                $sql = "SELECT first, last FROM students WHERE is_deleted = ? and studentNumber = ? Limit $page, ".max_page_count;
                $params = ["ii", 0, $row["record_id"]];
        
                $student = SelectExecuteStatement($con, $sql, $params);

                $student_details = $student -> fetch_assoc();

                $name = $student_details["first"] . " " . $student_details["last"];
            }

            $accountType = "Student";

            if($row["accountType"] == 0) {
                $accountType = "Employee";
            }
            if($row["accountType"] == 1) {
                $accountType = "Teacher";
            }
    
            $account[$count] = array (
                "id" => $row["id"],
                "account_type" => $accountType,
                "name" => $name,
                "username" => $row["username"]
            );
    
            $count++;
        }
        
        $sql = "SELECT COUNT(id) AS max_count FROM accounts WHERE is_deleted = ?";
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
                "content" => $account
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
    
    error("Page not found", "HTTP/1.1 404 Not Found");
}

else if(strtoupper($requestMethod) == post) {
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body);

    //INSERT SECTIONS
    if($data->action_type == "batch") {
        if($data->group_type == "1") {
            generateAccountForAll($con);
        }
    }

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

function generateAccountForAll($con) {
    $employee_sql = "SELECT id, first_name, last_name FROM employee WHERE is_deleted = ?";
    $params = ["i", 0];

    $result = SelectExecuteStatement($con, $employee_sql, $params);

    while($employee_row = $result -> fetch_assoc()) {
        $id = $employee_row["id"];

        if(!checkIfAccountAlreadyExist($con, $id, 0)) {
            $username = strtolower($employee_row["first_name"]);
            $password = password_hash(strtolower($employee_row["last_name"]), PASSWORD_DEFAULT);

            insertNewAccount($con, $username, $password, $id, 0);
        }
    }

    $teacher_sql = "SELECT id, first_name, last_name FROM teachers WHERE is_deleted = ?";
    $params = ["i", 0];

    $result = SelectExecuteStatement($con, $teacher_sql, $params);

    while($teacher_row = $result -> fetch_assoc()) {
        $id = $teacher_row["id"];

        if(!checkIfAccountAlreadyExist($con, $id, 1)) {
            $username = strtolower($teacher_row["first_name"] . "." . $teacher_row["last_name"]);
            $password = password_hash(strtolower($teacher_row["last_name"]), PASSWORD_DEFAULT);

            insertNewAccount($con, $username, $password, $id, 1);
        }
    }

    $student_sql = "SELECT studentNumber, first, last FROM students WHERE is_deleted = ?";
    $params = ["i", 0];

    $result = SelectExecuteStatement($con, $student_sql, $params);

    while($student_row = $result -> fetch_assoc()) {
        $id = $student_row["studentNumber"];

        if(!checkIfAccountAlreadyExist($con, $id, 2)) {
            $username = strtolower($student_row["studentNumber"]);
            $password = password_hash(strtolower($student_row["first"] . "." . $student_row["last"]), PASSWORD_DEFAULT);

            insertNewAccount($con, $username, $password, $id, 2);
        }
    }
    
    $result = array(
        "type" => "success",
        "message" => "Batch upload done!"
    );

    output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
}

function checkIfAccountAlreadyExist($con, $record_id, $account_type) {
    $sql = "SELECT id FROM accounts WHERE is_deleted = ? AND record_id = ? and accountType = ?";
    $params = ["iii", 0, $record_id, $account_type];

    $result = SelectExecuteStatement($con, $sql, $params);
    $flag = false;

    while($row = $result -> fetch_assoc()) {
        $flag = true;
        break;
    }

    return $flag;
}

function insertNewAccount($con, $username, $password, $record_id, $account_type) {
    $sql = "INSERT INTO `accounts`(`username`, `password`, `record_id`, `accountType`) VALUES (?,?,?,?)";
    $params = ["ssii", $username, $password, $record_id, $account_type];

    ExecuteStatement($con, $sql, $params);
}