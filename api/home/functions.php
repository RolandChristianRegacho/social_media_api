<?php
    function newPost($database, $data, $error = "normal") {
        // feature removed 
        // $post_id = idGenerator("posts", $error);
        $user = $_SESSION["id"];
        $content = $data -> content;

        //this code is obsolete
        /*
		$query = "SELECT * FROM `posts` where `id` = '$post_id'";
		$database -> query($query);

		if($database -> affected_rows > 0) {
            newPost($database, $data, "error");
		}
        else {
            $query = "INSERT INTO `posts`(`user`, `content`, `date`) VALUES (''$user','$content', NOW())";

            if($database -> query($query)) {
                return Array (
                    "type" => "success",
                    "text" => "Posted successfully!"
                );
            }
    
            return Array (
                "type" => "error",
                "text" => "Server error!"
            );
        }
        */

        $query = "INSERT INTO `posts`(`user`, `content`, `date`) VALUES ('$user','$content', NOW())";

        if($database -> query($query)) {
            return Array (
                "type" => "success",
                "text" => "Posted successfully!"
            );
        }

        return Array (
            "type" => "error",
            "text" => "Server error!"
        );
    }

    function getPosts($database) {
        $user = $_SESSION["id"];

		$query = "SELECT * FROM `posts` where `user` = '$user' order by `date` desc";	
		$result = $database -> query($query);
        $count = 0;
        $posts = array();

        while($row = $result -> fetch_assoc()) {
            $flag = false;
            $reply_count = 0;

            $user_query = "SELECT * FROM `accounts` where `id` = '$user'";	
            $results = $database -> query($user_query);
            $users = $results -> fetch_assoc();
            if($users["profile_picture"] !== null) {
                $users["profile_picture"] = 'data:image/jpeg;base64,'.base64_encode($users["profile_picture"]);
            }
            else {
                $users["profile_picture"] = getDefaultPic($database);
            }

            $reply_query = "SELECT * FROM `replies` where `post_id` = '".$row["id"]."' order by `date` desc";	
            $result_reply = $database -> query($reply_query);

            while($rows = $result_reply -> fetch_assoc()) {
                $flag = true;
            
                $reply_sender = "SELECT * FROM `accounts` where `id` = '".$rows["sender"]."'";	
                $result_sender = $database -> query($reply_sender);
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

            $posts[$count] = array(
                "user" => $users,
                "posts" => $row,
                "reply" => $flag ? $reply : "none"
            );
            $count++;
        }

        return $posts;
    }

    function sendReply($database, $data, $error = "normal") {
        // feature removed 
        // $reply_id = idGenerator("comments", $error);
        $user = $_SESSION["id"];
        $content = $data -> message;
        $post_id = $data -> post_id;

        //this code is obsolete
        /*
		$query = "SELECT * FROM `replies` where `id` = '$reply_id' order by `date` desc";
		$database -> query($query);

		if($database -> affected_rows > 0) {
            newPost($database, $data, "error");
		}
        else {
            $query = "INSERT INTO `replies`(`id`, `post_id`, `sender`, `content`, `date`) VALUES ('$reply_id', '$post_id', '$user', '$content', NOW())";

            if($database -> query($query)) {
                $reply_sender = "SELECT * FROM `accounts` where `id` = '$user'";
                $result_sender = $database -> query($reply_sender);
                $sender = $result_sender -> fetch_assoc();
                return Array (
                    "type" => "success",
                    "text" => "Replied successfully!",
                    "data" => array (
                        "name" => $_SESSION["name"],
                        "img" => "data:image/jpeg;base64,".base64_encode($sender['profile_picture'])
                    )
                );
            }
    
            return Array (
                "type" => "error",
                "text" => "Server error!"
            );
        }
        */
        
        $query = "INSERT INTO `replies`(`post_id`, `sender`, `content`, `date`) VALUES ('$post_id', '$user', '$content', NOW())";

        if($database -> query($query)) {
            $reply_sender = "SELECT * FROM `accounts` where `id` = '$user'";
            $result_sender = $database -> query($reply_sender);
            $sender = $result_sender -> fetch_assoc();
            return Array (
                "type" => "success",
                "text" => "Replied successfully!",
                "data" => array (
                    "name" => $_SESSION["name"],
                    "img" => "data:image/jpeg;base64,".base64_encode($sender['profile_picture'])
                )
            );
        }

        return Array (
            "type" => "error",
            "text" => "Server error!"
        );
    }

    // this function is obsolete
    /*
    function idGenerator($data, $type = "normal") {
        $second_part = intval(microtime(true));

        if($type == "error") {
            $second_part++;
        }

        if($data == "posts") {
            return "706f73747365" . $second_part;
        }

        if($data == "message") {
            return "6d6573736167" . $second_part;
        }

        if($data == "comments") {
            return "636f6d6d656e" . $second_part;
        }

        if($data == "image") {
            return "696d61676573" . $second_part;
        }
    }
    */

    function getDefaultPic($database) {
        $query = "SELECT * FROM `images` where `id` = '696d616765731633661054'";
        $result = $database -> query($query);
        $rows = $result -> fetch_assoc();
        return 'data:image/jpeg;base64,'.base64_encode($rows["image"]);
    }
?>