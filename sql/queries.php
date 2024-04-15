<?php

//select queries
define("loginquery", "SELECT `id`, `password`, `first_name`, `middle_name`, `last_name` FROM accounts WHERE username = ?");
define("getuserinformationquery", "SELECT `id`, `profile_picture`, `first_name`, `middle_name`, `last_name`, `birthday` FROM `accounts` where `id` = ?");
define("getdefaultpic", "SELECT * FROM `images` where `id` = '1'");
define("getpostfornewsfeedquery", "SELECT DISTINCT p.id, p.user, p.content, p.date FROM `friend_list` f INNER JOIN `posts` p ON f.friends = p.user WHERE f.owner = ? OR p.user = ? AND p.status = 1 ORDER BY `date` DESC");
define("getpostimagefornewsfeedquery", "SELECT `image`, `image_type` FROM `posts` WHERE `id` = ?");
define("getpostbyuserquery", "SELECT `id`, `user`, `content`, `image`, `date` FROM `posts` where `user` = ? and `status` = '1' order by `date` desc");
define("getreplybypostquery", "SELECT `id`, `post_id`, `sender`, `content`, `date` FROM `replies` where `post_id` = ? and `status` = '1' order by `date` desc");
define("getpostbyid", "SELECT `id`, `user`, `content`, `image`, `date` FROM `posts` where `id` = ? and `status` = '1'");
define("getpostuser", "SELECT `user` FROM `posts` where `id` = ? and `status` = '1'");
define("getuserinformationbysearchquery", "SELECT `id`, `profile_picture`, `first_name`, `middle_name`, `last_name` FROM `accounts` WHERE first_name LIKE ? or middle_name LIKE ? or last_name LIKE ? ");
define("getnotificationbyuserquery", "SELECT n.id, n.sender, n.receiver, n.context, n.date, n.status, a.first_name, n.post_id FROM `notifications` n LEFT JOIN `accounts` a ON n.sender = a.id  WHERE `receiver` = ? ORDER BY `date` DESC");
define("getmessagebyuser", "SELECT `id`, `sender_id`, `receiver_id`, `content`, `date`, `timestamp` FROM `messages` WHERE (`sender_id` = ? OR `sender_id` = ?) AND (`receiver_id` = ? OR `receiver_id` = ?)");
define("getfriendlistquery", "SELECT distinct a.id, a.profile_picture, a.first_name, a.middle_name, a.last_name FROM `friend_list` f LEFT JOIN `accounts` a ON f.friends = a.id WHERE f.owner = ?");
define("getfriendlistformatquery", "SELECT MAX(`id`) as `format_id`, IF (`sender_id` = ?, `receiver_id`, `sender_id`) AS `user_id` FROM `messages` WHERE `sender_id` = ? OR `receiver_id` = ? GROUP BY `user_id` ORDER BY `format_id` DESC");
define("getfriendsendertatusquery","SELECT `id` FROM `notifications` WHERE `receiver` = ? and `sender` = ? and `context` = 'Friend Request'");
define("getfriendreceiverstatusquery","SELECT `id` FROM `notifications` WHERE `sender` = ? and `receiver` = ? and `context` = 'Friend Request'");
define("getunreadmessagecountquery", "SELECT DISTINCT `sender_id` FROM `messages` WHERE `receiver_id` = ? and `status` = 0");
define("getunreadmessagecountbyuserquery", "SELECT `id` FROM `messages` WHERE `sender_id` = ? and `receiver_id` = ? and `status` = 0");
define("getnotificationidbysenderandreceiverquery", "SELECT `id` FROM `notifications` WHERE `receiver` = ? and `sender` = ? and `context` = 'Friend Request'");
define("getfriendstatusquery", "SELECT `id` FROM `friend_list` WHERE `owner` = ? AND `friends` = ?");

//insert queries
define("signupquery", "INSERT INTO `accounts`(`username`, `password`, `first_name`, `middle_name`, `last_name`, `email`, `birthday`, `date_created`) VALUES ( ?, ?, ?, ?, ?, ?, ?, NOW())");
define("createpostwithimagequery", "INSERT INTO `posts`(`user`, `content`, `image`, `image_type`, `date`) VALUES (?, ?, ?, ?, NOW())");
define("createpostquery", "INSERT INTO `posts`(`user`, `content`, `date`) VALUES (?, ?, NOW())");
define("createreplyquery", "INSERT INTO `replies`(`post_id`, `sender`, `content`, `date`) VALUES (?, ?, ?, NOW())");
define("createnotificationquery", "INSERT INTO `notifications`(`context`, `receiver`, `sender`, `post_id`, `date`) VALUES (?, ?, ?, ?, NOW())");
define("createnotificationwithoutpostidquery", "INSERT INTO `notifications`(`context`, `receiver`, `sender`, `date`) VALUES (?, ?, ?, NOW())");
define("createfriendquery", "INSERT INTO `friend_list`(`owner`, `friends`, `date_time`) VALUES (?, ?, NOW())");
define("createmessagequery", "INSERT INTO `messages` (`sender_id`, `receiver_id`, `content`, `timestamp`, `date`) VALUES (?, ?, ?, ?, NOW())");

//update queries
define("readnotificationquery", "UPDATE `notifications` SET `status`='1' WHERE `receiver` = ?");
define("readmessagequery", "UPDATE `messages` SET `status` = '1' WHERE `sender_id` = ? AND `receiver_id` = ?");
define("updateprofilequery", "UPDATE `accounts` SET `first_name` = ?, `middle_name` = ?, `last_name` = ?, `birthday` = ? WHERE `id` = ?");
define("updateprofilewithprofilepicturequery", "UPDATE `accounts` SET `first_name` = ?, `middle_name` = ?, `last_name` = ?, `profile_picture` = ?, `image_type` = ?, `birthday` = ? WHERE `id` = ?");

//delete queries
define("deletepostbyidquery", "UPDATE `posts` SET `status` = 0 WHERE `id` = ?");
define("deletenotificationquery", "DELETE FROM `notifications` WHERE `id` = ?");
define("deletefriendrequestquery", "DELETE FROM `notifications` WHERE `receiver` = ? AND `sender` = ? AND `context` = 'Friend Request'");