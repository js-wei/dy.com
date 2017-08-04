-- phpMyAdmin SQL Dump
-- version 4.4.15.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2017-08-04 16:48:21
-- 服务器版本： 5.5.48-log
-- PHP Version: 7.1.5

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
(1, -1, '524314430@qq.com', 'ba59abbe56e057f', '', 0, 1476171916, 1501563668, '192.168.5.1');

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `think_authentication`
--

INSERT INTO `think_authentication` (`id`, `mid`, `real_name`, `idcard_type`, `card`, `image`, `status`, `date`, `dates`) VALUES
(4, 6, '位巍', '身份证', '320324198804141879', '/uploads/uploadify/auth/20170804/7b0c29fd1ef00ad27ff5360541d7c7ba.png', 0, 1501830826, 0);

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
-- 表的结构 `think_flink`
--

CREATE TABLE IF NOT EXISTS `think_flink` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '规则id,自增主键',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '友情链接中文名称',
  `name` char(20) NOT NULL DEFAULT '' COMMENT '友情链接英文名称',
  `description` varchar(500) NOT NULL DEFAULT '' COMMENT '友情链接简单介绍',
  `ico` char(250) NOT NULL DEFAULT '' COMMENT '友情链接图标',
  `uri` char(250) NOT NULL DEFAULT '' COMMENT '友情链接链接指向,链接到的地址',
  `position` int(1) NOT NULL DEFAULT '0' COMMENT '友情链接位置：1首页，2内页',
  `date` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `effective` varchar(150) NOT NULL DEFAULT '0' COMMENT '友情链接有效时间,在有效时间内会显示',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序：越小越靠前',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态：0正常，1禁用'
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='友情链接表';

--
-- 转存表中的数据 `think_flink`
--

INSERT INTO `think_flink` (`id`, `title`, `name`, `description`, `ico`, `uri`, `position`, `date`, `effective`, `sort`, `status`) VALUES
(5, '百度', '', '百度', '/uploads/20161223/f3c2b09b5e5dab0785b79de2775fa32c.png', 'http://baidu.com', 1, 1482465028, '0', 100, 0);

-- --------------------------------------------------------

--
-- 表的结构 `think_gather`
--

