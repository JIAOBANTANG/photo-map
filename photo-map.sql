/*
 Navicat Premium Data Transfer

 Source Server         :
 Source Server Type    : MySQL
 Source Server Version : 50646
 Source Host           :
 Source Schema         : photo_map

 Target Server Type    : MySQL
 Target Server Version : 50646
 File Encoding         : 65001

 Date: 12/12/2019 12:00:59
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for m_files
-- ----------------------------
DROP TABLE IF EXISTS `m_files`;
CREATE TABLE `m_files` (
  `f_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '文件id',
  `f_uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `f_lng` varchar(20) NOT NULL DEFAULT '' COMMENT '经度',
  `f_lat` varchar(20) NOT NULL DEFAULT '' COMMENT '纬度',
  `f_url` varchar(255) NOT NULL DEFAULT '' COMMENT '文件路径',
  `f_slurl` varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图文件路径',
  `f_wsysurl` varchar(255) NOT NULL DEFAULT '' COMMENT '无损压缩',
  `f_caption` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `f_event` varchar(500) NOT NULL DEFAULT '' COMMENT '记录事件',
  `f_date_time` varchar(255) NOT NULL DEFAULT '' COMMENT '拍摄时间',
  `f_towncode` varchar(255) NOT NULL DEFAULT '' COMMENT '区号',
  `f_address` varchar(255) NOT NULL DEFAULT '' COMMENT '拍摄地点',
  `f_isd` enum('1','0') NOT NULL DEFAULT '0' COMMENT '是否删除：1=删除，0=未删除',
  `f_ctime` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `f_video_url` varchar(255) NOT NULL DEFAULT '' COMMENT '视频文件路径',
  PRIMARY KEY (`f_id`) USING BTREE,
  KEY `idx_uid` (`f_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='地图相册-文件表';


-- ----------------------------
-- Table structure for m_users
-- ----------------------------
DROP TABLE IF EXISTS `m_users`;
CREATE TABLE `m_users` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `u_love_uid` int(11) NOT NULL DEFAULT '0' COMMENT 'TA的用户ID',
  `u_qq` varchar(20) NOT NULL DEFAULT '' COMMENT 'qq号',
  `u_email` varchar(20) NOT NULL DEFAULT '' COMMENT 'qq邮箱',
  `u_name` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `u_avatar` varchar(50) NOT NULL DEFAULT '' COMMENT '用户头像',
  `u_google_auth` varchar(20) NOT NULL DEFAULT '' COMMENT '谷歌密钥',
  `u_password` varchar(20) NOT NULL DEFAULT '' COMMENT '用户密码',
  `u_code` varchar(20) NOT NULL COMMENT '关联密钥',
  `u_love_code` varchar(20) NOT NULL DEFAULT '' COMMENT 'TA的关联密钥',
  `u_love_time` timestamp NULL DEFAULT NULL COMMENT '关联时间',
  `u_ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '添加时间',
  PRIMARY KEY (`u_id`) USING BTREE,
  KEY `u_id` (`u_id`) USING BTREE,
  KEY `idx_qq` (`u_qq`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='地图相册-用户表';

SET FOREIGN_KEY_CHECKS = 1;
