<?php
define("get", "GET");
define("post", "POST");
define("put", "PUT");
define("delete", "DELETE");
define("options", "OPTIONS");
define("max_page_count", 10);

function Ok() {
    return "HTTP/1.1 200 OK";
}

function NotFound() {
    return "HTTP/1.1 404 Not Found";
}

function NotAllowed() {
    return "HTTP/1.1 405 Method Not Allowed";
}

function ServerError() {
    return "HTTP/1.1 500 Internal Server Error";
}