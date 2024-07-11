
# Выбрать имена (name) всех клиентов, которые не делали заказы в последние
# 7 дней.
SELECT  `clients`.`name` FROM `clients` 
WHERE `clients`.`id` NOT IN (SELECT `orders`.`customer_id` FROM `orders` WHERE `orders`.`order_date` >= DATE(NOW() - INTERVAL 7 DAY));
#Выбрать имена (name) 5 клиентов, которые сделали больше всего заказов в
#магазине.
SELECT `clients`.`name` FROM `orders` 
LEFT JOIN `clients` ON `orders`.`customer_id` = `clients`.`id`
GROUP BY `orders`.`customer_id`
ORDER BY COUNT(`orders`.`id`) DESC
LIMIT 5; 
#Выбрать имена (name) 10 клиентов, которые сделали заказы на наибольшую
#сумму.
SELECT `clients`.`name` FROM `orders` 
LEFT JOIN `clients` ON `orders`.`customer_id` = `clients`.`id`
GROUP BY `orders`.`customer_id`
ORDER BY COUNT(`orders`.`total`) DESC
LIMIT 10; 
#Выбрать имена (name) всех товаров, по которым не было доставленных
#заказов (со статусом “complete”).
SELECT `merchandise`.`name` FROM `orders` 
LEFT JOIN `merchandise` ON `orders`.`item_id` = `merchandise`.`id`
WHERE `orders`.`status` = 'new';
#Описать, какие бы вы создали индексы для оптимизации скорости работы
#запросов из п.2 и почему
---
#Соединение таблиц (JOIN's) по ключам .


