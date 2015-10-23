-- Adminer 4.2.2 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DELIMITER ;;

DROP PROCEDURE IF EXISTS `insert_session_and_if_exists_remove_obsolete`;;
CREATE PROCEDURE `insert_session_and_if_exists_remove_obsolete`(IN _user_id INT, IN _hash CHAR(128), IN _expire TINYINT(1))
BEGIN
    SET @exp = '0';
    IF (_expire = 1) THEN
      SET @exp = 'DATE_ADD(NOW(), INTERVAL 1 MONTH)';
    END IF;

    IF (SELECT exists(SELECT `id` FROM `users_sessions` WHERE `user_id` = _user_id LIMIT 1)) THEN
      SET @sql = CONCAT('UPDATE `users_sessions` SET `hash` = ?, `expire` = ', @exp, ' WHERE `user_id` = ? LIMIT 1');
      PREPARE stmt FROM @sql;
    ELSE
      SET @sql = CONCAT('INSERT INTO `users_sessions` (`hash`, `user_id`, `expire`) VALUES (?, ?, ', @exp, ')');
      PREPARE stmt FROM @sql;
    END IF;

    SET @ui = _user_id;
    SET @h = _hash;
    START TRANSACTION;
      EXECUTE stmt USING @h, @ui;
      UPDATE `users` SET `last_visit` = CURRENT_TIMESTAMP WHERE `id` = _user_id LIMIT 1;
      SET @row_count = (SELECT ROW_COUNT());
    COMMIT;
    SELECT @row_count as rc;
  END;;

DELIMITER ;

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

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `entity_id` int(11) NOT NULL,
  `entity_user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `entity_id` (`entity_id`,`entity_user_id`),
  KEY `entity_user_id` (`entity_user_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`entity_user_id`) REFERENCES `users` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `comments` (`entity_id`, `entity_user_id`, `content`, `created`) VALUES
(15,	8,	'Первый комментарий для (Первая публикация в группу про крылья id=2)',	'2015-10-17 14:15:28'),
(16,	9,	'Второй комментарий для (Первая публикация в группу про крылья id=2)',	'2015-10-17 16:10:43'),
(17,	10,	'Первый Ответ для первого комментария (id=15)',	'0000-00-00 00:00:00'),
(18,	10,	'Третий комментарий для (Первая публикация в группу про крылья id=2)',	'2015-10-18 04:07:01'),
(19,	9,	'Первый Ответ для третьего комментария (id=18)',	'0000-00-00 00:00:00'),
(20,	8,	'Второй Ответ для третьего комментария (id=18)',	'0000-00-00 00:00:00'),
(33,	9,	'Последний комментарий для (Первая публикация в группу про крылья id=2), который будет скрыт',	'2015-10-18 11:04:20'),
(34,	10,	'Самый, самый последний коммент для публикации с id = 2',	'2015-10-18 11:17:52'),
(35,	8,	'Первый комментарий для сущности id = 7',	'2015-10-18 14:09:21'),
(36,	10,	'Второй комментарий для сущности id = 7',	'2015-10-18 14:11:09'),
(37,	9,	'Первый комментарий для сущности id = 30',	'2015-10-18 14:12:21'),
(38,	8,	'Первый комментарий для перепоста id = 25',	'2015-10-19 04:09:07');

DROP TABLE IF EXISTS `entities`;
CREATE TABLE `entities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `entities_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `entities` (`id`, `parent_id`) VALUES
(1,	NULL),
(4,	NULL),
(8,	NULL),
(9,	NULL),
(10,	NULL),
(24,	NULL),
(2,	1),
(3,	1),
(7,	1),
(11,	2),
(12,	2),
(13,	2),
(15,	2),
(16,	2),
(18,	2),
(33,	2),
(34,	2),
(5,	4),
(6,	5),
(31,	5),
(35,	7),
(36,	7),
(25,	13),
(17,	15),
(19,	18),
(20,	18),
(26,	25),
(38,	25),
(27,	26),
(28,	27),
(29,	27),
(30,	28),
(37,	30),
(32,	31);

