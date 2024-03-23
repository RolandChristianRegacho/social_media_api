<?php
    require_once("../config.php");
    require_once("functions.php");
    
    $requestMethod = $_SERVER["REQUEST_METHOD"];

    if(strtoupper($requestMethod) == get) {
        if(isset($_GET["search"]) && isset($_GET["user_id"])) {
            $name = "%".$_GET["search"]."%";
            $params = ["sss", $name, $name, $name];
            $search_result = array();
            $count = 0;
        
            $result = SelectExecuteStatement($con, getuserinformationbysearchquery, $params);
            
            while($row = $result -> fetch_assoc()) {
                if($row["profile_picture"] !== null) {
                    $row["profile_picture"] = 'data:image/jpeg;base64,'.base64_encode($row["profile_picture"]);
                }
                else {
                    $row["profile_picture"] = getDefaultPic($con);
                }

                if($row["id"] != $_GET["user_id"]) {
                    $search_result[$count] = $row;
                }
                $count++;
            }

            if(count($search_result) == 0) {
                output(json_encode(array("type" => "not found")), array('Content-Type: application/json', Ok()));
            }

            $response = array(
                "type" => "found",
                "data" => $search_result
            );
            
            output(json_encode($response), array('Content-Type: application/json', Ok()));
        }
        else {
            error("Page not found", NotFound());
        }
    }

    else if(strtoupper($requestMethod) == options) {
        output(json_encode(array("type" => "success")), array('Content-Type: application/json', Ok()));
    }

    else {
        error("Method not supported", NotAllowed());
    }
?>