ALTER TABLE `#__com_qwhelloworld` ADD COLUMN `language` CHAR(7) NOT NULL DEFAULT '*' AFTER `alias`;

DROP INDEX `aliasindex` on `#__com_qwhelloworld`;
CREATE UNIQUE INDEX `aliasindex` ON `#__com_qwhelloworld` (`alias`, `catid`);