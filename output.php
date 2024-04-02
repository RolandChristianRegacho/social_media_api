<?php

function output($data, $http_headers = array()) {
    header_remove('Set-Cookie');

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Request-Headers: authorization');
    header("Access-Control-Allow-Credentials", "true");
    header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: authorization, Content-Type');
    header('Access-Control-Max-Age: 1728000');

    if (is_array($http_headers) && count($http_headers)) {
        foreach ($http_headers as $http_header) {
            header($http_header);
        }
    }
    echo $data;
    exit;
}

function error($error_desc, $error_header) {
    Output(json_encode(array('error' => $error_desc)), 
    array('Content-Type: application/json', $error_header));
}