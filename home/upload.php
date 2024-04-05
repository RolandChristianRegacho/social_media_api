<?php
    require_once("../config.php");
    require_once("functions.php");

    if(isset($_FILES["image_file"])) {
        $tmp_name = $_FILES['image_file']['tmp_name'];
    
        $fp = fopen($tmp_name, 'rb');
        $file_content = file_get_contents($tmp_name);

        $result = array();

        $params = ["sss", $_POST["user_id"], $_POST["content"], $file_content];

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
?>