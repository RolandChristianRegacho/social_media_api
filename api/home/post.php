<?php
    require_once("../config.php");
    require_once("functions.php");

    if(isset($_GET["newPost"])) {
        $data = json_decode($_POST["data"]);
        $post = newPost($database, $data);

        echo json_encode($post);
    }

    if(isset($_GET["getPosts"])) {
        $posts = getPosts($database);

        echo json_encode($posts);
    }

    if(isset($_GET["replyToPost"])) {
        $data = json_decode($_POST["data"]);
        $reply = sendReply($database, $data); 

        echo json_encode($reply);
    }
?>