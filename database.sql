-- my database
CREATE DATABASE IF NOT EXISTS `inventory`;

-- select database
USE `inventory`;

-- sample table products
CREATE TABLE IF NOT EXISTS `products`(
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `product_type_id` INT NOT NULL,
    `Food_name` VARCHAR(30) NOT NULL,
    `Quantity` INT NOT NULL,      
    `date_inserted` DATE NOT NULL,
    `date_updated` DATE NOT NULL

);

CREATE TABLE IF NOT EXISTS `product_type` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `type_name` VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS `users`(
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(30) NOT NULL,
    `password` VARCHAR(30) NOT NULL
);

INSERT INTO `product_type`
(`type_name`)
VALUES
('Foods'),
('Drinks'),
('Desserts'),
('Meals'),
('Combo'),
('Alcohols');



ALTER TABLE `products` ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`);

ALTER TABLE `products` ADD FOREIGN KEY (`product_type_id`) REFERENCES `product_type`(`id`);




