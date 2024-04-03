<?php

function SelectExecuteStatement($connection, $sql, $params = []) {
    try {
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
    catch(Exception $e) {
        print_r($e);
    } 
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