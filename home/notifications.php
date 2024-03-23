<?php
    require_once("../config.php");
    require_once("functions.php");
    
    $requestMethod = $_SERVER["REQUEST_METHOD"];

    if(strtoupper($requestMethod) == get) {
        if(isset($_GET["user_id"])) {
            $sql = "SELECT n.id, n.sender, n.receiver, n.context, n.date, n.status, a.first_name, n.post_id FROM `notifications` n LEFT JOIN `accounts` a ON n.sender = a.id  WHERE `receiver` = ? ORDER BY `date` DESC";
            $params = ["s", $_GET["user_id"]];
        
            $result = SelectExecuteStatement($con, $sql, $params);
            $response = array();
            $notification_response = array();
            $count = 0;
            $unread = 0;
            $flag = false;
            
            while($row = $result -> fetch_assoc()) {
                $flag = true;
                $response[$count] = $row;
                if($row["status"] == 0) {
                    $unread++;
                    $row["status"] = true;
                }
                else {
                    $row["status"] = false;
                }
                $count++;
            }

            if($flag) {
                $notification_response = array(
                    "type" => "found",
                    "data" => $response,
                    "unread_count" => $unread
                );
            }
            else {
                $notification_response = array(
                    "type" => "not found"
                );
            }

            output(json_encode($notification_response), array('Content-Type: application/json', Ok()));
        }
        else {
            error("Page not found", NotFound());
        }
    }

    else if(strtoupper($requestMethod) == post) {
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        if(isset($data->sender) && isset($data->receiver) && isset($data->content)) {
            $result = array();

            $query = "INSERT INTO `notifications`(`context`, `receiver`, `sender`, `date`) VALUES (?, ?, ?, NOW())";
            $params = ["sss", $data->content, $data->receiver, $data->sender];

            if(ExecuteStatement($con, $query, $params)) {
                $result = array(
                    "type" => "success",
                    "message" => "Sent successfully!"
                );
            }
            else {
                $result = array(
                    "type" => "error",
                    "message" => "An error occured while sending!"
                );
            }

            output(json_encode($result), array('Content-Type: application/json', Ok()));
        }
        
        else {
            error("Page not found", NotFound());
        }
    }

    else if(strtoupper($requestMethod) == put) {
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        if(isset($data->user_id)) {
            $result = array();

            $query = "UPDATE `notifications` SET `status`='1' WHERE `receiver` = ?;";
            $params = ["s", $data->user_id];

            if(ExecuteStatement($con, $query, $params)) {
                $result = array(
                    "type" => "success"
                );
            }
            else {
                $result = array(
                    "type" => "error"
                );
            }
    
            output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
        }

        else {
            error("Page not found", NotFound());
        }
    }

    else if(strtoupper($requestMethod) == options) {
        output(json_encode(array("type" => "success")), array('Content-Type: application/json', Ok()));
    }

    else {
        error("Method not supported", NotAllowed());
    }
?>