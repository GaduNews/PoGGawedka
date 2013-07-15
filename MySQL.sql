SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

 

CREATE TABLE IF NOT EXISTS `logs` (
  `ggid` int(11) NOT NULL,
  `raw_post_data` text COLLATE utf8_bin NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `messages` (
  `ggid` int(11) NOT NULL,
  `message` varchar(16384) COLLATE utf8_polish_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

CREATE TABLE IF NOT EXISTS `messages_priv` (
  `ggid_from` int(11) NOT NULL,
  `ggid_to` int(11) NOT NULL,
  `message` varchar(16384) COLLATE utf8_polish_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `key` varchar(32) COLLATE utf8_bin NOT NULL,
  `value` varchar(2048) COLLATE utf8_bin NOT NULL,
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `settings` (`key`, `value`) VALUES
('motd', 'A polubiłeś/aś już nasz fanpage? http://www.facebook.com/PoGGawedka'),
('admins', '0'),
('reports_xmpp', ''),
('moderators', '0');

CREATE TABLE IF NOT EXISTS `users` (
  `ggid` int(11) NOT NULL,
  `nickname` varchar(32) CHARACTER SET utf8 COLLATE utf8_polish_ci DEFAULT NULL,
  `active_channel` tinyint(1) NOT NULL DEFAULT '0',
  `active_only_when_online` tinyint(1) NOT NULL DEFAULT '0',
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `ggid` (`ggid`),
  UNIQUE KEY `nickname` (`nickname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
