ALTER TABLE `#__com_qwhelloworld` ADD COLUMN `description` VARCHAR(4000) NOT NULL DEFAULT '' AFTER `title`;

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `content_history_options`) 
VALUES
('QWHelloWorld Project', 'com_qwhelloworld.project', 
'{"formFile":"administrator\\/components\\/com_qwhelloworld\\/models\\/forms\\/project.xml", 
"hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path"], 
"ignoreChanges":["checked_out", "checked_out_time", "path"],
"convertToInt":[], 
"displayLookup":[
{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},
{"sourceColumn":"parent_id","targetTable":"#__com_qwhelloworld","targetColumn":"id","displayColumn":"title"},
{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}'),
('QWHelloWorld Category', 'com_qwhelloworld.category',
'{"formFile":"administrator\\/components\\/com_categories\\/models\\/forms\\/category.xml", 
"hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path","extension"], 
"ignoreChanges":["modified_user_id", "modified_time", "checked_out", "checked_out_time", "version", "hits", "path"],
"convertToInt":["publish_up", "publish_down"], 
"displayLookup":[
{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},
{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},
{"sourceColumn":"modified_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},
{"sourceColumn":"parent_id","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}');