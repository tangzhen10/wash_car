/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : wash_car

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2018-07-07 17:13:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_manager
-- ----------------------------
DROP TABLE IF EXISTS `t_manager`;
CREATE TABLE `t_manager` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员id',
  `name` varchar(255) NOT NULL COMMENT '账户名称',
  `password` char(40) NOT NULL COMMENT '密码（sha1）',
  `salt` char(40) NOT NULL COMMENT '盐',
  `create_at` datetime NOT NULL COMMENT '账号注册时间',
  `last_login_at` datetime DEFAULT NULL COMMENT '上次登录时间',
  `last_login_ip` char(15) DEFAULT '' COMMENT '上次登录ip',
  `status` enum('1','0','-1') NOT NULL DEFAULT '1' COMMENT '账号启用状态1启用，0禁用，-1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='管理员';

-- ----------------------------
-- Table structure for t_manager_role
-- ----------------------------
DROP TABLE IF EXISTS `t_manager_role`;
CREATE TABLE `t_manager_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `manager_id` int(10) unsigned NOT NULL COMMENT '管理员id',
  `role_id` int(10) unsigned NOT NULL COMMENT '角色id',
  PRIMARY KEY (`id`),
  KEY `manager_id` (`manager_id`) USING BTREE,
  KEY `role_id` (`role_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='管理员的角色';

-- ----------------------------
-- Table structure for t_permission
-- ----------------------------
DROP TABLE IF EXISTS `t_permission`;
CREATE TABLE `t_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限id',
  `name` varchar(255) NOT NULL COMMENT '权限名称',
  `route` varchar(255) DEFAULT '' COMMENT '路由',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父节点',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '权限等级',
  `sort` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '排序',
  `show` enum('1','0') NOT NULL DEFAULT '1' COMMENT '是否显示在菜单栏',
  `status` enum('1','0','-1') NOT NULL DEFAULT '1' COMMENT '状态 1启用 0停用 -1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='权限';

-- ----------------------------
-- Table structure for t_role
-- ----------------------------
DROP TABLE IF EXISTS `t_role`;
CREATE TABLE `t_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '角色id',
  `name` varchar(255) NOT NULL COMMENT '角色名称',
  `description` varchar(255) DEFAULT '' COMMENT '描述',
  `status` enum('1','0','-1') NOT NULL DEFAULT '1' COMMENT '状态 1启用 0停用 -1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='角色';

-- ----------------------------
-- Table structure for t_role_permission
-- ----------------------------
DROP TABLE IF EXISTS `t_role_permission`;
CREATE TABLE `t_role_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL COMMENT '角色id',
  `permission_id` int(10) unsigned NOT NULL COMMENT '权限id',
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=103 DEFAULT CHARSET=utf8 COMMENT='角色和权限的对应关系';

-- ----------------------------
-- Table structure for t_user
-- ----------------------------
DROP TABLE IF EXISTS `t_user`;
CREATE TABLE `t_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '昵称',
  `phone` char(20) DEFAULT '' COMMENT '手机号',
  `email` varchar(80) DEFAULT '' COMMENT '邮箱',
  `gender` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '性别 0未知 1男 2女',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `birthday` int(11) NOT NULL DEFAULT '-1' COMMENT '生日（strtotime）',
  `create_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间（时间戳）',
  `last_login_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最近登录时间（时间戳）',
  `last_login_ip` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ip (ip2long)',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='用户信息表';

-- ----------------------------
-- Table structure for t_user_auth
-- ----------------------------
DROP TABLE IF EXISTS `t_user_auth`;
CREATE TABLE `t_user_auth` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `identity_type` varchar(20) NOT NULL DEFAULT '' COMMENT '登录渠道，目前有手机号，邮箱',
  `identity` varchar(255) NOT NULL COMMENT '标识（手机号 邮箱 用户名或第三方应用的唯一标识）',
  `credential` char(40) NOT NULL COMMENT '密码凭证（站内的保存密码，站外的不保存或保存token）',
  `salt` char(32) NOT NULL DEFAULT '' COMMENT '盐',
  `create_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间(strtitime)',
  `create_ip` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '注册ip(ip2long)',
  `status` enum('1','0') NOT NULL DEFAULT '1' COMMENT '生效状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='用户登录授权表';
