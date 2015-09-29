-- Adminer 4.2.2 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `attempts`;
CREATE TABLE `attempts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unique_identifier` varchar(15) NOT NULL,
  `count_attempts` smallint(6) NOT NULL,
  `expired` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_identifier` (`unique_identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `authors`;
CREATE TABLE `authors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(20) DEFAULT NULL,
  `surname` varchar(20) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `authors` (`id`, `first_name`, `surname`, `description`) VALUES
(1,	'Девид',	'Фленаган',	'Автор популярных книг о JavaScript'),
(2,	'Артур',	'Кудрявцев',	'Выпустил книги главным образом по математике'),
(3,	'Sarah',	'McLux',	'Программист в области web-приложений');

DROP TABLE IF EXISTS `authors_books`;
CREATE TABLE `authors_books` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(10) unsigned NOT NULL,
  `book_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `authors_books_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `authors_books_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `authors_books` (`id`, `author_id`, `book_id`) VALUES
(1,	1,	1),
(2,	2,	1),
(3,	3,	2),
(4,	3,	1),
(5,	1,	3);

DROP TABLE IF EXISTS `basket`;
CREATE TABLE `basket` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `postponed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `basket_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `basket_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `description` text,
  `product_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `books_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `books` (`id`, `title`, `description`, `product_id`) VALUES
(1,	'JavaScript карманный справочник',	'описание справочника',	1),
(2,	'NodeJS',	'excelent environment',	2),
(3,	'Ниньдзя JS',	'наиболее полное руководство',	3),
(4,	'PHP 5 на практике',	'descr php5',	4),
(5,	'PHP - это просто',	'simple php',	5),
(6,	'JavaScript: сильные стороны',	'description 11',	6),
(7,	'Графика на JavaScript',	'книга о графике',	7),
(8,	'Изучаем работу с JQuery',	'jquery book',	8),
(9,	'CoffeeScript: второе дыхание',	'coffee',	9),
(10,	'Разработка приложений на JavaScript',	'new book about JS',	10),
(11,	'Speaking JavaScript',	'speak',	11),
(12,	'Веб-Мастеринг JS',	'веб-мастеринг descr',	12),
(13,	'Самоучитель JavaScript',	'самоуч js',	13),
(14,	'Современный сайт на JS',	'',	14),
(15,	'Новая книга по JS',	'new book',	15);

DROP TABLE IF EXISTS `general_catalog`;
CREATE TABLE `general_catalog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `general_catalog` (`id`, `title`, `enabled`) VALUES
(1,	'Книги',	1),
(2,	'Мебель',	1),
(3,	'Техника',	1),
(4,	'Продукты',	1),
(5,	'Одежда',	1);

DROP TABLE IF EXISTS `goods_bunch`;
CREATE TABLE `goods_bunch` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `sub_catalog_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_catalog_id` (`sub_catalog_id`),
  CONSTRAINT `goods_bunch_ibfk_1` FOREIGN KEY (`sub_catalog_id`) REFERENCES `sub_catalog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `goods_bunch` (`id`, `title`, `enabled`, `sub_catalog_id`) VALUES
(1,	'JavaScript',	1,	1),
(2,	'PHP',	1,	1),
(3,	'MySQL',	1,	1),
(4,	'Ruby',	1,	1),
(5,	'English for Beginner',	1,	2),
(6,	'Aizek',	1,	2);

DROP TABLE IF EXISTS `languages_books`;
CREATE TABLE `languages_books` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `languages_books_books`;
CREATE TABLE `languages_books_books` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `languages_books_id` int(10) unsigned NOT NULL,
  `book_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `languages_books_id` (`languages_books_id`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `languages_books_books_ibfk_1` FOREIGN KEY (`languages_books_id`) REFERENCES `languages_books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `languages_books_books_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `likes`;
CREATE TABLE `likes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `assessment` tinyint(1) NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `marks`;
CREATE TABLE `marks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `marks_products`;
CREATE TABLE `marks_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mark_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mark_id` (`mark_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `marks_products_ibfk_1` FOREIGN KEY (`mark_id`) REFERENCES `marks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `marks_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `notification_about_presence_goods`;
CREATE TABLE `notification_about_presence_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `notification_about_presence_goods_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `notification_about_presence_goods_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `orders_goods`;
CREATE TABLE `orders_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `performed` tinyint(1) DEFAULT '0',
  `product_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `orders_goods_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `price` smallint(6) NOT NULL DEFAULT '0',
  `presence` tinyint(1) NOT NULL DEFAULT '1',
  `new_mark` tinyint(1) NOT NULL DEFAULT '1',
  `goods_bunch_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `goods_bunch_id` (`goods_bunch_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`goods_bunch_id`) REFERENCES `goods_bunch` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `products` (`id`, `price`, `presence`, `new_mark`, `goods_bunch_id`) VALUES
(1,	184,	1,	1,	1),
(2,	452,	0,	1,	1),
(3,	706,	1,	1,	1),
(4,	240,	1,	1,	2),
(5,	1131,	1,	0,	2),
(6,	998,	1,	1,	1),
(7,	129,	1,	1,	1),
(8,	1556,	1,	1,	1),
(9,	772,	1,	1,	1),
(10,	1192,	1,	1,	1),
(11,	90,	1,	1,	1),
(12,	104,	1,	1,	1),
(13,	452,	1,	1,	1),
(14,	2322,	1,	1,	1),
(15,	742,	1,	1,	1);

DROP TABLE IF EXISTS `publishing_house`;
CREATE TABLE `publishing_house` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `publishing_house_books`;
CREATE TABLE `publishing_house_books` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `publishing_house_id` int(10) unsigned NOT NULL,
  `book_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `publishing_house_id` (`publishing_house_id`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `publishing_house_books_ibfk_1` FOREIGN KEY (`publishing_house_id`) REFERENCES `publishing_house` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `publishing_house_books_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `recalls`;
CREATE TABLE `recalls` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  `commentary` text NOT NULL,
  `date_crated` date NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `recalls_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sub_catalog`;
CREATE TABLE `sub_catalog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  `ordering` smallint(6) NOT NULL DEFAULT '-1',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `catalog_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `catalog_id` (`catalog_id`),
  CONSTRAINT `sub_catalog_ibfk_1` FOREIGN KEY (`catalog_id`) REFERENCES `general_catalog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sub_catalog` (`id`, `title`, `ordering`, `enabled`, `catalog_id`) VALUES
(1,	'Программирование',	1,	1,	1),
(2,	'Иностранные',	2,	1,	1),
(3,	'Медицинские',	3,	1,	1);

DROP TABLE IF EXISTS `usefulness_recalls`;
CREATE TABLE `usefulness_recalls` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `assessment` tinyint(1) NOT NULL,
  `recall_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recall_id` (`recall_id`),
  CONSTRAINT `usefulness_recalls_ibfk_1` FOREIGN KEY (`recall_id`) REFERENCES `recalls` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(20) NOT NULL,
  `password` char(128) NOT NULL,
  `salt` char(128) NOT NULL,
  `confirm_code` varchar(128) DEFAULT NULL,
  `confirm_code_expired` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `recover_password_code` varchar(128) DEFAULT NULL,
  `recover_password_code_expired` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `users_sessions`;
CREATE TABLE `users_sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `hash` char(128) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `agent` varchar(100) NOT NULL,
  `expire` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `session_name` char(100) NOT NULL,
  `session_id` char(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `users_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2015-09-29 12:00:55
