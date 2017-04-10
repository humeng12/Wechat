-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2015-05-22 10:42:55
-- 服务器版本： 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `jike`
--

-- --------------------------------------------------------

--
-- 表的结构 `jike_friends`
--

CREATE TABLE IF NOT EXISTS `jike_friends` (
  `user_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `friend_id` int(10) unsigned NOT NULL COMMENT '好友ID'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `jike_friends`
--

INSERT INTO `jike_friends` (`user_id`, `friend_id`) VALUES
(1, 2),
(1, 3),
(2, 1),
(2, 3),
(3, 1),
(3, 2);

-- --------------------------------------------------------

--
-- 表的结构 `jike_profile`
--

CREATE TABLE IF NOT EXISTS `jike_profile` (
  `user_id` int(10) unsigned NOT NULL COMMENT '对应用户的ID',
  `age` tinyint(3) unsigned DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '地址'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `jike_profile`
--

INSERT INTO `jike_profile` (`user_id`, `age`, `address`) VALUES
(1, 18, '在那遥远的地方，有一个小山村'),
(2, 26, '不要问我从哪里来，我的故乡在远方');

-- --------------------------------------------------------

--
-- 表的结构 `jike_topic`
--

CREATE TABLE IF NOT EXISTS `jike_topic` (
`id` int(10) unsigned NOT NULL COMMENT 'Topic编号',
  `user_id` int(10) unsigned NOT NULL COMMENT '作者ID',
  `title` char(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '标题',
  `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '正文',
  `create_time` int(11) unsigned NOT NULL COMMENT '发表时间',
  `status` tinyint(3) DEFAULT '1' COMMENT '状态'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `jike_topic`
--

INSERT INTO `jike_topic` (`id`, `user_id`, `title`, `content`, `create_time`, `status`) VALUES
(1, 1, '欢迎在极客学院学习thinkphp框架知识', '极客学院推出了很多的教程，此处省略一万字。。。', 1427174043, 1);

-- --------------------------------------------------------

--
-- 表的结构 `jike_user`
--

CREATE TABLE IF NOT EXISTS `jike_user` (
`id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `username` char(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '用户名',
  `password` char(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '密码md5',
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'email',
  `create_time` int(10) unsigned NOT NULL COMMENT '注册时间',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态'
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='jike_user';

--
-- 转存表中的数据 `jike_user`
--

INSERT INTO `jike_user` (`id`, `username`, `password`, `email`, `create_time`, `status`) VALUES
(1, 'linda', '202cb962ac59075b964b07152d234b70', 'linda@jikexueyuan.com', 1427174043, 1),
(2, 'jim', '202cb962ac59075b964b07152d234b70', 'jim@jikexueyuan.com', 1427172242, 1),
(3, 'liliy', '202cb962ac59075b964b07152d234b70', 'liliy@jikexueyuan.com', 1427186701, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jike_profile`
--
ALTER TABLE `jike_profile`
 ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `jike_topic`
--
ALTER TABLE `jike_topic`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jike_user`
--
ALTER TABLE `jike_user`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jike_topic`
--
ALTER TABLE `jike_topic`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Topic编号',AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `jike_user`
--
ALTER TABLE `jike_user`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
