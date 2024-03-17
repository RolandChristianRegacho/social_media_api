<?php
    if(isset($_GET["code"])) {
        header("location: ../error_pages/?code=" . $_GET["code"] . "&message=" . $_GET["message"]);
    }