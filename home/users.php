<?php
    require_once("../config.php");
    require_once("functions.php");
    
    $requestMethod = $_SERVER["REQUEST_METHOD"];

    $headers = apache_request_headers();
    $token = explode(" ", $headers["Authorization"])[1];
        
    if(strtoupper($requestMethod) == options) {
        output(json_encode(array("type" => "success")), array('Content-Type: application/json', Ok()));
    }
    if(password_verify(TOKEN, $token)) {
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
                $user_id = $_GET["user_id"];

                $params = ["s", $user_id];
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
                
                    $params = ["ss", $row["id"], $user_id];
                
                    $row["unread_count"] = countUnreadMessage(SelectExecuteStatement($con, getunreadmessagecountbyuserquery, $params));
                
                    $user_response[$count] = $row;
                    $count++;
                }
                
                $params = ["sss", $user_id, $user_id, $user_id];
                
                $format = SelectExecuteStatement($con, getfriendlistformatquery, $params);
                $format_array = array();
                $response_array = array();
                
                while($row = $format -> fetch_assoc()) {
                    array_push($format_array, array("id" => $row['user_id']));
                }
                
                $array = sortArrayByArray($format_array, $user_response);
                
                $response = array(
                    "type" => "found",
                    "data" => $array
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
                $params = ["ss", $data->sender, $data->receiver];
    
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
    
        else if(strtoupper($requestMethod) == put) {
            $request_body = file_get_contents('php://input');
            $data = json_decode($request_body);
    
            if(isset($data->user_id)) {
                $response = array();
                $params = ["ssssi", $data->first_name, $data->middle_name, $data->last_name, $data->birthday, $data->user_id];
    
                if(ExecuteStatement($con, updateprofilequery, $params)) {
                    $response = array(
                        "type" => "success",
                        "message" => "Updated successfully!"
                    );
                }
                else {
                    $response = array(
                        "type" => "error",
                        "message" => "An error occured while updating!"
                    );
                }
    
                output(json_encode($response), array('Content-Type: application/json', Ok()));
            }
            else {
                error("Page not found", NotFound());
            }
        }
    
        else {
            error("Method not supported", NotAllowed());
        }
    }
    else {
        error("Invalid Credentials", NotAuthorized());
    }
?>