DROP TABLE IF EXISTS `entities_sheet`;
CREATE TABLE `entities_sheet` (
  `entity_id` int(11) NOT NULL,
  `type_entity_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`entity_id`),
  KEY `type_entity_id` (`type_entity_id`),
  CONSTRAINT `entities_sheet_ibfk_1` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `entities_sheet_ibfk_2` FOREIGN KEY (`type_entity_id`) REFERENCES `type_entities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `entities_sheet` (`entity_id`, `type_entity_id`, `created`) VALUES
(2,	1,	'2015-10-10 02:15:50'),
(3,	1,	'2015-10-10 02:16:43'),
(5,	1,	'2015-10-10 02:18:14'),
(6,	2,	'2015-10-10 02:46:48'),
(7,	1,	'2015-10-10 03:40:29'),
(11,	2,	'2015-10-10 08:09:12'),
(12,	2,	'2015-10-10 08:12:34'),
(13,	2,	'2015-10-10 08:37:21'),
(25,	2,	'2015-10-15 10:16:35'),
(26,	2,	'2015-10-15 10:21:15'),
(27,	2,	'2015-10-15 10:23:00'),
(28,	2,	'2015-10-15 11:11:01'),
(29,	2,	'2015-10-15 11:15:54'),
(30,	2,	'2015-10-15 11:17:06'),
(31,	2,	'2015-10-16 04:27:55'),
(32,	2,	'2015-10-16 04:33:53');

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

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `entity_id` int(11) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `created` date NOT NULL,
  PRIMARY KEY (`entity_id`),
  CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `groups` (`entity_id`, `uid`, `title`, `description`, `created`) VALUES
(1,	30937305030562,	'Группа про крылья',	'А это описание группы про крылья\r\nПривет, это вторая строка.',	'2015-10-11'),
(4,	14630880189398,	'Группа про ртуть',	'Группа была создана, что бы описать все\r\nприемущества использования ртути.',	'2015-10-13');

DROP TABLE IF EXISTS `groups_admins`;
CREATE TABLE `groups_admins` (
  `entity_group_id` int(11) NOT NULL,
  `entity_user_id` int(11) NOT NULL,
  UNIQUE KEY `entity_group_id` (`entity_group_id`,`entity_user_id`),
  KEY `entity_user_id` (`entity_user_id`),
  CONSTRAINT `groups_admins_ibfk_1` FOREIGN KEY (`entity_group_id`) REFERENCES `groups` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `groups_admins_ibfk_2` FOREIGN KEY (`entity_user_id`) REFERENCES `users` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `groups_admins` (`entity_group_id`, `entity_user_id`) VALUES
(1,	8),
(4,	10),
(1,	24);

DROP TABLE IF EXISTS `groups_users`;
CREATE TABLE `groups_users` (
  `entity_group_id` int(11) NOT NULL,
  `entity_user_id` int(11) NOT NULL,
  UNIQUE KEY `entity_group_id` (`entity_group_id`,`entity_user_id`),
  KEY `entity_user_id` (`entity_user_id`),
  CONSTRAINT `groups_users_ibfk_1` FOREIGN KEY (`entity_group_id`) REFERENCES `groups` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `groups_users_ibfk_2` FOREIGN KEY (`entity_user_id`) REFERENCES `users` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `groups_users` (`entity_group_id`, `entity_user_id`) VALUES
(1,	8),
(1,	9),
(1,	10),
(4,	24);

DROP TABLE IF EXISTS `ignored_entities_by_users`;
CREATE TABLE `ignored_entities_by_users` (
  `entity_user_id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  UNIQUE KEY `entity_id` (`entity_id`,`entity_user_id`),
  KEY `entity_user_id` (`entity_user_id`),
  CONSTRAINT `ignored_entities_by_users_ibfk_1` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ignored_entities_by_users_ibfk_2` FOREIGN KEY (`entity_user_id`) REFERENCES `users` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `ignored_entities_by_users` (`entity_user_id`, `entity_id`) VALUES
(8,	3),
(8,	33),
(9,	7);

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
  `entity_id` int(11) NOT NULL,
  `entity_id_user` int(11) NOT NULL,
  UNIQUE KEY `entity_id` (`entity_id`,`entity_id_user`),
  KEY `entity_id_user` (`entity_id_user`),
  CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`entity_id_user`) REFERENCES `users` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `likes` (`entity_id`, `entity_id_user`) VALUES
(2,	8),
(6,	8),
(7,	8),
(18,	8),
(25,	8),
(2,	9),
(6,	9),
(15,	9),
(27,	9),
(6,	10),
(7,	10),
(15,	10),
(25,	10);

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


DROP TABLE IF EXISTS `not_owners_created_entities`;
CREATE TABLE `not_owners_created_entities` (
  `entity_user_id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  UNIQUE KEY `entity_id` (`entity_id`,`entity_user_id`),
  KEY `entity_user_id` (`entity_user_id`),
  CONSTRAINT `not_owners_created_entities_ibfk_1` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `not_owners_created_entities_ibfk_2` FOREIGN KEY (`entity_user_id`) REFERENCES `users` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `not_owners_created_entities` (`entity_user_id`, `entity_id`) VALUES
(9,	2);

DROP TABLE IF EXISTS `not_viewed_new_comments_by_users`;
CREATE TABLE `not_viewed_new_comments_by_users` (
  `entity_user_id` int(11) NOT NULL,
  `entity_comment_id` int(11) NOT NULL,
  KEY `entity_user_id` (`entity_user_id`),
  KEY `entity_comment_id` (`entity_comment_id`),
  CONSTRAINT `not_viewed_new_comments_by_users_ibfk_1` FOREIGN KEY (`entity_user_id`) REFERENCES `users` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `not_viewed_new_comments_by_users_ibfk_2` FOREIGN KEY (`entity_comment_id`) REFERENCES `comments` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `not_viewed_new_comments_by_users` (`entity_user_id`, `entity_comment_id`) VALUES
(8,	16),
(10,	18),
(8,	34),
(8,	33);

DROP TABLE IF EXISTS `orders_goods`;
CREATE TABLE `orders_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `performed` tinyint(1) DEFAULT '0',
  `product_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `orders_goods_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ost_likes`;
CREATE TABLE `ost_likes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `assessment` tinyint(1) NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `ost_likes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `owners_reposts`;
CREATE TABLE `owners_reposts` (
  `entity_repost_id` int(11) NOT NULL,
  `entity_owner_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `entity_repost_id` (`entity_repost_id`),
  KEY `entity_owner_id` (`entity_owner_id`),
  CONSTRAINT `owners_reposts_ibfk_1` FOREIGN KEY (`entity_repost_id`) REFERENCES `reposts` (`entity_sheet_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `owners_reposts_ibfk_2` FOREIGN KEY (`entity_owner_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `owners_reposts` (`entity_repost_id`, `entity_owner_id`, `created`) VALUES
(6,	1,	'2015-10-10 02:46:48'),
(11,	9,	'2015-10-10 08:11:24'),
(12,	10,	'2015-10-10 08:13:38'),
(13,	8,	'2015-10-10 08:38:05'),
(25,	1,	'2015-10-15 10:20:05'),
(26,	10,	'2015-10-15 10:22:25'),
(27,	1,	'2015-10-15 10:24:31'),
(28,	9,	'2015-10-15 11:12:47'),
(29,	8,	'2015-10-15 11:16:35'),
(30,	1,	'2015-10-15 11:18:19'),
(31,	10,	'2015-10-16 04:29:18'),
(32,	1,	'2015-10-16 04:34:24');

DROP VIEW IF EXISTS `packed_general_entities`;
CREATE TABLE `packed_general_entities` (`entity_id` int(11), `uid` bigint(20), `info` varchar(200), `e_type` bigint(20));


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

DROP TABLE IF EXISTS `publications`;
CREATE TABLE `publications` (
  `entity_sheet_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`entity_sheet_id`),
  CONSTRAINT `publications_ibfk_1` FOREIGN KEY (`entity_sheet_id`) REFERENCES `entities_sheet` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `publications` (`entity_sheet_id`, `content`, `created`) VALUES
(2,	'Первая публикация в группу про крылья\r\nА это вторая строка',	'2015-10-10 02:15:50'),
(3,	'Вторая публикация в группу про крылья',	'2015-10-10 02:16:43'),
(5,	'Первая публикация в группу про РТУТЬ',	'2015-10-10 02:18:14'),
(7,	'Третья публикация в группу про крылья',	'2015-10-10 03:41:18');

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


DROP TABLE IF EXISTS `reposts`;
CREATE TABLE `reposts` (
  `entity_sheet_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`entity_sheet_id`),
  CONSTRAINT `reposts_ibfk_1` FOREIGN KEY (`entity_sheet_id`) REFERENCES `entities_sheet` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `reposts` (`entity_sheet_id`, `description`, `created`) VALUES
(6,	'Репост для (первая публикация в группу про ртуть) в группу про крылья',	'2015-10-10 02:46:48'),
(11,	'Репост для (Первая публикация в группу про крылья) от пользователя id=9',	'2015-10-10 08:09:32'),
(12,	'Репост для (Первая публикация в группу про крылья) от пользователя id=10',	'2015-10-10 08:13:19'),
(13,	'Репост для (Первая публикация в группу про крылья) от пользователя id=8',	'2015-10-10 08:37:49'),
(25,	'Репост для репоста (id = 13) в группу про крылья',	'2015-10-15 10:17:09'),
(26,	'Репост для репоста (id = 25) на стену пользователя id = 10',	'2015-10-15 10:22:16'),
(27,	'Репост для репоста (id = 26) в группу про крылья',	'2015-10-15 10:23:47'),
(28,	'Репост для репоста (id = 27) на стеную пользователя id = 9 ',	'2015-10-15 11:11:51'),
(29,	'Репост для репоста (id = 27) на стеную пользователя id = 8 ',	'2015-10-15 11:16:11'),
(30,	'Репост для репоста (id = 28) в группу про крылья',	'2015-10-15 11:17:48'),
(31,	'Репост для публикации (Первая публикация в группу про РТУТЬ) от пользователя id = 10',	'2015-10-16 04:28:50'),
(32,	'Репост для репоста (id = 31) в группу про крылья',	'2015-10-16 04:34:15');

DROP TABLE IF EXISTS `reposts_parents_trees`;
CREATE TABLE `reposts_parents_trees` (
  `entity_repost_id` int(11) NOT NULL,
  `entity_parent_id` int(11) NOT NULL,
  `entity_origin_sheet_id` int(11) NOT NULL,
  KEY `entity_repost_id` (`entity_repost_id`),
  KEY `entity_parent_id` (`entity_parent_id`),
  KEY `entity_origin_sheet_id` (`entity_origin_sheet_id`),
  CONSTRAINT `reposts_parents_trees_ibfk_1` FOREIGN KEY (`entity_repost_id`) REFERENCES `reposts` (`entity_sheet_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reposts_parents_trees_ibfk_2` FOREIGN KEY (`entity_parent_id`) REFERENCES `reposts` (`entity_sheet_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reposts_parents_trees_ibfk_3` FOREIGN KEY (`entity_origin_sheet_id`) REFERENCES `entities_sheet` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `reposts_parents_trees` (`entity_repost_id`, `entity_parent_id`, `entity_origin_sheet_id`) VALUES
(25,	13,	2),
(26,	25,	2),
(26,	13,	2),
(27,	26,	2),
(27,	25,	2),
(27,	13,	2),
(28,	27,	2),
(28,	26,	2),
(28,	25,	2),
(28,	13,	2),
(29,	27,	2),
(29,	26,	2),
(29,	25,	2),
(29,	13,	2),
(30,	28,	2),
(30,	27,	2),
(30,	26,	2),
(30,	25,	2),
(30,	13,	2),
(32,	31,	5);

DROP TABLE IF EXISTS `reviews_entities`;
CREATE TABLE `reviews_entities` (
  `entity_id` int(11) NOT NULL,
  `entity_user_id` int(11) NOT NULL,
  KEY `entity_id` (`entity_id`),
  KEY `entity_user_id` (`entity_user_id`),
  CONSTRAINT `reviews_entities_ibfk_1` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reviews_entities_ibfk_2` FOREIGN KEY (`entity_user_id`) REFERENCES `users` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `reviews_entities` (`entity_id`, `entity_user_id`) VALUES
(7,	9),
(7,	10),
(7,	8),
(6,	10);

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

DROP TABLE IF EXISTS `sub_comments_total_count`;
CREATE TABLE `sub_comments_total_count` (
  `entity_parent_comment_id` int(11) NOT NULL,
  `children_count` int(11) NOT NULL,
  KEY `entity_parent_comment_id` (`entity_parent_comment_id`),
  CONSTRAINT `sub_comments_total_count_ibfk_1` FOREIGN KEY (`entity_parent_comment_id`) REFERENCES `comments` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sub_comments_total_count` (`entity_parent_comment_id`, `children_count`) VALUES
(15,	1),
(18,	2);

DROP TABLE IF EXISTS `type_entities`;
CREATE TABLE `type_entities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `type_entities` (`id`, `title`) VALUES
(1,	'publications'),
(2,	'reposts');

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
  `email` varchar(50) NOT NULL,
  `password` char(178) NOT NULL,
  `salt` char(128) NOT NULL,
  `routine_hash_code` varchar(128) DEFAULT NULL,
  `routine_hash_code_expired` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `is_password_recover_code_sended` tinyint(1) NOT NULL DEFAULT '0',
  `created` date NOT NULL,
  `last_visit` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `entity_id` int(11) NOT NULL,
  `first_name` varchar(15) DEFAULT NULL,
  `surname` varchar(15) DEFAULT NULL,
  `uid` bigint(14) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entity_id` (`entity_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users` (`id`, `email`, `password`, `salt`, `routine_hash_code`, `routine_hash_code_expired`, `is_active`, `is_confirmed`, `is_password_recover_code_sended`, `created`, `last_visit`, `entity_id`, `first_name`, `surname`, `uid`) VALUES
(22,	'stan.coder@gmail.com',	'ebbc56aaa2880c8f812c83d3958f6e022ac8b3f41b6ce716e12b91d2515968c04b8bfd320ed9366e6c1e70d42c28724972e26d99e5fe6e4c71c639b9b778e488EB819710494F0632D418182CEA118B6115D05E927FFEB15F55',	'e1aa108d4a452f11efab5b7d6ed34a412d89a8191e6e11820d4500a2d08daea39469c3063bed257c6cfb50cc011d66d6034d2defca65b820473aec7fc7c48e7a',	NULL,	'0000-00-00 00:00:00',	1,	1,	0,	'0000-00-00',	'2015-10-23 03:18:27',	8,	'Stanislav',	'Zavalishin',	81063635591952),
(23,	'max@yandex.ru',	'have_to_change',	'have_to_change',	NULL,	'0000-00-00 00:00:00',	1,	0,	0,	'0000-00-00',	'0000-00-00 00:00:00',	9,	'Max',	'Zimovsky',	62081692541920),
(24,	'yeld@mail.ru',	'have_to_change',	'have_to_change',	NULL,	'0000-00-00 00:00:00',	1,	0,	0,	'0000-00-00',	'0000-00-00 00:00:00',	10,	'Galina',	'Xenova',	74025386215244),
(26,	'stan.coddddder@gmail.com',	'ebbc56aaa2880c8f812c83d3958f6e022ac8b3f41b6ce716e12b91d2515968c04b8bfd320ed9366e6c1e70d42c28724972e26d99e5fe6e4c71c639b9b778e488EB819710494F0632D418182CEA118B6115D05E927FFEB15F55',	'e1aa108d4a452f11efab5b7d6ed34a412d89a8191e6e11820d4500a2d08daea39469c3063bed257c6cfb50cc011d66d6034d2defca65b820473aec7fc7c48e7a',	'a397cd0793c692af24aec8f937d47983e8bef13c5607cc32ac95790fc96786ed60bc9a21869a5afeb31b02a8ee053f470feda3c5ed675df4fd7eee8e21a7e560',	'2015-10-16 06:01:56',	1,	0,	0,	'2015-10-13',	'0000-00-00 00:00:00',	24,	'Astor',	'Poper',	96826410683275);

DROP TABLE IF EXISTS `users_sessions`;
CREATE TABLE `users_sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `hash` char(128) NOT NULL,
  `expire` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `users_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users_sessions` (`id`, `user_id`, `hash`, `expire`) VALUES
(41,	22,	'c77f0c8d908222171d6288fd626d9e48cbff23900c3bbe6ae11b7bd7789c151326508db5170479ebd69939dd429de2a08f061d057159e06eae60c911ea9cb808',	'0000-00-00 00:00:00');

DROP TABLE IF EXISTS `packed_general_entities`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `packed_general_entities` AS (select `u`.`entity_id` AS `entity_id`,`u`.`uid` AS `uid`,concat(`u`.`first_name`,'|',`u`.`surname`) AS `info`,2 AS `e_type` from `users` `u` having (`info` is not null)) union all (select `g`.`entity_id` AS `entity_id`,`g`.`uid` AS `uid`,`g`.`title` AS `info`,1 AS `e_type` from `groups` `g`);

-- 2015-10-23 05:38:23
