<?php
	require_once("../Constant.php");

function SelectExecuteStatement($connection, $sql, $params = []) {
    $stmt = $connection->prepare($sql);
    
    if(!$stmt) {
        error("Server Error", ServerError());
    }
    
    if(count($params) > 0) {
        $stmt->bind_param(...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result;
}

function ExecuteStatement($connection, $sql, $params = []) {
    $stmt = $connection->prepare($sql);

    if(!$stmt) {
        error("Server Error", ServerError());
    }
    
    if(count($params) > 0) {
        $stmt->bind_param(...$params);
    }

    $stmt->execute();
    
    if($stmt) {
        return true;
    }

    return false;
}