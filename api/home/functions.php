<?php
    function getDefaultPic($database) {
        $query = "SELECT * FROM `images` where `id` = '0'";
        $result = $database -> query($query);
        $rows = $result -> fetch_assoc();
        return 'data:image/jpeg;base64,'.base64_encode($rows["image"]);
    }
?>