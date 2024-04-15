<?php
    require_once("../config.php");
    require_once("functions.php");

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Request-Headers: authorization');
    header("Access-Control-Allow-Credentials", "true");
    header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: authorization, Content-Type');
    header('Access-Control-Max-Age: 1728000');

    if(isset($_FILES["image_file"])) {
        $tmp_name = $_FILES['image_file']['tmp_name'];
    
        $fp = fopen($tmp_name, 'rb');
        $file_content = file_get_contents($tmp_name);

        $result = array();

        $params = ["ssss", $_POST["user_id"], $_POST["content"], $file_content, $_FILES["image_file"]["type"]];
        
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
    else if(isset($_FILES["image_profile"])) {
        $tmp_name = $_FILES['image_profile']['tmp_name'];
    
        $fp = fopen($tmp_name, 'rb');
        $file_content = file_get_contents($tmp_name);

        $result = array();

        $params = ["ssssssi", $_POST["first_name"], $_POST["middle_name"], $_POST["last_name"], $file_content, $_FILES["image_profile"]["type"], $_POST["birthday"], $_POST["user_id"]];
        
        if(ExecuteStatement($con, updateprofilewithprofilepicturequery, $params)) {
            $result = array(
                "type" => "success",
                "message" => "Updated successfully!"
            );
        }
        else {
            $result = array(
                "type" => "error",
                "message" => "An error occured while updating!"
            );
        }

        output(json_encode($result), array('Content-Type: application/json', Ok()));
    }
?>