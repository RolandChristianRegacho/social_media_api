<?php

//select queries
define("loginquery", "SELECT `id`, `password`, `first_name`, `middle_name`, `last_name` FROM accounts WHERE username = ?");
define("getuserinformationquery", "SELECT `id`, `profile_picture`, `first_name`, `middle_name`, `last_name` FROM `accounts` where `id` = ?");
define("getdefaultpic", "SELECT * FROM `images` where `id` = '1'");
define("getpostfornewsfeedquery", "SELECT `id`, `user`, `content`, `date` FROM `posts` where (`user` = ? or `user` = '1') and `status` = '1' order by `date` desc");
define("getpostbyuserquery", "SELECT `id`, `user`, `content`, `date` FROM `posts` where `user` = ? and `status` = '1' order by `date` desc");
define("getreplybypostquery", "SELECT `id`, `post_id`, `sender`, `content`, `date` FROM `replies` where `post_id` = ? and `status` = '1' order by `date` desc");
define("getpostbyid", "SELECT `id`, `user`, `content`, `date` FROM `posts` where `id` = ? and `status` = '1'");
define("getpostuser", "SELECT `user` FROM `posts` where `id` = ? and `status` = '1'");
define("getuserinformationbysearchquery", "SELECT `id`, `profile_picture`, `first_name`, `middle_name`, `last_name` FROM `accounts` WHERE first_name LIKE ? or middle_name LIKE ? or last_name LIKE ? ");
define("getnotificationbyuserquery", "SELECT n.id, n.sender, n.receiver, n.context, n.date, n.status, a.first_name, n.post_id FROM `notifications` n LEFT JOIN `accounts` a ON n.sender = a.id  WHERE `receiver` = ? ORDER BY `date` DESC");
define("getmessagebyuser", "SELECT `id`, `sender_id`, `receiver_id`, `content`, `date` FROM `messages` WHERE (`sender_id` = ? OR `sender_id` = ?) AND (`receiver_id` = ? OR `receiver_id` = ?)");
define("getalluserexceptuserquery", "SELECT `id`, `profile_picture`, `first_name`, `middle_name`, `last_name` FROM `accounts` where `id` != ?");

//insert queries
define("signupquery", "INSERT INTO `accounts`(`username`, `password`, `first_name`, `middle_name`, `last_name`, `email`, `birthday`, `date_created`) VALUES ( ?, ?, ?, ?, ?, ?, ?, NOW())");
define("createpostquery", "INSERT INTO `posts`(`user`, `content`, `date`) VALUES (?, ?, NOW())");
define("createreplyquery", "INSERT INTO `replies`(`post_id`, `sender`, `content`, `date`) VALUES (?, ?, ?, NOW())");
define("createnotificationquery", "INSERT INTO `notifications`(`context`, `receiver`, `sender`, `post_id`, `date`) VALUES (?, ?, ?, ?, NOW())");
define("createfriendquery", "INSERT INTO `friend_list`(`user_1`, `user_2`, `date_time`) VALUES (?, ?, NOW())");
define("createmessagequery", "INSERT INTO `messages` (`sender_id`, `receiver_id`, `content`, `date`) VALUES (?, ?, ?, NOW())");

//update queries
define("readnotificationquery", "UPDATE `notifications` SET `status`='1' WHERE `receiver` = ?");

//delete queries
define("deletepostbyidquery", "UPDATE `posts` SET `status` = 0 WHERE `id` = ?");
define("deletenotificationquery", "DELETE FROM `notifications` WHERE `id` = ?");