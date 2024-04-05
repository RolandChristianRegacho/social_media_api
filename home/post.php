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
            if(isset($_GET["user_id"]) && isset($_GET["context"])) {
                try {
                    $user = $_GET["user_id"];
    
                    $params = ["s", $user];
                    $result = SelectExecuteStatement($con, getpostbyuserquery, $params);
                    $count = 0;
                    $posts = array();
            
                    while($row = $result -> fetch_assoc()) {
                        $flag = false;
                        $reply_count = 0;
                        $poster_id = $row["user"];
            
                        $params = ["s", $poster_id];
                        $results = SelectExecuteStatement($con, getuserinformationquery, $params);
    
                        $users = $results -> fetch_assoc();
                        if($users["profile_picture"] !== null) {
                            $users["profile_picture"] = 'data:image/jpeg;base64,'.base64_encode($users["profile_picture"]);
                        }
                        else {
                            $users["profile_picture"] = getDefaultPic($con);
                        }
                        
                        $params = ["s", $row["id"]];
                        $result_reply = SelectExecuteStatement($con, getreplybypostquery, $params);
    
                        $reply = [];
            
                        while($rows = $result_reply -> fetch_assoc()) {
                            $flag = true;
                        
                            $params = ["s", $rows["sender"]];
                            $result_sender = SelectExecuteStatement($con, getuserinformationquery, $params);
    
                            $sender = $result_sender -> fetch_assoc();
            
                            if($sender["profile_picture"] !== null) {
                                $sender["profile_picture"] = 'data:image/jpeg;base64,'.base64_encode($sender["profile_picture"]);
                            }
                            else {
                                $sender["profile_picture"] = getDefaultPic($con);
                            }
            
                            $reply[$reply_count] = array(
                                "reply" => $rows,
                                "sender" => $sender
                            );
                            $reply_count++;
                        }
    
                        if($flag) {
                            $posts[$count] = array(
                                "user" => $users,
                                "posts" => $row,
                                "reply" => $reply
                            );
                        }
                        else {
                            $posts[$count] = array(
                                "user" => $users,
                                "posts" => $row
                            );
                        }
                        $count++;
                    }
    
                    $post = array(
                        "type" => "success",
                        "post" => $posts
                    );
            
                    output(json_encode($post), array('Content-Type: application/json', Ok()));
                }
                catch(Exception $e) {
                    error($e, ServerError());
                }
            }
            else if(isset($_GET["user_id"])) {
                try {
                    $user = $_GET["user_id"];
    
                    $params = ["ss", $user, $user];
                    $result = SelectExecuteStatement($con, getpostfornewsfeedquery, $params);
                    $count = 0;
                    $posts = array();
                    $empty = true;
            
                    while($row = $result -> fetch_assoc()) {
                        $empty = false;
                        $flag = false;
                        $reply_count = 0;
                        $poster_id = $row["user"];
            
                        $params = ["s", $row["id"]];
                        $result_for_image = SelectExecuteStatement($con, getpostimagefornewsfeedquery, $params);

                        $post_image = $result_for_image -> fetch_assoc();
                        $row["image"] = 'data:image/jpeg;base64,'.base64_encode($post_image["image"]);
            
                        $params = ["s", $poster_id];
                        $results = SelectExecuteStatement($con, getuserinformationquery, $params);
    
                        $users = $results -> fetch_assoc();
                        if($users["profile_picture"] !== null) {
                            $users["profile_picture"] = 'data:image/jpeg;base64,'.base64_encode($users["profile_picture"]);
                        }
                        else {
                            $users["profile_picture"] = getDefaultPic($con);
                        }
                        
                        $params = ["s", $row["id"]];
                        $result_reply = SelectExecuteStatement($con, getreplybypostquery, $params);
    
                        $reply = [];
            
                        while($rows = $result_reply -> fetch_assoc()) {
                            $flag = true;
                        
                            $params = ["s", $rows["sender"]];
                            $result_sender = SelectExecuteStatement($con, getuserinformationquery, $params);
    
                            $sender = $result_sender -> fetch_assoc();
            
                            if($sender["profile_picture"] !== null) {
                                $sender["profile_picture"] = 'data:image/jpeg;base64,'.base64_encode($sender["profile_picture"]);
                            }
                            else {
                                $sender["profile_picture"] = getDefaultPic($con);
                            }
            
                            $reply[$reply_count] = array(
                                "reply" => $rows,
                                "sender" => $sender
                            );
                            $reply_count++;
                        }
    
                        if($flag) {
                            $posts[$count] = array(
                                "user" => $users,
                                "posts" => $row,
                                "reply" => $reply
                            );
                        }
                        else {
                            $posts[$count] = array(
                                "user" => $users,
                                "posts" => $row
                            );
                        }
                        $count++;
                    }
    
                    if($empty) {
                        $post = array(
                            "type" => "success",
                            "post" => $posts
                        );
                    }
                    else {
                        $post = array(
                            "type" => "success",
                            "post" => $posts
                        );
                    }
            
                    output(json_encode($post), array('Content-Type: application/json', Ok()));
                }
                catch(Exception $e) {
                    error($e, ServerError());
                }
            }
            else if(isset($_GET["post_id"])) {
                try {
                    $post_id = $_GET["post_id"];
            
                    $params = ["s", $post_id];
                    $result = SelectExecuteStatement($con, getpostbyid, $params);
                    //$result = $database -> query($query);
                    $count = 0;
                    $posts = array();
            
                    while($row = $result -> fetch_assoc()) {
                        $flag = false;
                        $reply_count = 0;
                        $poster_id = $row["user"];
            
                        $params = ["s", $poster_id];
                        $results = SelectExecuteStatement($con, getuserinformationquery, $params);
    
                        $users = $results -> fetch_assoc();
                        if($users["profile_picture"] !== null) {
                            $users["profile_picture"] = 'data:image/jpeg;base64,'.base64_encode($users["profile_picture"]);
                        }
                        else {
                            $users["profile_picture"] = getDefaultPic($con);
                        }
                        
                        $params = ["s", $row["id"]];
                        $result_reply = SelectExecuteStatement($con, getreplybypostquery, $params);
    
                        $reply = [];
            
                        while($rows = $result_reply -> fetch_assoc()) {
                            $flag = true;
                        
                            $params = ["s", $rows["sender"]];
                            $result_sender = SelectExecuteStatement($con, getuserinformationquery, $params);
    
                            $sender = $result_sender -> fetch_assoc();
            
                            if($sender["profile_picture"] !== null) {
                                $sender["profile_picture"] = 'data:image/jpeg;base64,'.base64_encode($sender["profile_picture"]);
                            }
                            else {
                                $sender["profile_picture"] = getDefaultPic($con);
                            }
            
                            $reply[$reply_count] = array(
                                "reply" => $rows,
                                "sender" => $sender
                            );
                            $reply_count++;
                        }
    
                        if($flag) {
                            $posts[$count] = array(
                                "user" => $users,
                                "posts" => $row,
                                "reply" => $reply
                            );
                        }
                        else {
                            $posts[$count] = array(
                                "user" => $users,
                                "posts" => $row
                            );
                        }
                        $count++;
                    }
    
                    $post = array(
                        "type" => "success",
                        "post" => $posts
                    );
            
                    output(json_encode($posts), array('Content-Type: application/json', Ok()));
                }
                catch(Exception $e) {
                    error($e, ServerError());
                }
            }
            else {
                error("Page not found", NotFound());
            }
        }
    
        else if(strtoupper($requestMethod) == post) {
            $request_body = file_get_contents('php://input');
            $data = json_decode($request_body);
    
            if(isset($data->user_id) && isset($data->content) && isset($data->image_file)) {
                $result = array();
                $decoded_image = base64_decode($data->image_file);
    
                $params = ["sss", $data->user_id, $data->content, $decoded_image];
        
                if(ExecuteStatement($con, createpostwithimagequery, $params)) {
                    $result = array(
                        "type" => "success",
                        "message" => "Posted successfully!"
                    );
                }
                else {
                    $result = array(
                        "type" => "error",
                        "message" => "An error occured while posting!"
                    );
                }
        
                output(json_encode($result), array('Content-Type: application/json', Ok()));
            }
            else if(isset($data->user_id) && isset($data->content)) {
                $result = array();
    
                $params = ["ss", $data->user_id, $data->content];
        
                if(ExecuteStatement($con, createpostquery, $params)) {
                    $result = array(
                        "type" => "success",
                        "message" => "Posted successfully!"
                    );
                }
                else {
                    $result = array(
                        "type" => "error",
                        "message" => "An error occured while posting!"
                    );
                }
        
                output(json_encode($result), array('Content-Type: application/json', Ok()));
            }
            else if(isset($data->user_id) && isset($data->reply)) {
                $result = array();
                $params = ["sss", $data->reply->post_id, $data->user_id, $data->reply->content];
        
                if(ExecuteStatement($con, createreplyquery, $params)) {
                    $result = Array (
                        "type" => "success",
                        "text" => "Replied successfully!"
                    );
    
                    notifyReply($con, $data->user_id, $data->reply->post_id);
        
                    output(json_encode($result), array('Content-Type: application/json', Ok()));
                }
                else {
                    $result = Array (
                        "type" => "error",
                        "text" => "Server error!"
                    );
                }
                
                output(json_encode($result), array('Content-Type: application/json', Ok()));
            }
            else {
                error("Page not found", NotFound());
            }
        }
    
        else if(strtoupper($requestMethod) == delete) {
            $request_body = file_get_contents('php://input');
            $data = json_decode($request_body);
    
            if(isset($data->post_id)) {
                $result = array();
                $params = ["s", $data->post_id];
    
                if(ExecuteStatement($con, deletepostbyidquery, $params)) {
                    $result = Array (
                        "type" => "success",
                        "text" => "Deleted successfully!"
                    );
                }
                else {
                    $result = Array (
                        "type" => "error",
                        "text" => "Server error!"
                    );
                }
    
                output(json_encode($result), array('Content-Type: application/json', Ok()));
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