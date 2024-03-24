<?php

//select queries
define("loginquery", "SELECT `id`, `password`, `first_name`, `middle_name`, `last_name` FROM accounts WHERE username = ?");
define("getuserinformationquery", "SELECT `id`, `profile_picture`, `first_name`, `middle_name`, `last_name` FROM `accounts` where `id` = ?");
define("getdefaultpic", "SELECT * FROM `images` where `id` = '1'");
define("getpostbyuserquery", "SELECT `id`, `user`, `content`, `date` FROM `posts` where `user` = ? or `user` = '1' order by `date` desc");
define("getreplybypostquery", "SELECT `id`, `post_id`, `sender`, `content`, `date` FROM `replies` where `post_id` = ? order by `date` desc");
define("getpostbyid", "SELECT `id`, `user`, `content`, `date` FROM `posts` where `id` = ?");
define("getpostuser", "SELECT `user` FROM `posts` where `id` = ?");
define("getuserinformationbysearchquery", "SELECT `id`, `profile_picture`, `first_name`, `middle_name`, `last_name` FROM `accounts` WHERE first_name LIKE ? or middle_name LIKE ? or last_name LIKE ? ");
define("getnotificationbyuserquery", "SELECT n.id, n.sender, n.receiver, n.context, n.date, n.status, a.first_name, n.post_id FROM `notifications` n LEFT JOIN `accounts` a ON n.sender = a.id  WHERE `receiver` = ? ORDER BY `date` DESC");

//insert queries
define("signupquery", "INSERT INTO `accounts`(`username`, `password`, `first_name`, `middle_name`, `last_name`, `email`, `birthday`, `date_created`) VALUES ( ?, ?, ?, ?, ?, ?, ?, NOW())");
define("createpostquery", "INSERT INTO `posts`(`user`, `content`, `date`) VALUES (?, ?, NOW())");
define("createreplyquery", "INSERT INTO `replies`(`post_id`, `sender`, `content`, `date`) VALUES (?, ?, ?, NOW())");
define("createnotificationquery", "INSERT INTO `notifications`(`context`, `receiver`, `sender`, `post_id`, `date`) VALUES (?, ?, ?, ?, NOW())");

//update queries
define("readnotificationquery", "UPDATE `notifications` SET `status`='1' WHERE `receiver` = ?");