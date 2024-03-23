<?php
    function getDefaultPic($database) {
        $query = "SELECT * FROM `images` where `id` = '1'";
        $result = $database -> query($query);
        $rows = $result -> fetch_assoc();
        return 'data:image/jpeg;base64,'.base64_encode($rows["image"]);
    }

    function notifyReply($database, $sender, $post_id) {
        $result = array();

        $query = "SELECT `user` FROM `posts` where `id` = ?";
        $params = ["s", $post_id];
        $result = SelectExecuteStatement($database, $query, $params);

        $row = $result -> fetch_assoc();

        if($row["user"] != $sender) {
            $query = "INSERT INTO `notifications`(`context`, `receiver`, `sender`, `post_id`, `date`) VALUES (?, ?, ?, ?, NOW())";
            $params = ["ssss", "Reply", $row["user"], $sender, $post_id];
    
            ExecuteStatement($database, $query, $params);
        }
    }
?>