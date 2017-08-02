-- phpMyAdmin SQL Dump
-- version 4.4.15.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2017-08-03 07:20:09
-- 服务器版本： 5.5.48-log
-- PHP Version: 5.5.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_spread`
--

-- --------------------------------------------------------

--
-- 表的结构 `think_admin`
--

CREATE TABLE IF NOT EXISTS `think_admin` (
  `id` int(11) NOT NULL COMMENT '主键,自增长',
  `gid` int(11) NOT NULL COMMENT '所属群组:-1为超级管理员',
  `username` varchar(30) NOT NULL COMMENT '用户名',
  `password` varchar(32) NOT NULL COMMENT '用户密码',
  `hash` varchar(50) NOT NULL COMMENT '密码校验',
  `status` tinyint(1) NOT NULL COMMENT '状态:0正常;1锁定',
  `date` int(11) NOT NULL COMMENT '添加日期',
  `last_date` int(10) NOT NULL COMMENT '最后登录时间',
  `last_ip` varchar(15) NOT NULL COMMENT '最后登录IP'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员表';

--
-- 转存表中的数据 `think_admin`
--

INSERT INTO `think_admin` (`id`, `gid`, `username`, `password`, `hash`, `status`, `date`, `last_date`, `last_ip`) VALUES
(1, -1, '524314430@qq.com', 'ba59abbe56e057f', '', 0, 1476171916, 1501667209, '192.168.254.1');

-- --------------------------------------------------------

--
-- 表的结构 `think_config`
--

CREATE TABLE IF NOT EXISTS `think_config` (
  `id` int(11) NOT NULL COMMENT '主键，自增长',
  `title` varchar(150) NOT NULL COMMENT '网站名',
  `logo` varchar(50) DEFAULT NULL COMMENT '网站LOGO',
  `keywords` varchar(250) NOT NULL COMMENT '网站关键词',
  `description` varchar(500) NOT NULL COMMENT '网站说明',
  `conact` varchar(1000) NOT NULL COMMENT '联系方式',
  `url` varchar(150) NOT NULL DEFAULT '' COMMENT '网站地址',
  `carousel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态;0正常,1禁用',
  `sum` int(10) NOT NULL COMMENT '幻灯个数',
  `flink` int(11) NOT NULL DEFAULT '0' COMMENT '友情链接',
  `tel` varchar(30) DEFAULT NULL COMMENT '电话/传真',
  `fax` varchar(50) NOT NULL DEFAULT '' COMMENT '传真',
  `phone` varchar(15) DEFAULT NULL COMMENT '手机',
  `extends` varchar(500) DEFAULT NULL COMMENT '其他扩展配置',
  `address` varchar(220) DEFAULT NULL COMMENT '地址',
  `email` varchar(50) DEFAULT NULL COMMENT '邮箱',
  `copyright` varchar(10000) NOT NULL COMMENT '版权信息',
  `icp` varchar(200) NOT NULL COMMENT '备案号',
  `is_shard` tinyint(1) NOT NULL DEFAULT '0' COMMENT '开启分享',
  `shard` text NOT NULL COMMENT '分享代码',
  `code` text NOT NULL COMMENT '统计代码，多个使用'':''分割',
  `date` int(10) NOT NULL COMMENT '修改日期',
  `site_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '站点是否可见,0可见,1不可见',
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='网站配置';

--
-- 转存表中的数据 `think_config`
--

INSERT INTO `think_config` (`id`, `title`, `logo`, `keywords`, `description`, `conact`, `url`, `carousel`, `sum`, `flink`, `tel`, `fax`, `phone`, `extends`, `address`, `email`, `copyright`, `icp`, `is_shard`, `shard`, `code`, `date`, `site_show`, `status`) VALUES
(1, '南京坤爵信息技术有限公司', '', '南京坤爵信息技术有限公司，南京坤爵，坤爵，档案数字化，档案', '南京坤爵信息技术有限公司主要从事计算机软硬件、网络工程、服务外包及档案数字化相关产业。是一家专注于档案数字化加工领域多年的高科技服务型企业。严格遵守档案数字化行业标准和保密规定，为机关企事业单位、金融、电力、报社、公检法、国土、房产、档案馆等及各类企业提供专业的数字化加工服务。公司拥有专业、先进的数字化加工设备，自主产权的数字化加工软件和丰富的行业加工管理经验，以高效、高质量和优质的服务建立起行业的品牌形象。', '', 'http://phpcms.jswei.com', 0, 3, 0, '025-86183859', '', '025-86183859', '{"qq":"524314430","gmail":"jswei30@gmail.com","msn":"js_weiwei_100@hotmail.com"}', '南京市江宁区天元东路228号莱茵量子国际1114室', 'www_kunjue@163.com', '2010-2025 版权所有', '', 1, '&lt;!--JiaThisButtonBEGIN--&gt;&lt;div class=&quot;jiathis_style_24x24&quot;&gt;&lt;a class=&quot;jiathis_button_weixin&quot;&gt;&lt;/a&gt;&lt;a class=&quot;jiathis_button_tsina&quot;&gt;&lt;/a&gt;&lt;a class=&quot;jiathis_button_tqq&quot;&gt;&lt;/a&gt;&lt;a class=&quot;jiathis_button_fb&quot;&gt;&lt;/a&gt;&lt;a class=&quot;jiathis_button_twitter&quot;&gt;&lt;/a&gt;&lt;a class=&quot;jiathis_button_googleplus&quot;&gt;&lt;/a&gt;&lt;a class=&quot;jiathis_button_fav&quot;&gt;&lt;/a&gt;&lt;a class=&quot;jiathis_button_w3c&quot;&gt;&lt;/a&gt;&lt;a class=&quot;jiathis_button_ujian&quot;&gt;&lt;/a&gt;&lt;a class=&quot;jiathis_button_ishare&quot;&gt;&lt;/a&gt;&lt;a href=&quot;http://www.jiathis.com/share&quot; class=&quot;jiathisjiathis_txtjticojtico_jiathis&quot; target=&quot;_blank&quot;&gt;&lt;/a&gt;&lt;/div&gt;&lt;script type=&quot;text/javascript&quot; src=&quot;http://v3.jiathis.com/code_mini/jia.js&quot; charset=&quot;utf-8&quot;&gt;&lt;/script&gt;&lt;!--JiaThisButtonEND--&gt;', '', 1476674789, 0, 0),
(2, '', NULL, '', '', '', '', 0, 0, 0, NULL, '', NULL, NULL, NULL, NULL, '', '', 0, '', '', 1484111685, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `think_member`
--

CREATE TABLE IF NOT EXISTS `think_member` (
  `id` int(11) NOT NULL COMMENT '主键自增',
  `phone` varchar(50) DEFAULT NULL COMMENT '用户名',
  `password` varchar(50) DEFAULT NULL COMMENT '密码',
  `nickname` varchar(50) DEFAULT NULL COMMENT '昵称',
  `head` varchar(50) DEFAULT NULL COMMENT '头像',
  `email` varchar(50) DEFAULT NULL COMMENT '邮箱',
  `auth` varchar(150) DEFAULT NULL,
  `alipay` varchar(150) DEFAULT NULL,
  `city` varchar(150) DEFAULT NULL,
  `address` varchar(150) DEFAULT NULL,
  `last_login_address` varchar(150) DEFAULT NULL,
  `last_login_time` varchar(50) DEFAULT NULL COMMENT '最近登录时间',
  `last_login_ip` varchar(20) DEFAULT NULL COMMENT '最近登录ip',
  `status` int(11) NOT NULL COMMENT '状态;0正常,1锁定',
  `date` int(11) NOT NULL COMMENT '注册时间',
  `dates` int(11) NOT NULL COMMENT '时间戳'
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='会员表';

--
-- 转存表中的数据 `think_member`
--

INSERT INTO `think_member` (`id`, `phone`, `password`, `nickname`, `head`, `email`, `auth`, `alipay`, `city`, `address`, `last_login_address`, `last_login_time`, `last_login_ip`, `status`, `date`, `dates`) VALUES
(3, '13584866592', 'ba59abbe56e057f', '魏巍', NULL, '524314430@qq.com', NULL, '524314430@qq.com', '江苏省-苏州市-姑苏区', '苏锦一村', NULL, NULL, NULL, 0, 1501587203, 1501672955);

-- --------------------------------------------------------

--
-- 表的结构 `think_model`
--

CREATE TABLE IF NOT EXISTS `think_model` (
  `id` int(11) NOT NULL COMMENT '主键，自增长',
  `fid` int(11) NOT NULL COMMENT '父节点：0,为顶级',
  `title` varchar(30) NOT NULL COMMENT '英文标识',
  `name` varchar(50) NOT NULL COMMENT '中文名',
  `info` varchar(250) NOT NULL COMMENT '简介',
  `ico` varchar(50) NOT NULL COMMENT '图标',
  `pic` varchar(500) NOT NULL COMMENT '控制器图片',
  `sort` int(11) NOT NULL DEFAULT '100' COMMENT '排序,越小越靠前',
  `show` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否显示导航,0显示,1不显示',
  `status` int(10) NOT NULL COMMENT '状态:0正常,1锁定',
  `date` int(10) NOT NULL COMMENT '添加日期',
  `dates` int(10) NOT NULL COMMENT '修改日期'
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COMMENT='控制器';

--
-- 转存表中的数据 `think_model`
--

INSERT INTO `think_model` (`id`, `fid`, `title`, `name`, `info`, `ico`, `pic`, `sort`, `show`, `status`, `date`, `dates`) VALUES
(1, 0, 'control', '控制器管理', '控制器管理', 'fa-cog', '', 1, 0, 0, 1433401695, 1501671775),
(2, 1, 'index', '查看控制器', '查看控制器', '', '', 2, 0, 0, 1396104162, 1482386654),
(42, 41, 'index', '订单管理', '订单管理', '', '', 100, 0, 0, 1501678177, 0),
(39, 0, 'product', '产品管理', '产品管理', '', '', 100, 0, 0, 1501667273, 0),
(40, 39, 'index', '产品管理', '产品管理', '', '', 100, 0, 0, 1501667296, 1501667317),
(41, 0, 'order', '订单管理', '订单管理', '', '', 100, 0, 0, 1501678143, 0),
(13, 0, 'config', '系统设置', '系统设置', 'fa-cog', '', 2, 0, 0, 1434100605, 1501671786),
(14, 13, 'index', '基本配置', '基本配置', '', '', 100, 0, 0, 1433321860, 1501585934),
(38, 37, 'index', '用户管理', '用户管理', '', '', 100, 0, 0, 1501586193, 0),
(37, 0, 'member', '用户管理', '用户管理', '', '', 3, 0, 0, 1501586161, 1501671806);

-- --------------------------------------------------------

--
-- 表的结构 `think_order`
--

CREATE TABLE IF NOT EXISTS `think_order` (
  `id` int(11) NOT NULL,
  `mid` int(11) DEFAULT NULL COMMENT ' 购买者id',
  `ordid` varchar(255) DEFAULT NULL COMMENT '订单号',
  `ordtime` int(11) DEFAULT NULL COMMENT '订单时间',
  `finishtime` int(11) NOT NULL COMMENT '订单完成时间',
  `product` text COMMENT '产品',
  `ordtitle` varchar(255) DEFAULT NULL COMMENT '订单标题',
  `ordbuynum` int(11) DEFAULT '0' COMMENT '购买数量',
  `ordprice` float(10,2) DEFAULT '0.00' COMMENT '产品单价',
  `ordfee` float(10,2) DEFAULT '0.00' COMMENT '订单总金额',
  `ordstatus` int(11) DEFAULT '0' COMMENT '订单状态0未支付，1支付成功，2用户取消',
  `payment_type` varchar(255) DEFAULT NULL COMMENT '支付类型',
  `payment_trade_no` varchar(255) DEFAULT NULL COMMENT '支付接口交易号',
  `payment_trade_status` varchar(255) DEFAULT NULL COMMENT '支付接口返回的交易状态',
  `payment_notify_id` varchar(255) DEFAULT NULL COMMENT '异步推送ID',
  `payment_notify_time` varchar(255) DEFAULT NULL COMMENT '异步推送时间',
  `payment_buyer_email` varchar(255) DEFAULT NULL COMMENT '购买者帐号',
  `ordcode` varchar(255) DEFAULT NULL COMMENT '二维码',
  `parameter` text
) ENGINE=MyISAM AUTO_INCREMENT=613 DEFAULT CHARSET=utf8 COMMENT='订单表';

-- --------------------------------------------------------

--
-- 表的结构 `think_product`
--

CREATE TABLE IF NOT EXISTS `think_product` (
  `id` int(11) NOT NULL COMMENT '主键，自增长',
  `title` varchar(150) DEFAULT NULL COMMENT '产品名称',
  `description` varchar(150) DEFAULT NULL COMMENT '说明',
  `image` varchar(150) DEFAULT NULL COMMENT '图片',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `divides` varchar(10) NOT NULL DEFAULT '0' COMMENT '分成',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态;0正常,1锁定',
  `sort` int(11) NOT NULL DEFAULT '100',
  `date` int(11) NOT NULL DEFAULT '0' COMMENT '时间',
  `dates` int(11) NOT NULL DEFAULT '0' COMMENT '时间戳'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='产品表';

--
-- 转存表中的数据 `think_product`
--

INSERT INTO `think_product` (`id`, `title`, `description`, `image`, `price`, `divides`, `status`, `sort`, `date`, `dates`) VALUES
(1, '测试产品', '测试产品', '/uploads/uploadify/image/20170802/67deff7d153057eef5c9129079548afa.png', '180.00', '0.01', 0, 100, 1501677687, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `think_admin`
--
ALTER TABLE `think_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `think_config`
--
ALTER TABLE `think_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `think_member`
--
ALTER TABLE `think_member`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `think_model`
--
ALTER TABLE `think_model`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sort` (`sort`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `think_order`
--
ALTER TABLE `think_order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `think_product`
--
ALTER TABLE `think_product`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `think_admin`
--
ALTER TABLE `think_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键,自增长',AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `think_config`
--
ALTER TABLE `think_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自增长',AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `think_member`
--
ALTER TABLE `think_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键自增',AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `think_model`
--
ALTER TABLE `think_model`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自增长',AUTO_INCREMENT=43;
--
-- AUTO_INCREMENT for table `think_order`
--
ALTER TABLE `think_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=613;
--
-- AUTO_INCREMENT for table `think_product`
--
ALTER TABLE `think_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自增长',AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
