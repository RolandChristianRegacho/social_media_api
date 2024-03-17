<?php

function output($data, $http_headers = array()) {
    header_remove('Set-Cookie');
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