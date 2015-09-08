create table online_store.general_catalog (
  id serial not null primary key,
  title varchar(20),
  enabled boolean not null default true
);

create table online_store.sub_catalog (
  id serial not null primary key,
  title varchar(20) not null,
  ordering smallint not null default -1,
  enabled boolean not null default true,
  catalog_id integer not null,
  foreign key (catalog_id) references online_store.general_catalog (id) match simple on update cascade on delete cascade
);

create table online_store.goods_bunch (
  id serial not null primary key,
  title varchar(20) not null,
  enabled boolean not null default true,
  sub_catalog_id integer not null,
  foreign key (sub_catalog_id) references online_store.sub_catalog (id) match simple on update cascade on delete cascade
);

create table online_store.products (
  id serial not null primary key,
  price smallint not null default 0,
  presence boolean not null default true,
  new_mark boolean not null default true,
  goods_bunch_id integer not null,
  foreign key (goods_bunch_id) references online_store.goods_bunch (id) match simple on update cascade on delete cascade
);

create table online_store.users (
  id serial not null primary key,
  email varchar(20) not null,
  password char(128) not null,
  salt char(128) not null,
  confirm_code varchar(128),
  confirm_code_expired timestamp,
  recover_password_code varchar(128),
  recover_password_code_expired timestamp,
  is_active boolean default true,
  created date default CURRENT_DATE
);

create table online_store.attempts (
  id serial not null primary key,
  unique_identifier varchar(15) not null unique,
  count_attempts smallint not null,
  expired timestamp not null
);

create table online_store.users_sessions (
  id serial not null primary key,
  user_id integer not null,
  hash char(128) not null,
  ip varchar(15) not null,
  agent varchar(100) not null,
  expire timestamp,
  session_name char(100) not null,
  session_id char(100) not null,
  foreign key (user_id) references online_store.users (id) match simple on update cascade on delete cascade
);

create table online_store.basket (
  id serial not null primary key,
  user_id integer not null,
  product_id integer not null,
  postponed boolean default false,
  foreign key (user_id) references online_store.users (id) match simple on update cascade on delete cascade,
  foreign key (product_id) references online_store.products (id) match simple on update cascade on delete cascade
);

create table online_store.orders_goods (
  id serial not null primary key,
  performed boolean default false,
  product_id integer not null,
  foreign key (product_id) references online_store.products (id) match simple on update cascade on delete cascade
);

create table online_store.notification_about_presence_goods (
  id serial not null primary key,
  user_id integer not null,
  product_id integer not null,
  foreign key (user_id) references online_store.users (id) match simple on update cascade on delete cascade,
  foreign key (product_id) references online_store.products (id) match simple on update cascade on delete cascade
);

create table online_store.marks (
  id serial not null primary key,
  title varchar(20) not null
);

create table online_store.marks_products (
  id serial not null primary key,
  mark_id integer not null,
  product_id integer not null,
  foreign key (mark_id) references online_store.marks (id) match simple on update cascade on delete cascade,
  foreign key (product_id) references online_store.products (id) match simple on update cascade on delete cascade
);

create table online_store.likes (
  id serial not null primary key,
  assessment boolean not null,
  product_id integer not null,
  foreign key (product_id) references online_store.products (id) match simple on update cascade on delete cascade
);

create table online_store.recalls (
  id serial not null primary key,
  title varchar(20) not null,
  commentary text not null,
  date_crated date not null default CURRENT_DATE,
  user_id integer not null,
  foreign key (user_id) references online_store.users (id) match simple on update cascade on delete cascade
);

create table online_store.usefulness_recalls (
  id serial not null primary key,
  assessment boolean not null,
  recall_id integer not null,
  foreign key (recall_id) references online_store.recalls (id) match simple on update cascade on delete cascade
);

--create table online_store. (
--  id serial not null primary key,
--);

create table online_store.books (
  id serial not null primary key,
  title varchar(50) not null,
  description text,
  product_id integer not null,
  foreign key (product_id) references online_store.products (id) match simple on update cascade on delete cascade
);

create table online_store.authors (
  id serial not null primary key,
  initials varchar(20)[],
  description text
);

