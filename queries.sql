INSERT INTO categories (name, slug) VALUES
  ('Доски и лыжи', 'boards'),
  ('Крепления', 'attachment'),
  ('Ботинки', 'boots'),
  ('Одежда', 'clothing'),
  ('Инструменты', 'tools'),
  ('Разное', 'other');

INSERT INTO users (email, login, password, contact) VALUES
  ('vlad.petrov@yandex.ru', 'VladPetrov', 123456, 89617897878),
  ('angelina.ivanova@mail.ru', 'Angelina777', 123456, 89614565656),
  ('k.lastochkin@yandex.ru', 'Kostya95', 123456, 89053130031);

INSERT INTO lots (name, description, image, start_price, end_date, step, user_id, category_id) VALUES
  ('2014 Rossignol District Snowboard', 'Сноуборд модель 2014 года', 'img/lot-1.jpg', 10999, '2026-12-01', 100, 1, 1),
  ('DC Ply Mens 2016/2017 Snowboard', 'Сноуборд в идеальном состоянии', 'img/lot-2.jpg', 159999, '2026-01-01', 200, 3, 1),
  ('Крепления Union Contact Pro 2015 года размер L/XL', 'Лучшие крепления в своей ценовой категории', 'img/lot-3.jpg', 8000, '2025-12-30 15:57', 1000, 2, 2),
  ('Ботинки для сноуборда DC Mutiny Charocal', 'Удобные ботинки для сноуборда', 'img/lot-4.jpg', 10999, '2025-12-31', 100, 2, 3),
  ('Куртка для сноуборда DC Mutiny Charocal', 'Мужская куртка для сноуборда размера XL', 'img/lot-5.jpg', 7500, '2025-12-01', 500, 1, 4),
  ('Маска Oakley Canopy', 'Маска горнолыжная', 'img/lot-6.jpg', 5400, '2026-12-01', 100, 3, 6);

INSERT INTO rates (date, cost, user_id, lot_id) VALUES
  ('2026-08-01', 11099, 2, 1),
  ('2026-08-03', 11199, 3, 1),
  ('2026-08-02', 11299, 2, 1);

-- ЗАПРОСЫ

-- получить все категории

SELECT * FROM categories;

-- получить самые новые, открытые лоты.
-- Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории;

SELECT l.name, l.start_price, l.image, l.start_price, c.name as category_name FROM lots l JOIN categories c ON l.category_id = c.id WHERE l.end_date > NOW();

-- показать лот по его ID. Получите также название категории, к которой принадлежит лот;

SELECT l.*, c.name as category_name FROM lots l JOIN categories c ON l.category_id = c.id WHERE l.id = 1;

-- обновить название лота по его идентификатору;

UPDATE lots SET name = '2020 Rossignol Snowboard' WHERE id = 1;

-- получить список ставок для лота по его идентификатору с сортировкой по дате.

SELECT r.* FROM rates r JOIN lots l ON r.lot_id = l.id ORDER BY r.date ASC;
