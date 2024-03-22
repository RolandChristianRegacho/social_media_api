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

        $query = "INSERT INTO `notifications`(`context`, `receiver`, `sender`, `date`) VALUES (?, ?, ?, NOW())";
        $params = ["sss", "Reply", $row["user"], $sender];

        ExecuteStatement($database, $query, $params);
    }
?>