ALTER TABLE`#__com_qwhelloworld` ADD COLUMN `alias` VARCHAR(40) NOT NULL DEFAULT '' AFTER `title`;
UPDATE `#__com_qwhelloworld` AS h1
SET alias = (SELECT CONCAT('id-', ID) FROM (SELECT * FROM `#__com_qwhelloworld`) AS h2 WHERE h1.id = h2.id);
CREATE UNIQUE INDEX `aliasindex` ON `#__com_qwhelloworld` (`alias`);