create table online_store.publishing_house (
  id serial not null primary key,
  title varchar(20),
  description text
);

create table online_store.languages_books (
  id serial not null primary key,
  title char(2) not null
);

create table online_store.authors_books (
  id serial not null primary key,
  author_id integer not null,
  book_id integer not null,
  foreign key (author_id) references online_store.authors (id) match simple on update cascade on delete cascade,
  foreign key (book_id) references online_store.books (id) match simple on update cascade on delete cascade
);

create table online_store.publishing_house_books (
  id serial not null primary key,
  publishing_house_id integer not null,
  book_id integer not null,
  foreign key (publishing_house_id) references online_store.publishing_house (id) match simple on update cascade on delete cascade,
  foreign key (book_id) references online_store.books (id) match simple on update cascade on delete cascade
);

create table online_store.languages_books_books (
  id serial not null primary key,
  languages_books_id integer not null,
  book_id integer not null,
  foreign key (languages_books_id) references online_store.languages_books (id) match simple on update cascade on delete cascade,
  foreign key (book_id) references online_store.books (id) match simple on update cascade on delete cascade
);

insert into online_store.general_catalog (title) values ('Книги'), ('Мебель'), ('Техника'), ('Продукты'), ('Одежда');
insert into online_store.sub_catalog (title, ordering, catalog_id) values ('Программирование', 1, 1), ('Иностранные', 2, 1), ('Медицинские', 3, 1);
insert into online_store.goods_bunch (title, sub_catalog_id) values ('JavaScript', 1), ('PHP', 1), ('MySQL', 1), ('Ruby', 1);
insert into online_store.goods_bunch (title, sub_catalog_id) values ('English for Beginner', 2), ('Aizek', 2);

-- for books JavaScript
insert into online_store.products (price, new_mark, goods_bunch_id) values
(184, true, 1), (452, true, 1), (706, true, 1);

-- for PHP
insert into online_store.products (price, new_mark, goods_bunch_id) values
(240, true,  2), (1131, false, 2);

insert into online_store.books (title, description, product_id) values
('JavaScript карманный справочник', 'описание справочника', 13),
('NodeJS', 'excelent environment', 14),
('Девид Фленаган JS', 'наиболее полное руководство', 15);

insert into online_store.books (title, description, product_id) values
('PHP 5 на практике', 'descr php5', 16),
('PHP - это просто', 'simple php', 17);

--------------------------------------------

insert into online_store.products (price, new_mark, goods_bunch_id) values
(998, true, 1), (129, true, 1), (1556, true, 1);

insert into online_store.books (title, description, product_id) values
('JavaScript: сильные стороны', 'description 11', 19),
('Графика на JavaScript', 'книга о графике', 20),
('Изучаем работу с JQuery', 'jquery book', 21);

insert into online_store.products (price, new_mark, goods_bunch_id) values
(772, true, 1), (1192, true, 1), (90, true, 1);

insert into online_store.books (title, description, product_id) values
('CoffeeScript: второе дыхание', 'coffee', 22),
('Разработка приложений на JavaScript', 'new book about JS', 23),
('Speaking JavaScript', 'speak', 24);

insert into online_store.products (price, new_mark, goods_bunch_id) values
(104, true, 1), (452, true, 1), (2322, true, 1);

insert into online_store.books (title, description, product_id) values
('Веб-Мастеринг JS', 'веб-мастеринг descr', 25),
('Самоучитель JavaScript', 'самоуч js', 26),
('Современный сайт на JS', '', 27);

insert into online_store.products (price, new_mark, goods_bunch_id) values (742, true, 1);
insert into online_store.books (title, description, product_id) values ('Новая книга по JS', 'new book', 28);

------------------------------------------
-- AUTHORS
insert into online_store.authors (initials, description) values (array['Девид', 'Фленаган'], 'Автор популярных книг о JavaScript');
insert into online_store.authors (initials, description) values (array['Артур', 'Кудрявцев'], 'Выпустил книги главным образом по математике');
insert into online_store.authors (initials, description) values (array['Sarah', 'McLux'], 'Программист в области web-приложений');