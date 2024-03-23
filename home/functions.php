<?php
    function getDefaultPic($database) {
        $result = SelectExecuteStatement($database, getdefaultpic);
        $rows = $result -> fetch_assoc();
        return 'data:image/jpeg;base64,'.base64_encode($rows["image"]);
    }

    function notifyReply($database, $sender, $post_id) {
        $result = array();

        $params = ["s", $post_id];
        $result = SelectExecuteStatement($database, getpostuser, $params);

        $row = $result -> fetch_assoc();

        if($row["user"] != $sender) {
            $params = ["ssss", "Reply", $row["user"], $sender, $post_id];
    
            ExecuteStatement($database, createnotificationquery, $params);
        }
    }
?>