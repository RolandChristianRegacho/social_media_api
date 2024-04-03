<?php
    function getDefaultPic($database) {
        $result = SelectExecuteStatement($database, getdefaultpic);
        $rows = $result -> fetch_assoc();
        return 'data:image/jpeg;base64,'.base64_encode($rows["image"]);
    }

    function notifyReply($database, $sender, $post_id) {
        $params = ["s", $post_id];
        $result = SelectExecuteStatement($database, getpostuser, $params);

        $row = $result -> fetch_assoc();

        if($row["user"] != $sender) {
            $params = ["ssss", "Reply", $row["user"], $sender, $post_id];
    
            ExecuteStatement($database, createnotificationquery, $params);
        }
    }

    function deleteNotif($database, $notif_id) {
        $params = ["s", $notif_id];

        return ExecuteStatement($database, deletenotificationquery, $params);
    }

    function checkSelectStatementIfEmpty($result) {
        while($row = $result -> fetch_assoc()) {
            return false;
        }

        return true;
    }

    function countUnreadMessage($result) {
        $count = 0;
        while($row = $result -> fetch_assoc()) {
            $count++;
        }

        return $count;
    }

    function sortArrayByArray($array_to_sort, $array_format) {
        $array = array();
    
        foreach($array_to_sort as $items) {
            $counter = 0;
            foreach($array_format as $item) {
                if($items["id"] == $item["id"]) {
                    array_push($array, $item);
                    array_splice($array_format, $counter, 1);
                }
                $counter++;
            }
        }

        foreach($array_format as $items) {
            array_push($array, $items);
        }
    
        return $array;
    }
?>