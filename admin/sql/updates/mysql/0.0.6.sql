DROP TABLE IF EXISTS `#__com_qwhelloworld`;

CREATE TABLE `#__com_qwhelloworld` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(25) NOT NULL,
	`published` tinyint(4) NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)
)	
	AUTO_INCREMENT=0
	ENGINE=InnoDB
	DEFAULT CHARSET=utf8mb4
	DEFAULT COLLATE=utf8mb4_unicode_ci

INSERT INTO `#__com_qwhelloworld` (`title`) VALUES
('Hello World!'),
('Good bye World!');