<?php

function output($data, $http_headers = array()) {
    header_remove('Set-Cookie');

    array_push($http_headers, 'Access-Control-Allow-Origin: *');
    array_push($http_headers, 'Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    array_push($http_headers, 'Access-Control-Allow-Headers: token, Content-Type');
    array_push($http_headers, 'Access-Control-Max-Age: 1728000');
    array_push($http_headers, 'Content-Length: 0');

    if (is_array($http_headers) && count($http_headers)) {
        foreach ($http_headers as $http_header) {
            header($http_header);
        }
    }
    echo $data;
    exit;
}

function error($error_desc, $error_header) {

    array_push($error_header, 'Access-Control-Allow-Origin: *');
    array_push($error_header, 'Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    array_push($error_header, 'Access-Control-Allow-Headers: token, Content-Type');
    array_push($error_header, 'Access-Control-Max-Age: 1728000');
    array_push($error_header, 'Content-Length: 0');
    
    Output(json_encode(array('error' => $error_desc)), 
    array('Content-Type: application/json', $error_header));
}