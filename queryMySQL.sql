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
  last_visit timestamp default 0,
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

create table likes (
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