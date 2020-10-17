ALTER TABLE`#__com_qwhelloworld` ADD COLUMN `ordering` int(11) NOT NULL DEFAULT '0' AFTER `language`;
UPDATE `#__com_qwhelloworld` SET `ordering` = `id`;