CREATE TABLE IF NOT EXISTS `think_gather` (
  `id` int(11) NOT NULL COMMENT '主键自增',
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `description` varchar(250) DEFAULT NULL COMMENT '描述',
  `url` varchar(150) DEFAULT NULL COMMENT '采集地址',
  `rule` varchar(1500) DEFAULT NULL COMMENT '采集规则',
  `tit` varchar(20) DEFAULT NULL,
  `charset` int(11) NOT NULL DEFAULT '0' COMMENT '编码',
  `type` int(11) NOT NULL COMMENT '类型',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态;0正常,1禁用',
  `date` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `dates` int(11) NOT NULL DEFAULT '0' COMMENT '时间戳'
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='采集规则表';

--
-- 转存表中的数据 `think_gather`
--

INSERT INTO `think_gather` (`id`, `title`, `description`, `url`, `rule`, `tit`, `charset`, `type`, `status`, `date`, `dates`) VALUES
(1, '腾讯新闻采集规则', '腾讯新闻采集规则是根据腾讯的滚动新闻动态采集新闻,并根据类别进行保存新闻', 'http://roll.news.qq.com/interface/roll.php?0.25121977164547493&cata=&site=news&date=&page=1&mode=1&of=json', '{"list":{"tit":["span.t-tit","text"],"title":["a","text"],"href":["a","href"],"time":["span.t-time","text"]},"content":{"title":[".hd>h1","text"],"author":["span[bosszone=\\"jgname\\"]","text"],"content":["div[bosszone=\\"content\\"]","html"],"date":[".a_time","text"]}}', '不需填写使用tit采集', 1, 1, 0, 1484707581, 1484806524),
(2, '军事新闻采集规则', '次规则采集凤凰网的军事频道的新闻', 'http://news.ifeng.com/listpage/7130/1/0/50420222/50501074/list.shtml', '{"list":{"title":[".comListBox>h2>a","text"],"href":[".comListBox>h2>a","href"],"time":[".comListBox>p","text"]},"content":{"title":["h1[itemprop=\\"headline\\"]","text"],"author":["span[itemprop=\\"name\\"].ss03","text"],"content":["#main_content","html"],"date":["span[itemprop=\\"datePublished\\"].ss01","text"]}}', '军事', 2, 1, 0, 1484726029, 1484809370),
(3, '娱乐新闻采集', '此规则采集ZAKER娱乐频道', 'http://www.myzaker.com/channel/9', '{"list":{"title":["h2.figcaption>a","text"],"href":["h2.figcaption>a","href"]},"content":{"title":["div.article_header>h1","text"],"author":["div.article_header span.auther","text"],"content":["div.article_content>#content","html"],"date":["div.article_header span.time","text"]}}', '娱乐', 0, 1, 0, 1484810493, 1484810691),
(4, '汽车频道采集规则', '汽车频道采集规则', 'http://www.myzaker.com/channel/7', '{"list":{"title":["h2.figcaption>a","text"],"href":["h2.figcaption>a","href"]},"content":{"title":["div.article_header>h1","text"],"author":["div.article_header span.auther","text"],"content":["div.article_content>#content","html"],"date":["div.article_header span.time","text"]}}', '汽车', 0, 1, 0, 1484811435, 0),
(5, '体育频道采集规则', '体育频道采集规则', 'http://www.myzaker.com/channel/8', '{"list":{"title":["h2.figcaption>a","text"],"href":["h2.figcaption>a","href"]},"content":{"title":["div.article_header>h1","text"],"author":["div.article_header span.auther","text"],"content":["div.article_content>#content","html"],"date":["div.article_header span.time","text"]}}', '体育', 0, 1, 0, 1484811717, 0),
(6, '科技频道采集规则', '科技频道采集规则', 'http://www.myzaker.com/channel/13', '{"list":{"title":["h2.figcaption>a","text"],"href":["h2.figcaption>a","href"]},"content":{"title":["div.article_header>h1","text"],"author":["div.article_header span.auther","text"],"content":["div.article_content>#content","html"],"date":["div.article_header span.time","text"]}}', '科技', 0, 1, 0, 1484811822, 0),
(7, '财经频道采集规则', '财经频道采集规则', 'http://www.myzaker.com/channel/4', '{"list":{"title":["h2.figcaption>a","text"],"href":["h2.figcaption>a","href"]},"content":{"title":["div.article_header>h1","text"],"author":["div.article_header span.auther","text"],"content":["div.article_content>#content","html"],"date":["div.article_header span.time","text"]}}', '财经', 0, 1, 0, 1484811916, 0),
(8, '互联网采集规则', '互联网采集规则', 'http://www.myzaker.com/channel/5', '{"list":{"title":["h2.figcaption>a","text"],"href":["h2.figcaption>a","href"]},"content":{"title":["div.article_header>h1","text"],"author":["div.article_header span.auther","text"],"content":["div.article_content>#content","html"],"date":["div.article_header span.time","text"]}}', '互联网', 0, 1, 0, 1484812002, 0),
(9, '教育频道采集规则', '教育频道采集规则', 'http://www.myzaker.com/channel/11', '{"list":{"title":["h2.figcaption>a","text"],"href":["h2.figcaption>a","href"]},"content":{"title":["div.article_header>h1","text"],"author":["div.article_header span.auther","text"],"content":["div.article_content>#content","html"],"date":["div.article_header span.time","text"]}}', '教育', 0, 1, 0, 1484812057, 0),
(10, '时尚频道采集规则', '时尚频道采集规则', 'http://www.myzaker.com/channel/12', '{"list":{"title":["h2.figcaption>a","text"],"href":["h2.figcaption>a","href"]},"content":{"title":["div.article_header>h1","text"],"author":["div.article_header span.auther","text"],"content":["div.article_content>#content","html"],"date":["div.article_header span.time","text"]}}', '时尚', 0, 1, 0, 1484812130, 0);

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
  `is_check` int(11) DEFAULT '0' COMMENT '认证',
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
(6, '13584866592', 'ba59abbe56e057f', NULL, NULL, 2, '524314430@qq.com', '江苏省-苏州市-姑苏区', '三元一村47幢603室', NULL, NULL, NULL, '215001', '524314430@qq.com', '局域网', '1501830129', '192.168.5.1', 0, 1501830826, 1501749245);

-- --------------------------------------------------------

--
-- 表的结构 `think_message`
--

CREATE TABLE IF NOT EXISTS `think_message` (
  `id` int(11) NOT NULL COMMENT '主键，自增长',
  `mid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `title` int(11) NOT NULL COMMENT '标题',
  `content` int(11) NOT NULL COMMENT '内容',
  `status` int(11) NOT NULL COMMENT '状态',
  `date` int(11) NOT NULL DEFAULT '0' COMMENT '时间',
  `dates` int(11) DEFAULT NULL COMMENT '时间戳'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='消息表';

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
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COMMENT='控制器';

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
(38, 37, 'index', '用户管理', '用户管理', '', '', 100, 0, 0, 1501550820, 0);

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
-- Indexes for table `think_flink`
--
ALTER TABLE `think_flink`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `think_gather`
--
ALTER TABLE `think_gather`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自增长',AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `think_config`
--
ALTER TABLE `think_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自增长',AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `think_flink`
--
ALTER TABLE `think_flink`
  MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `think_gather`
--
ALTER TABLE `think_gather`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键自增',AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `think_member`
--
ALTER TABLE `think_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键自增',AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `think_message`
--
ALTER TABLE `think_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自增长';
--
-- AUTO_INCREMENT for table `think_model`
--
ALTER TABLE `think_model`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自增长',AUTO_INCREMENT=39;
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
