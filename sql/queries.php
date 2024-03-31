<?php

//select queries
define("loginquery", "SELECT `id`, `password`, `first_name`, `middle_name`, `last_name` FROM accounts WHERE username = ?");
define("getuserinformationquery", "SELECT `id`, `profile_picture`, `first_name`, `middle_name`, `last_name` FROM `accounts` where `id` = ?");
define("getdefaultpic", "SELECT * FROM `images` where `id` = '1'");
define("getpostfornewsfeedquery", "SELECT `id`, `user`, `content`, `date` FROM `posts` where `user` = (SELECT `user_1` FROM `friend_list` WHERE `user_2` = ?) OR `user` = (SELECT `user_2` FROM `friend_list` WHERE `user_1` = ?) OR `user` = ? AND `status` = '1' ORDER BY `date` DESC");
define("getpostbyuserquery", "SELECT `id`, `user`, `content`, `date` FROM `posts` where `user` = ? and `status` = '1' order by `date` desc");
define("getreplybypostquery", "SELECT `id`, `post_id`, `sender`, `content`, `date` FROM `replies` where `post_id` = ? and `status` = '1' order by `date` desc");
define("getpostbyid", "SELECT `id`, `user`, `content`, `date` FROM `posts` where `id` = ? and `status` = '1'");
define("getpostuser", "SELECT `user` FROM `posts` where `id` = ? and `status` = '1'");
define("getuserinformationbysearchquery", "SELECT `id`, `profile_picture`, `first_name`, `middle_name`, `last_name` FROM `accounts` WHERE first_name LIKE ? or middle_name LIKE ? or last_name LIKE ? ");
define("getnotificationbyuserquery", "SELECT n.id, n.sender, n.receiver, n.context, n.date, n.status, a.first_name, n.post_id FROM `notifications` n LEFT JOIN `accounts` a ON n.sender = a.id  WHERE `receiver` = ? ORDER BY `date` DESC");
define("getmessagebyuser", "SELECT `id`, `sender_id`, `receiver_id`, `content`, `date`, `timestamp` FROM `messages` WHERE (`sender_id` = ? OR `sender_id` = ?) AND (`receiver_id` = ? OR `receiver_id` = ?)");
define("getfriendlistquery", "SELECT `id`, `profile_picture`, `first_name`, `middle_name`, `last_name` FROM `accounts` WHERE `id` = (SELECT `user_1` FROM `friend_list` WHERE `user_2` = ?) OR `id` = (SELECT `user_2` FROM `friend_list` WHERE `user_1` = ?)");
define("getfriendsendertatusquery","SELECT `id` FROM `notifications` WHERE `receiver` = ? and `sender` = ? and `context` = 'Friend Request'");
define("getfriendreceiverstatusquery","SELECT `id` FROM `notifications` WHERE `sender` = ? and `receiver` = ? and `context` = 'Friend Request'");
define("getunreadmessagecountquery", "SELECT DISTINCT `sender_id` FROM `messages` WHERE `receiver_id` = ? and `status` = 0");
define("getunreadmessagecountbyuserquery", "SELECT `id` FROM `messages` WHERE `sender_id` = ? and `receiver_id` = ? and `status` = 0");
define("getnotificationidbysenderandreceiverquery", "SELECT `id` FROM `notifications` WHERE `sender` = ? and `receiver` = ? and `context` = 'Friend Request'");
define("getfriendstatusquery", "SELECT `id` FROM `friend_list` WHERE (`user_1` = ? AND `user_2` = ?) OR (`user_1` = ? AND `user_2` = ?)");

//insert queries
define("signupquery", "INSERT INTO `accounts`(`username`, `password`, `first_name`, `middle_name`, `last_name`, `email`, `birthday`, `date_created`) VALUES ( ?, ?, ?, ?, ?, ?, ?, NOW())");
define("createpostquery", "INSERT INTO `posts`(`user`, `content`, `date`) VALUES (?, ?, NOW())");
define("createreplyquery", "INSERT INTO `replies`(`post_id`, `sender`, `content`, `date`) VALUES (?, ?, ?, NOW())");
define("createnotificationquery", "INSERT INTO `notifications`(`context`, `receiver`, `sender`, `post_id`, `date`) VALUES (?, ?, ?, ?, NOW())");
define("createnotificationwithoutpostidquery", "INSERT INTO `notifications`(`context`, `receiver`, `sender`, `date`) VALUES (?, ?, ?, NOW())");
define("createfriendquery", "INSERT INTO `friend_list`(`user_1`, `user_2`, `date_time`) VALUES (?, ?, NOW())");
define("createmessagequery", "INSERT INTO `messages` (`sender_id`, `receiver_id`, `content`, `timestamp`, `date`) VALUES (?, ?, ?, ?, NOW())");

//update queries
define("readnotificationquery", "UPDATE `notifications` SET `status`='1' WHERE `receiver` = ?");
define("readmessagequery", "UPDATE `messages` SET `status` = '1' WHERE `sender_id` = ? AND `receiver_id` = ?");

//delete queries
define("deletepostbyidquery", "UPDATE `posts` SET `status` = 0 WHERE `id` = ?");
define("deletenotificationquery", "DELETE FROM `notifications` WHERE `id` = ?");
define("deletefriendrequestquery", "DELETE FROM `notifications` WHERE `receiver` = ? AND `sender` = ? AND `context` = 'Friend Request'");