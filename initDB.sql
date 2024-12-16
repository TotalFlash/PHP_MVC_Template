CREATE DATABASE IF NOT EXISTS `test`;
USE `test`;

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `name` varchar(64) NOT NULL,
                        `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
                        PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT COLLATE=utf8_general_ci;

LOCK TABLES `user` WRITE;
INSERT INTO `user` VALUES (1,'Niclas','2024-12-11 12:56:18'),(2,'Lena','2024-12-11 12:56:23');
UNLOCK TABLES;