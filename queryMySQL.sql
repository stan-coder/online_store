create table general_catalog (
  id int unsigned not null primary key auto_increment,
  title varchar(20),
  enabled tinyint(1) not null default 1
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table sub_catalog (
  id int unsigned not null primary key auto_increment,
  title varchar(20) not null,
  ordering smallint not null default -1,
  enabled tinyint(1) not null default 1,
  catalog_id int unsigned not null,
  foreign key (catalog_id) references general_catalog(id) on update cascade on delete cascade
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table goods_bunch (
  id int unsigned not null primary key auto_increment,
  title varchar(20) not null,
  enabled tinyint(1) not null default 1,
  sub_catalog_id int unsigned not null,
  foreign key (sub_catalog_id) references sub_catalog (id) on update cascade on delete cascade
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table products (
  id int unsigned not null primary key auto_increment,
  price smallint not null default 0,
  presence tinyint(1) not null default 1,
  new_mark tinyint(1) not null default 1,
  goods_bunch_id int unsigned not null,
  foreign key (goods_bunch_id) references goods_bunch (id) on update cascade on delete cascade
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table users (
  id int unsigned not null primary key auto_increment,
  email varchar(50) not null,
  password char(128) not null,
  salt char(128) not null,
  routine_hash_code varchar(128),
  routine_hash_code_expired timestamp default 0,
  is_active tinyint(1) not null default 1,
  is_confirmed tinyint(1) not null default 0,
  is_password_recover_code_sended tinyint(1) not null default 0,
  created date not null,
  last_visit timestamp default 0
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table attempts (
  id int unsigned not null primary key auto_increment,
  unique_identifier varchar(15) not null unique,
  count_attempts smallint not null,
  expired timestamp not null
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table users_sessions (
  id int unsigned not null primary key auto_increment,
  user_id int unsigned not null unique,
  hash char(128) not null,
  expire timestamp default 0,
  foreign key (user_id) references users (id) on update cascade on delete cascade
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table basket (
  id int unsigned not null primary key auto_increment,
  user_id int unsigned not null,
  product_id int unsigned not null,
  postponed tinyint(1) default 0,
  foreign key (user_id) references users (id) on update cascade on delete cascade,
  foreign key (product_id) references products (id) on update cascade on delete cascade
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table orders_goods (
  id int unsigned not null primary key auto_increment,
  performed tinyint(1) default 0,
  product_id int unsigned not null,
  foreign key (product_id) references products (id) on update cascade on delete cascade
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table notification_about_presence_goods (
  id int unsigned not null primary key auto_increment,
  user_id int unsigned not null,
  product_id int unsigned not null,
  foreign key (user_id) references users (id) on update cascade on delete cascade,
  foreign key (product_id) references products (id) on update cascade on delete cascade
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table marks (
  id int unsigned not null primary key auto_increment,
  title varchar(20) not null
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table marks_products (
  id int unsigned not null primary key auto_increment,
  mark_id int unsigned not null,
  product_id int unsigned not null,
  foreign key (mark_id) references marks (id) on update cascade on delete cascade,
  foreign key (product_id) references products (id) on update cascade on delete cascade
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table ost_likes (
  id int unsigned not null primary key auto_increment,
  assessment tinyint(1) not null,
  product_id int unsigned not null,
  foreign key (product_id) references products (id) on update cascade on delete cascade
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table recalls (
  id int unsigned not null primary key auto_increment,
  title varchar(20) not null,
  commentary text not null,
  date_crated date not null,
  user_id int unsigned not null,
  foreign key (user_id) references users (id) on update cascade on delete cascade
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table usefulness_recalls (
  id int unsigned not null primary key auto_increment,
  assessment tinyint(1) not null,
  recall_id int unsigned not null,
  foreign key (recall_id) references recalls (id) on update cascade on delete cascade
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table books (
  id int unsigned not null primary key auto_increment,
  title varchar(50) not null,
  description text,
  product_id int unsigned not null,
  foreign key (product_id) references products (id) on update cascade on delete cascade
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table authors (
  id int unsigned not null primary key auto_increment,
  first_name varchar(20),
  surname varchar(20),
  description text
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table publishing_house (
  id int unsigned not null primary key auto_increment,
  title varchar(20),
  description text
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table languages_books (
  id int unsigned not null primary key auto_increment,
  title char(2) not null
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table authors_books (
  id int unsigned not null primary key auto_increment,
  author_id int unsigned not null,
  book_id int unsigned not null,
  foreign key (author_id) references authors (id) on update cascade on delete cascade,
  foreign key (book_id) references books (id) on update cascade on delete cascade
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table publishing_house_books (
  id int unsigned not null primary key auto_increment,
  publishing_house_id int unsigned not null,
  book_id int unsigned not null,
  foreign key (publishing_house_id) references publishing_house (id) on update cascade on delete cascade,
  foreign key (book_id) references books (id) on update cascade on delete cascade
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

create table languages_books_books (
  id int unsigned not null primary key auto_increment,
  languages_books_id int unsigned not null,
  book_id int unsigned not null,
  foreign key (languages_books_id) references languages_books (id) on update cascade on delete cascade,
  foreign key (book_id) references books (id) on update cascade on delete cascade
) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=INNODB;

DELIMITER //
CREATE PROCEDURE insert_session_and_if_exists_remove_obsolete (IN _user_id INT, IN _hash CHAR(128), IN _expire TINYINT(1))
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
  END; //
DELIMITER ;

---------------------------------------------------------------------------------

insert into general_catalog (title) values ('Книги'), ('Мебель'), ('Техника'), ('Продукты'), ('Одежда');
insert into sub_catalog (title, ordering, catalog_id) values ('Программирование', 1, 1), ('Иностранные', 2, 1), ('Медицинские', 3, 1);
insert into goods_bunch (title, sub_catalog_id) values ('JavaScript', 1), ('PHP', 1), ('MySQL', 1), ('Ruby', 1);
insert into goods_bunch (title, sub_catalog_id) values ('English for Beginner', 2), ('Aizek', 2);

-- for books JavaScript
insert into products (price, new_mark, goods_bunch_id) values (184, 1, 1), (452, 1, 1), (706, 1, 1);

-- for PHP
insert into products (price, new_mark, goods_bunch_id) values (240, 1,  2), (1131, 0, 2);

insert into books (title, description, product_id) values
('JavaScript карманный справочник', 'описание справочника', 1),
('NodeJS', 'excelent environment', 2),
('Девид Фленаган JS', 'наиболее полное руководство', 3);

insert into books (title, description, product_id) values
('PHP 5 на практике', 'descr php5', 4),
('PHP - это просто', 'simple php', 5);

---------------------------------------------------------------------------------

insert into products (price, new_mark, goods_bunch_id) values (998, 1, 1), (129, 1, 1), (1556, 1, 1);

insert into books (title, description, product_id) values
('JavaScript: сильные стороны', 'description 11', 6),
('Графика на JavaScript', 'книга о графике', 7),
('Изучаем работу с JQuery', 'jquery book', 8);

insert into products (price, new_mark, goods_bunch_id) values (772, 1, 1), (1192, 1, 1), (90, 1, 1);

insert into books (title, description, product_id) values
('CoffeeScript: второе дыхание', 'coffee', 9),
('Разработка приложений на JavaScript', 'new book about JS', 10),
('Speaking JavaScript', 'speak', 11);

insert into products (price, new_mark, goods_bunch_id) values (104, 1, 1), (452, 1, 1), (2322, 1, 1);

insert into books (title, description, product_id) values
('Веб-Мастеринг JS', 'веб-мастеринг descr', 12),
('Самоучитель JavaScript', 'самоуч js', 13),
('Современный сайт на JS', '', 14);

insert into products (price, new_mark, goods_bunch_id) values (742, 1, 1);
insert into books (title, description, product_id) values ('Новая книга по JS', 'new book', 15);

------------------------------------------ AUTHORS
insert into authors (first_name, surname, description) values ('Девид', 'Фленаган', 'Автор популярных книг о JavaScript');
insert into authors (first_name, surname, description) values ('Артур', 'Кудрявцев', 'Выпустил книги главным образом по математике');
insert into authors (first_name, surname, description) values ('Sarah', 'McLux', 'Программист в области web-приложений');

-----------------------------------------------
---------- social network

CREATE TABLE entities (

  id int(11) AUTO_INCREMENT NOT NULL,
  parent_id int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (parent_id) REFERENCES entities(id) ON UPDATE CASCADE ON DELETE CASCADE

) ENGINE=InnoDB;


CREATE TABLE groups (

  entity_id int(11) NOT NULL,
  uid bigint(20) NOT NULL,
  title varchar(200) NOT NULL,
  description text NOT NULL,
  created date NOT NULL,
  PRIMARY KEY (entity_id),
  FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB;


CREATE TABLE type_entities (

  id int(11) AUTO_INCREMENT NOT NULL,
  title varchar(30) NOT NULL,
  PRIMARY KEY (id)

) ENGINE=InnoDB;


CREATE TABLE entities_sheet (

  entity_id int(11) NOT NULL,
  type_entity_id int(11) NOT NULL,
  created timestamp default CURRENT_TIMESTAMP,
  PRIMARY KEY (entity_id),
  FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (type_entity_id) REFERENCES type_entities(id) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB;


CREATE TABLE publications (

  entity_sheet_id int(11) NOT NULL,
  content text NOT NULL,
  created timestamp default CURRENT_TIMESTAMP,
  PRIMARY KEY (entity_sheet_id),
  FOREIGN KEY (entity_sheet_id) REFERENCES entities_sheet(entity_id) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB;


CREATE TABLE reposts (

  entity_sheet_id int(11) NOT NULL,
  description text NOT NULL,
  created timestamp default CURRENT_TIMESTAMP,
  PRIMARY KEY (entity_sheet_id),
  FOREIGN KEY (entity_sheet_id) REFERENCES entities_sheet(entity_id) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB;


CREATE TABLE owners_reposts (
  entity_repost_id int(11) NOT NULL,
  entity_owner_id int(11) NOT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (entity_repost_id) REFERENCES reposts(entity_sheet_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (entity_owner_id) REFERENCES entities(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


CREATE TABLE users (

  entity_id int(11) NOT NULL,
  email varchar(50) NOT NULL,
  first_name varchar(15),
  surname varchar(15),
  PRIMARY KEY (entity_id),
  FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB;


CREATE TABLE likes (

  entity_id int(11) NOT NULL,
  entity_id_user int(11) NOT NULL,
  UNIQUE (entity_id, entity_id_user),
  FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (entity_id_user) REFERENCES users(entity_id)  ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB;


CREATE TABLE reviews_entities (

  entity_id int(11) NOT NULL,
  entity_user_id int(11) NOT NULL,
  FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (entity_user_id) REFERENCES users(entity_id) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE = InnoBD;


CREATE TABLE not_viewed_new_comments_by_users (

  entity_user_id int(11) NOT NULL,
  entity_comment_id int(11) NOT NULL,
  UNIQUE(entity_user_id, entity_comment_id),
  FOREIGN KEY (entity_user_id) REFERENCES users(entity_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (entity_comment_id) REFERENCES comments(entity_id) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE = InnoBD;


CREATE TABLE not_owners_created_entities (

  entity_user_id int(11) NOT NULL,
  entity_id int(11) NOT NULL,
  UNIQUE (entity_id, entity_user_id),
  FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (entity_user_id) REFERENCES users(entity_id) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE = InnoBD;


CREATE TABLE ignored_entities_by_users (

  entity_user_id int(11) NOT NULL,
  entity_id int(11) NOT NULL,
  UNIQUE (entity_id, entity_user_id),
  FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (entity_user_id) REFERENCES users(entity_id) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE = InnoBD;


CREATE TABLE reposts_parents_trees (
  entity_repost_id int(11) NOT NULL,
  entity_parent_id int(11) NOT NULL,
  entity_origin_sheet_id int(11) NOT NULL,
  FOREIGN KEY (entity_repost_id) REFERENCES reposts(entity_sheet_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (entity_parent_id) REFERENCES reposts(entity_sheet_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (entity_origin_sheet_id) REFERENCES entities_sheet(entity_id)  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


CREATE TABLE comments (

  entity_id int(11) NOT NULL,
  entity_user_id int(11) NOT NULL,
  content text NOT NULL,
  created timestamp default CURRENT_TIMESTAMP,
  UNIQUE (entity_id, entity_user_id),
  FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (entity_user_id) REFERENCES users(entity_id) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB;


CREATE TABLE sub_comments_total_count (
  entity_parent_comment_id int(11) NOT NULL,
  children_count int(11) NOT NULL,
  FOREIGN KEY (entity_parent_comment_id) REFERENCES comments(entity_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE groups_users (
  entity_group_id int(11) NOT NULL,
  entity_user_id int(11) NOT NULL,
  UNIQUE (entity_group_id, entity_user_id),
  FOREIGN KEY (entity_group_id) REFERENCES groups(entity_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (entity_user_id) REFERENCES users(entity_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE groups_admins (
  entity_group_id int(11) NOT NULL,
  entity_user_id int(11) NOT NULL,
  UNIQUE (entity_group_id, entity_user_id),
  FOREIGN KEY (entity_group_id) REFERENCES groups(entity_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (entity_user_id) REFERENCES users(entity_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

create view `packed_general_entities` as
(select u.entity_id, u.uid, concat(first_name, ' ', surname) as info, 2 as e_type from users u having info is not null)
union all
(select g.entity_id, g.uid, g.title info, 1 as e_type from groups g);


select t1.e_id entity_id, t1.e_type entity_type, t1.created created, l2.likes_count likes_count, notown.entity_user_id not_owner_entity_user_id, e2.reposts_count reposts_count,
  re2.reviews_count reviews_count, e5.comments_count comments_count, e5.total_comments_count total_comments_count
from (
  ((select esh.entity_id e_id, esh.type_entity_id e_type, esh.created from groups g
    left join entities e1 on g.entity_id = e1.parent_id
    left join entities_sheet esh on e1.id = esh.entity_id
  where g.uid = 5261037467)
    union
    (select owr.entity_repost_id e_id, 2 as e_type, owr.created from groups g
    left join owners_reposts owr on g.entity_id = owr.entity_owner_id
    where g.uid = 5261037467)) as t1
  )
  left join (select entity_user_id, entity_id from ignored_entities_by_users ign where ign.entity_user_id = 8) as t2 on t1.e_id = t2.entity_id
  left join (select l1.entity_id, count(l1.entity_id) likes_count from likes l1 group by l1.entity_id) l2 on t1.e_id = l2.entity_id
  left join not_owners_created_entities notown on t1.e_id = notown.entity_id
  left join (
    select e3.parent_id, count(e3.id) as reposts_count from entities e3
      left join reposts r1 on e3.id = r1.entity_sheet_id where r1.entity_sheet_id is not null group by e3.parent_id) e2 on t1.e_id = e2.parent_id
  left join (select re1.entity_id, count(re1.entity_id) as reviews_count from reviews_entities re1 group by re1.entity_id) re2 on t1.e_id = re2.entity_id
  left join (select e4.parent_id, count(c.entity_id) as comments_count, count(c.entity_id)+sum(sc.children_count) as total_comments_count from comments c
    left join entities e4 on c.entity_id = e4.id
    left join sub_comments_total_count sc on c.entity_id = sc.entity_parent_comment_id
  group by e4.parent_id) as e5 on e5.parent_id = t1.e_id

where t2.entity_id is null
order by t1.created, t1.e_id