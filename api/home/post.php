<?php
    require_once("../config.php");
    require_once("functions.php");
    
    $requestMethod = $_SERVER["REQUEST_METHOD"];

    if(strtoupper($requestMethod) == get) {
        if(isset($_GET["id"])) {
            try {
                $user = $_GET["id"];
        
                $query = "SELECT * FROM `posts` where `user` = ? order by `date` desc";
                $params = ["s", $user];
                $result = SelectExecuteStatement($con, $query, $params);
                //$result = $database -> query($query);
                $count = 0;
                $posts = array();
        
                while($row = $result -> fetch_assoc()) {
                    $flag = false;
                    $reply_count = 0;
        
                    $user_query = "SELECT id, profile_picture, first_name, last_name FROM `accounts` where `id` = ?";
                    $params = ["s", $user];
                    $results = SelectExecuteStatement($con, $user_query, $params);
                    //$results = $database -> query($user_query);
                    $users = $results -> fetch_assoc();
                    if($users["profile_picture"] !== null) {
                        $users["profile_picture"] = 'data:image/jpeg;base64,'.base64_encode($users["profile_picture"]);
                    }
                    else {
                        $users["profile_picture"] = getDefaultPic($database);
                    }
        
                    $reply_query = "SELECT * FROM `replies` where `post_id` = ? order by `date` desc";
                    $params = ["s", $row["id"]];
                    $result_reply = SelectExecuteStatement($con, $reply_query, $params);
                    //$result_reply = $database -> query($reply_query);
        
                    while($rows = $result_reply -> fetch_assoc()) {
                        $flag = true;
                    
                        $reply_sender = "SELECT * FROM `accounts` where `id` = ?";	
                        $params = ["s", $rows["sender"]];
                        $result_sender = SelectExecuteStatement($con, $reply_sender, $params);
                        //$result_sender = $database -> query($reply_sender);
                        $sender = $result_sender -> fetch_assoc();
        
                        if($sender["profile_picture"] !== null) {
                            $sender["profile_picture"] = 'data:image/jpeg;base64,'.base64_encode($sender["profile_picture"]);
                        }
                        else {
                            $sender["profile_picture"] = getDefaultPic($database);
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
        
                output(json_encode($posts), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
            }
            catch(Exception $e) {
                error($e, "HTTP/1.1 500 Internal Server Error");
            }
        }
    }

    else if(strtoupper($requestMethod) == post) {
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        if(isset($data->user_id) && isset($data->content)) {
            $result = array();

            $query = "INSERT INTO `posts`(`user`, `content`, `date`) VALUES (?, ?, NOW())";
            $params = ["ss", $data->user_id, $data->content];
    
            if(ExecuteStatement($con, $query, $params)) {
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
    
            output(json_encode($result), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
        }
        else {
            error("Page not found", "HTTP/1.1 404 Not Found");
        }
    }

    else if(strtoupper($requestMethod) == options) {
        output(json_encode(array("type" => "success")), array('Content-Type: application/json', "HTTP/1.1 200 OK"));
    }
?>