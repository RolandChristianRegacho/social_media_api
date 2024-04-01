<?php
    require_once("../config.php");
    require_once("functions.php");
    
    $requestMethod = $_SERVER["REQUEST_METHOD"];

    if(strtoupper($requestMethod) == get) {
        if(isset($_GET["search"]) && isset($_GET["user_id"])) {
            $name = "%".$_GET["search"]."%";
            $params = ["sss", $name, $name, $name];
            $search_result = array();
            $count = 0;
        
            $result = SelectExecuteStatement($con, getuserinformationbysearchquery, $params);
            
            while($row = $result -> fetch_assoc()) {
                if($row["profile_picture"] !== null) {
                    $row["profile_picture"] = 'data:image/jpeg;base64,'.base64_encode($row["profile_picture"]);
                }
                else {
                    $row["profile_picture"] = getDefaultPic($con);
                }

                $params_for_status = ["is", $row["id"], $_GET["user_id"]];
                $params_for_friend_status = ["ss", $_GET["user_id"], $row["id"]];

                if(!checkSelectStatementIfEmpty(SelectExecuteStatement($con, getfriendsendertatusquery, $params_for_status))) {
                    $row["request_type"] = "sender";
                }
                else if(!checkSelectStatementIfEmpty(SelectExecuteStatement($con, getfriendreceiverstatusquery, $params_for_status))) {
                    $row["request_type"] = "receiver";
                }
                else if(!checkSelectStatementIfEmpty(SelectExecuteStatement($con, getfriendstatusquery, $params_for_friend_status))) {
                    $row["request_type"] = "friend";
                }
                else {
                    $row["request_type"] = null;
                }

                if($row["id"] != $_GET["user_id"]) {
                    $search_result[$count] = $row;
                }
                $count++;
            }

            if(count($search_result) == 0) {
                output(json_encode(array("type" => "not found")), array('Content-Type: application/json', Ok()));
            }

            $response = array(
                "type" => "found",
                "data" => $search_result
            );
            
            output(json_encode($response), array('Content-Type: application/json', Ok()));
        }
        else if(isset($_GET["profile_id"])) {
            $params = ["s", $_GET["profile_id"]];
            $response = array();
        
            $result = SelectExecuteStatement($con, getuserinformationquery, $params);
            
            while($row = $result -> fetch_assoc()) {
                if($row["profile_picture"] !== null) {
                    $row["profile_picture"] = 'data:image/jpeg;base64,'.base64_encode($row["profile_picture"]);
                }
                else {
                    $row["profile_picture"] = getDefaultPic($con);
                }

                $response = array(
                    "type" => "found",
                    "data" => $row
                );
            }
            
            output(json_encode($response), array('Content-Type: application/json', Ok()));
        }
        else if(isset($_GET["user_id"])) {
            $params = ["s", $_GET["user_id"]];
            $response = array();
            $user_response = array();
            $count = 0;
        
            $result = SelectExecuteStatement($con, getfriendlistquery, $params);
            
            while($row = $result -> fetch_assoc()) {
                if($row["profile_picture"] !== null) {
                    $row["profile_picture"] = 'data:image/jpeg;base64,'.base64_encode($row["profile_picture"]);
                }
                else {
                    $row["profile_picture"] = getDefaultPic($con);
                }

                $params = ["ss", $row["id"], $_GET["user_id"]];

                $row["unread_count"] = countUnreadMessage(SelectExecuteStatement($con, getunreadmessagecountbyuserquery, $params));

                $user_response[$count] = $row;
                $count++;
            }

            $response = array(
                "type" => "found",
                "data" => $user_response
            );
            
            output(json_encode($response), array('Content-Type: application/json', Ok()));
        }
        else {
            error("Page not found", NotFound());
        }
    }

    else if(strtoupper($requestMethod) == post) {
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        if(isset($data->sender) && isset($data->receiver) && isset($data->notification_id)) {
            $response = array();
            $params = ["s", $data->sender, $data->receiver];

            if(ExecuteStatement($con, createfriendquery, $params)) {
                if(deleteNotif($con, $data->notification_id)) {
                    $response = array (
                        "type" => "success",
                        "message" => "Friend Accepted!"
                    );
                }
                else {
                    $response = array (
                        "type" => "error",
                        "message" => "Server Error!"
                    );
                }
            }
            else {
                $response = array (
                    "type" => "error",
                    "message" => "Server Error!"
                );
            }

            output(json_encode($response), array('Content-Type: application/json', Ok()));
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