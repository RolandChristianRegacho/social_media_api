<?php
require_once("config.php");
require_once("./home/functions.php");

$user_id = 1;

$params = ["s", $user_id];
$user_response = array();
$count = 0;

$result = SelectExecuteStatement($con, getfriendlistquery, $params);

while($row = $result -> fetch_assoc()) {
    if($row["profile_picture"] !== null) {
        $row["profile_picture"] = null;
    }
    else {
        $row["profile_picture"] = null;//getDefaultPic($con);
    }

    $params = ["ss", $row["id"], $user_id];

    $row["unread_count"] = countUnreadMessage(SelectExecuteStatement($con, getunreadmessagecountbyuserquery, $params));

    $user_response[$count] = $row;
    $count++;
}

$params = ["sss", $user_id, $user_id, $user_id];

$format = SelectExecuteStatement($con, getfriendlistformatquery, $params);
$format_array = array();
$response_array = array();

while($row = $format -> fetch_assoc()) {
    array_push($format_array, array("id" => $row['user_id']));
}

$array = sortArrayByArray($format_array, $user_response);

$response = array(
    "type" => "found",
    "data" => $user_response,
    "format" => $format_array,
    "response" => $array
);

output(json_encode($response), array('Content-Type: application/json', Ok()));