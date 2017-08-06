-- phpMyAdmin SQL Dump
-- version 4.4.15.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2017-08-06 23:53:59
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
(1, -1, '524314430@qq.com', 'ba59abbe56e057f', '', 0, 1476171916, 1502033119, '192.168.254.1');

-- --------------------------------------------------------

--
-- 表的结构 `think_authentication`
--

CREATE TABLE IF NOT EXISTS `think_authentication` (
  `id` int(11) NOT NULL COMMENT '主键，自增长',
  `mid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `real_name` varchar(100) DEFAULT NULL COMMENT '姓名',
  `idcard_type` varchar(50) DEFAULT NULL COMMENT '证件类型',
  `card` varchar(50) DEFAULT NULL COMMENT '证件',
  `image` varchar(250) DEFAULT NULL COMMENT '身份照片',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '认证状态',
  `date` int(11) NOT NULL DEFAULT '0' COMMENT '认证时间',
  `dates` int(11) DEFAULT '0' COMMENT '时间戳'
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `think_authentication`
--

INSERT INTO `think_authentication` (`id`, `mid`, `real_name`, `idcard_type`, `card`, `image`, `status`, `date`, `dates`) VALUES
(7, 6, '魏巍', '身份证', '320324198804141879', '/uploads/uploadify/auth/20170805/93c2def63be52069ecb402bbf8428bb6.png', 1, 1501945198, 0),
(6, 6, '魏巍', '身份证', '320324198804141879', '/uploads/uploadify/auth/20170805/5f424dc90b568a2fb744c9e992794065.png', 2, 1501938704, 0);

-- --------------------------------------------------------

--
-- 表的结构 `think_config`
--

CREATE TABLE IF NOT EXISTS `think_config` (
  `id` int(11) NOT NULL COMMENT '主键，自增长',
  `title` varchar(150) DEFAULT NULL COMMENT '网站名',
  `logo` varchar(150) DEFAULT NULL COMMENT '网站LOGO',
  `keywords` varchar(250) DEFAULT NULL COMMENT '网站关键词',
  `description` varchar(500) DEFAULT NULL COMMENT '网站说明',
  `conact` varchar(1000) DEFAULT NULL COMMENT '联系方式',
  `url` varchar(150) DEFAULT NULL COMMENT '网站地址',
  `carousel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态;0正常,1禁用',
  `sum` int(10) NOT NULL DEFAULT '0' COMMENT '幻灯个数',
  `flink` int(11) NOT NULL DEFAULT '0' COMMENT '友情链接',
  `tel` varchar(30) DEFAULT NULL COMMENT '电话/传真',
  `fax` varchar(50) DEFAULT NULL COMMENT '传真',
  `phone` varchar(15) DEFAULT NULL COMMENT '手机',
  `extends` varchar(500) DEFAULT NULL COMMENT '其他扩展配置',
  `address` varchar(220) DEFAULT NULL COMMENT '地址',
  `email` varchar(50) DEFAULT NULL COMMENT '邮箱',
  `copyright` varchar(10000) DEFAULT NULL COMMENT '版权信息',
  `icp` varchar(200) DEFAULT NULL COMMENT '备案号',
  `is_shard` tinyint(1) NOT NULL DEFAULT '0' COMMENT '开启分享',
  `shard` text COMMENT '分享代码',
  `code` text COMMENT '统计代码，多个使用'':''分割',
  `date` int(11) NOT NULL DEFAULT '0' COMMENT '修改日期',
  `site_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '站点是否可见,0可见,1不可见',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `dates` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='网站配置';

--
-- 转存表中的数据 `think_config`
--

INSERT INTO `think_config` (`id`, `title`, `logo`, `keywords`, `description`, `conact`, `url`, `carousel`, `sum`, `flink`, `tel`, `fax`, `phone`, `extends`, `address`, `email`, `copyright`, `icp`, `is_shard`, `shard`, `code`, `date`, `site_show`, `status`, `dates`) VALUES
(1, '苏州东游网络科技有限公司', '/uploads/uploadify/image/20170801/644a03189aed88d7d06054330ee9d0a6.png', 'cpa联盟,cps联盟,推广联盟,广告联盟,cpm联盟,流量联盟,安装联盟', '苏州东游网络科技有限公司自营产品推广平台，是为合作伙伴提供的产品推广平台获取推广链接。通过各大社交网站推广引导消费群体消费，从而为合作伙伴带来收入。', '', 'http://dy.com', 0, 3, 0, '0512-86662522', '', '', '{"qq":"524314430","gmail":"jswei30@gmail.com","msn":"js_weiwei_100@hotmail.com"}', '苏州市东创科技园B3-507', '', 'CopyRight © 2010  版权所有 苏州东游网络科技有限公司', '', 1, '&lt;!--JiaThisButtonBEGIN--&gt;&lt;div class=&quot;jiathis_style_24x24&quot;&gt;&lt;a class=&quot;jiathis_button_weixin&quot;&gt;&lt;/a&gt;&lt;a class=&quot;jiathis_button_tsina&quot;&gt;&lt;/a&gt;&lt;a class=&quot;jiathis_button_tqq&quot;&gt;&lt;/a&gt;&lt;a class=&quot;jiathis_button_fb&quot;&gt;&lt;/a&gt;&lt;a class=&quot;jiathis_button_twitter&quot;&gt;&lt;/a&gt;&lt;a class=&quot;jiathis_button_googleplus&quot;&gt;&lt;/a&gt;&lt;a class=&quot;jiathis_button_fav&quot;&gt;&lt;/a&gt;&lt;a class=&quot;jiathis_button_w3c&quot;&gt;&lt;/a&gt;&lt;a class=&quot;jiathis_button_ujian&quot;&gt;&lt;/a&gt;&lt;a class=&quot;jiathis_button_ishare&quot;&gt;&lt;/a&gt;&lt;a href=&quot;http://www.jiathis.com/share&quot; class=&quot;jiathisjiathis_txtjticojtico_jiathis&quot; target=&quot;_blank&quot;&gt;&lt;/a&gt;&lt;/div&gt;&lt;script type=&quot;text/javascript&quot; src=&quot;http://v3.jiathis.com/code_mini/jia.js&quot; charset=&quot;utf-8&quot;&gt;&lt;/script&gt;&lt;!--JiaThisButtonEND--&gt;', '&lt;script src=&quot;https://s13.cnzz.com/z_stat.php?id=1263211486&amp;web_id=1263211486&quot; language=&quot;JavaScript&quot;&gt;&lt;/script&gt;', 1476674789, 0, 0, 1501573536);

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
  `is_check` int(11) DEFAULT '0' COMMENT '认证:0未认证,1认证通过,2认证中,3认证失败',
  `email` varchar(50) DEFAULT NULL COMMENT '邮箱',
  `city` varchar(150) DEFAULT NULL,
  `address` varchar(150) DEFAULT NULL,
  `bank_name` varchar(150) DEFAULT NULL COMMENT '开户银行',
  `bank_people` varchar(150) DEFAULT NULL COMMENT '开户人',
  `bank_no` varchar(50) DEFAULT NULL COMMENT '银行卡号',
  `zip_code` varchar(10) DEFAULT NULL COMMENT '邮编',
  `alipay` varchar(50) DEFAULT NULL,
  `last_login_address` varchar(150) DEFAULT NULL COMMENT '最后登录地',
  `last_login_time` varchar(50) DEFAULT NULL COMMENT '最近登录时间',
  `last_login_ip` varchar(20) DEFAULT NULL COMMENT '最近登录ip',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态;0正常,1锁定',
  `date` int(11) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `dates` int(11) NOT NULL DEFAULT '0' COMMENT '时间戳'
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='会员表';

--
-- 转存表中的数据 `think_member`
--

INSERT INTO `think_member` (`id`, `phone`, `password`, `nickname`, `head`, `is_check`, `email`, `city`, `address`, `bank_name`, `bank_people`, `bank_no`, `zip_code`, `alipay`, `last_login_address`, `last_login_time`, `last_login_ip`, `status`, `date`, `dates`) VALUES
(6, '13584866592', 'ba59abbe56e057f', NULL, NULL, 1, '524314430@qq.com', '江苏省-苏州市-姑苏区', '三元一村47幢603室', NULL, NULL, NULL, '215001', '524314430@qq.com', '局域网', '1502022024', '192.168.254.1', 0, 1501945198, 1501945429);

-- --------------------------------------------------------

--
-- 表的结构 `think_message`
--

CREATE TABLE IF NOT EXISTS `think_message` (
  `id` int(11) NOT NULL COMMENT '主键，自增长',
  `mid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `aid` int(11) NOT NULL DEFAULT '0' COMMENT '认证id',
  `title` varchar(150) DEFAULT NULL COMMENT '标题',
  `content` text COMMENT '内容',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '类型:0认证,1系统',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `date` int(11) NOT NULL DEFAULT '0' COMMENT '时间',
  `dates` int(11) DEFAULT NULL COMMENT '时间戳'
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='消息表';

--
-- 转存表中的数据 `think_message`
--

INSERT INTO `think_message` (`id`, `mid`, `aid`, `title`, `content`, `type`, `status`, `date`, `dates`) VALUES
(6, 6, 0, '8月新产品', '各位小伙伴们你们好,我们在8月新推出了一款新的产品,欢迎选购', 1, 1, 1502022032, NULL);

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
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COMMENT='控制器';

--
-- 转存表中的数据 `think_model`
--

INSERT INTO `think_model` (`id`, `fid`, `title`, `name`, `info`, `ico`, `pic`, `sort`, `show`, `status`, `date`, `dates`) VALUES
(1, 0, 'control', '控制器管理', '控制器管理', 'fa-cog', '', 100, 0, 0, 1433401695, 1396019426),
(2, 1, 'index', '查看控制器', '查看控制器', '', '', 2, 0, 0, 1396104162, 1482386654),
(9, 0, 'column', '栏目管理', '栏目管理', ' fa-cog', '', 100, 0, 1, 1396104162, 1396104162),
(10, 9, 'index', '查看栏目', '查看栏目', 'fa-eye-open', '', 100, 0, 1, 2147483647, 2147483647),
(13, 0, 'config', '系统设置', '系统设置', 'fa-cog', '', 100, 0, 0, 1434100605, 1433321682),
(14, 13, 'index', '基本配置', '基本配置', '', '', 100, 0, 0, 1433321860, 1501549241),
(37, 0, 'member', '用户管理', '用户管理', '', '', 100, 0, 0, 1501550798, 1501550886),
(38, 37, 'index', '用户管理', '用户管理', '', '', 100, 0, 0, 1501550820, 0),
(39, 0, 'product', '产品管理', '产品管理', '', '', 100, 0, 0, 1501853968, 0),
(40, 39, 'index', '产品管理', '产品管理', '', '', 100, 0, 0, 1501853987, 0),
(41, 37, 'auth', '认证管理', '认证管理', '', '', 100, 0, 0, 1501854634, 0),
(42, 0, 'message', '消息管理', '消息管理', '', '', 100, 0, 0, 1502020714, 0),
(43, 42, 'index', '消息管理', '消息管理', '', '', 100, 0, 0, 1502020729, 0),
(44, 0, 'order', '订单管理', '订单管理', '', '', 100, 0, 0, 1502033207, 0),
(45, 44, 'index', '订单管理', '订单管理', '', '', 100, 0, 0, 1502033237, 0);

-- --------------------------------------------------------

--
-- 表的结构 `think_my_product`
--

CREATE TABLE IF NOT EXISTS `think_my_product` (
  `id` int(11) NOT NULL COMMENT '主键，自增长',
  `mid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '产品id',
  `click` int(11) NOT NULL DEFAULT '0' COMMENT '点击',
  `buy` int(11) NOT NULL DEFAULT '0' COMMENT '购买',
  `url` varchar(150) DEFAULT NULL COMMENT '连击',
  `short_url` varchar(50) DEFAULT NULL COMMENT '短地址',
  `date` int(11) NOT NULL DEFAULT '0' COMMENT '获取时间',
  `dates` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='我的产品表';

--
-- 转存表中的数据 `think_my_product`
--

INSERT INTO `think_my_product` (`id`, `mid`, `pid`, `click`, `buy`, `url`, `short_url`, `date`, `dates`) VALUES
(1, 6, 1, 2, 1, 'http://dy.com/product/detail.html?id=1&code=MTAwMDY=', 'http://t.cn/R9KaZFb', 1501865946, 0),
(2, 6, 3, 0, 0, 'http://dy.com/product/detail.html?id=3&code=MTAwMDY=', 'http://t.cn/R9Ka5VW', 1501865960, 0),
(3, 6, 2, 0, 0, 'http://dy.com/product/detail.html?id=2&code=MTAwMDY=', 'http://t.cn/R9Kax4m', 1501866111, 0),
(4, 6, 4, 0, 0, 'http://dy.com/product/detail.html?id=4&code=MTAwMDY=', 'http://t.cn/R9KarUI', 1501866887, 0),
(5, 6, 5, 0, 0, 'http://dy.com/product/detail.html?id=5&code=MTAwMDY=', 'http://t.cn/R9Karx4', 1501866893, 0),
(6, 6, 6, 0, 0, 'http://dy.com/product/detail.html?id=6&code=MTAwMDY=', 'http://t.cn/R9KaroL', 1501866896, 0),
(7, 6, 8, 0, 0, 'http://dy.com/product/detail.html?id=8&code=MTAwMDY=', 'http://t.cn/R9Kar0j', 1501866899, 0),
(8, 6, 9, 0, 0, 'http://dy.com/product/detail.html?id=9&code=MTAwMDY=', 'http://t.cn/R9KarnF', 1501866904, 0),
(9, 6, 7, 0, 0, 'http://dy.com/product/detail.html?id=7&code=MTAwMDY=', 'http://t.cn/R9Kad7u', 1501866911, 0);

-- --------------------------------------------------------

--
-- 表的结构 `think_order`
--

CREATE TABLE IF NOT EXISTS `think_order` (
  `id` int(11) NOT NULL,
  `mid` int(11) DEFAULT NULL COMMENT ' 购买者id',
  `proid` int(11) NOT NULL DEFAULT '0' COMMENT '产品id',
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
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='订单表';

--
-- 转存表中的数据 `think_order`
--

INSERT INTO `think_order` (`id`, `mid`, `proid`, `ordid`, `ordtime`, `finishtime`, `product`, `ordtitle`, `ordbuynum`, `ordprice`, `ordfee`, `ordstatus`, `payment_type`, `payment_trade_no`, `payment_trade_status`, `payment_notify_id`, `payment_notify_time`, `payment_buyer_email`, `ordcode`, `parameter`) VALUES
(1, 6, 1, '2017080652564898', 1502004772, 0, '{"id":1,"title":"\\u6d4b\\u8bd5\\u4ea7\\u54c1","price":"180.00","total":180,"address":"\\u4e09\\u5143\\u4e8c\\u6751","name":"\\u9b4f\\u5dcd"}', '测试产品', 1, 180.00, 180.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 6, 1, '2017080610150994', 1502005006, 0, '{"id":1,"title":"\\u6d4b\\u8bd5\\u4ea7\\u54c1","price":"180.00","total":180,"address":"\\u4e09\\u5143\\u4e8c\\u6751","name":"\\u9b4f\\u5dcd"}', '测试产品', 1, 180.00, 180.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 6, 1, '2017080650579757', 1502005394, 0, '{"id":1,"title":"\\u6d4b\\u8bd5\\u4ea7\\u54c1","price":"180.00","total":180,"address":"\\u4e09\\u5143\\u4e8c\\u6751","name":"\\u9b4f\\u5dcd"}', '测试产品', 1, 180.00, 180.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 6, 1, '2017080699102485', 1502005644, 0, '{"id":1,"title":"\\u6d4b\\u8bd5\\u4ea7\\u54c1","price":"180.00","total":180,"address":"\\u4e09\\u5143\\u4e8c\\u6751","name":"\\u9b4f\\u5dcd"}', '测试产品', 1, 180.00, 180.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 6, 1, '2017080652985510', 1502005812, 0, '{"id":1,"title":"\\u6d4b\\u8bd5\\u4ea7\\u54c1","price":"180.00","total":180,"address":"\\u4e09\\u5143\\u4e8c\\u6751","name":"\\u9b4f\\u5dcd"}', '测试产品', 1, 180.00, 180.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 6, 1, '2017080698485254', 1502006507, 1502006544, '{"id":1,"title":"\\u6d4b\\u8bd5\\u4ea7\\u54c1","price":"180.00","total":180,"address":"\\u4e09\\u5143\\u4e8c\\u6751","name":"\\u9b4f\\u5dcd"}', '测试产品', 1, 180.00, 180.00, 1, '支付宝', '2017080621001004260280571585', NULL, NULL, '2017-08-06 16:02:20', '524314430@qq.com', NULL, NULL),
(7, 6, 1, '2017080652555398', 1502022180, 0, '{"id":1,"title":"\\u6d4b\\u8bd5\\u4ea7\\u54c1","price":"180.00","total":180,"address":"\\u4e09\\u5143\\u4e8c\\u6751","name":"\\u9b4f\\u5dcd"}', '测试产品', 1, 180.00, 180.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 6, 1, '2017080651505498', 1502022227, 1502022257, '{"id":1,"title":"\\u6d4b\\u8bd5\\u4ea7\\u54c1","price":"180.00","total":180,"address":"\\u4e09\\u5143\\u4e8c\\u6751","name":"\\u9b4f\\u5dcd"}', '测试产品', 1, 180.00, 180.00, 1, '支付宝', '2017080621001004260281099766', NULL, NULL, '2017-08-06 20:24:12', '524314430@qq.com', NULL, NULL);

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
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='产品表';

--
-- 转存表中的数据 `think_product`
--

INSERT INTO `think_product` (`id`, `title`, `description`, `image`, `price`, `divides`, `status`, `sort`, `date`, `dates`) VALUES
(1, '测试产品', '测试产品', '/uploads/uploadify/image/20170802/67deff7d153057eef5c9129079548afa.png', '180.00', '0.01', 0, 100, 1501677687, 0),
(2, '测试的产品', '测试的产品', '/uploads/uploadify/image/20170804/a7f11cb39caede0e57bba006fa8d4731.jpg', '199.00', '0.02', 0, 100, 1501854088, 0),
(3, '测试的产品1', '测试的产品1', '/uploads/uploadify/image/20170804/02ce740ccb14a1a4107d4072e04202df.png', '10.00', '0.03', 0, 100, 1501854131, 0),
(4, '测试产品3', '测试产品3', '/uploads/uploadify/image/20170804/29ff0d5efa71d3d2d948b8987f69da7c.png', '250.00', '0.10', 0, 100, 1501854299, 0),
(5, '测试产品4', '测试产品4', '/uploads/uploadify/image/20170804/e0c10a1343741c7213c34fb157b6cf92.png', '250.00', '0.12', 0, 100, 1501854353, 0),
(6, '测试产品5', '测试产品5', '/uploads/uploadify/image/20170804/94ea723ae7734a65c82a763fd9a8657a.jpg', '450.00', '0.15', 0, 100, 1501854392, 0),
(7, '测试产品6', '测试产品6', '/uploads/uploadify/image/20170804/175c11fee82d10ff108aa039a41c592a.jpg', '450.00', '0.16', 0, 100, 1501854431, 0),
(8, '测试产品7', '测试产品7', '/uploads/uploadify/image/20170804/42efb6f57cbd74c99c74930a3b2b9db2.jpg', '500.00', '0.20', 0, 100, 1501854479, 0),
(9, '测试产品8', '测试产品8', '/uploads/uploadify/image/20170804/eaf086cab7ef087c0762758e2cd735b6.png', '600.00', '0.21', 0, 100, 1501854550, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `think_admin`
--
ALTER TABLE `think_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `think_authentication`
--
ALTER TABLE `think_authentication`
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
-- Indexes for table `think_message`
--
ALTER TABLE `think_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `think_model`
--
ALTER TABLE `think_model`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sort` (`sort`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `think_my_product`
--
ALTER TABLE `think_my_product`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `think_authentication`
--
ALTER TABLE `think_authentication`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自增长',AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `think_config`
--
ALTER TABLE `think_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自增长',AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `think_member`
--
ALTER TABLE `think_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键自增',AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `think_message`
--
ALTER TABLE `think_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自增长',AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `think_model`
--
ALTER TABLE `think_model`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自增长',AUTO_INCREMENT=46;
--
-- AUTO_INCREMENT for table `think_my_product`
--
ALTER TABLE `think_my_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自增长',AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `think_order`
--
ALTER TABLE `think_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `think_product`
--
ALTER TABLE `think_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自增长',AUTO_INCREMENT=10;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
