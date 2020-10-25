CREATE TABLE `#__com_qwhelloworld_projects` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`catid` int(11) NOT NULL DEFAULT 0,
	`asset_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
	/*`ordering` int(11) NOT NULL DEFAULT '0',*/
	`parent_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
	`lft` int(11) NOT NULL DEFAULT 0,
	`rgt` int(11) NOT NULL DEFAULT 0,
	`level` int(10) UNSIGNED NOT NULL DEFAULT 0,
	`path` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',	
	`published` tinyint(4) NOT NULL DEFAULT '1',
	`checked_out` int(10) unsigned NOT NULL DEFAULT 0,
    `checked_out_time` datetime,
	`created` datetime NOT NULL,
	`created_by` int(10) unsigned NOT NULL DEFAULT 0,	
	`access` int(10) unsigned NOT NULL DEFAULT 0,
	`language` char(7) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The language code for the article.',
	`params` text NOT NULL,
	`description` VARCHAR(4000) NOT NULL DEFAULT '',
	`image` VARCHAR(1024) NOT NULL DEFAULT '',
	`latitude` DECIMAL(9,7) NOT NULL DEFAULT 0.0,
	`longitude` DECIMAL(10,7) NOT NULL DEFAULT 0.0,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

/* What is this for? */
CREATE UNIQUE INDEX `aliasindex` ON `#__com_qwhelloworld_projects` (`alias`, `catid`);

INSERT INTO `#__com_qwhelloworld_projects` (`title`,`alias`, `published`, `language`, `parent_id`, `level`, `path`, `lft`, `rgt`) VALUES
('QWHelloWorld root','qwhelloworld-root-alias', 1, '*', 0, 0, '', 0, 5),
('Hello World!','hello-world', 0, '*', 1, 1, 'hello-world', 1, 2),
('Goodbye World!','goodbye-world', 0, 'en-GB', 1, 1, 'goodbye-world', 3, 4);

/* Content History (Versions) / Tags? - for Projects, Categories */
INSERT INTO `#__content_types` (`type_title`, `type_alias`, `content_history_options`, `table`, `field_mappings`, `router`) 
VALUES
(
	'QWHelloWorld Project',

	'com_qwhelloworld.project',

	'{
		"formFile": "administrator\\/components\\/com_qwhelloworld\\/models\\/forms\\/project.xml",
		"hideFields": ["asset_id", "checked_out", "checked_out_time", "version", "lft", "rgt", "level", "path"],
		"ignoreChanges": ["checked_out", "checked_out_time", "path"],
		"convertToInt": [],
		"displayLookup": [
		{
			"sourceColumn": "created_by",
			"targetTable": "#__users",
			"targetColumn": "id",
			"displayColumn": "name"
		},
		{
			"sourceColumn": "parent_id",
			"targetTable": "#__com_qwhelloworld_projects",
			"targetColumn": "id",
			"displayColumn": "title"
		},
		{
			"sourceColumn": "catid",
			"targetTable": "#__categories",
			"targetColumn": "id",
			"displayColumn": "title"
		}]
	}',

	'{
		"special":
		{
			"dbtable": "#__com_qwhelloworld_projects",
			"key": "id",
			"type": "Project",
			"prefix": "QwhelloworldTable",
			"config": "array()"
		},
		"common":
		{
			"dbtable": "#__ucm_content",
			"key": "ucm_id",
			"type": "Corecontent",
			"prefix": "Table",
			"config": "array()"
		}
	}',

	'{
		"common":
		{
			"core_content_item_id": "id",
			"core_title": "title",
			"core_state": "published",
			"core_alias": "alias",
			"core_language": "language",
			"core_created_time": "created",
			"core_body": "description",
			"core_access": "access",
			"core_catid": "catid"
		}
	}',

	'QwhelloworldHelperRoute::getQwhelloworldRoute'
),

(
	'QWHelloWorld Category',

	'com_qwhelloworld.category',

	'{
		"formFile": "administrator\\/components\\/com_categories\\/models\\/forms\\/category.xml",
		"hideFields": ["asset_id", "checked_out", "checked_out_time", "version", "lft", "rgt", "level", "path", "extension"],
		"ignoreChanges": ["modified_user_id", "modified_time", "checked_out", "checked_out_time", "version", "hits", "path"],
		"convertToInt": ["publish_up", "publish_down"],
		"displayLookup": [
		{
			"sourceColumn": "created_user_id",
			"targetTable": "#__users",
			"targetColumn": "id",
			"displayColumn": "name"
		},
		{
			"sourceColumn": "access",
			"targetTable": "#__viewlevels",
			"targetColumn": "id",
			"displayColumn": "title"
		},
		{
			"sourceColumn": "modified_user_id",
			"targetTable": "#__users",
			"targetColumn": "id",
			"displayColumn": "name"
		},
		{
			"sourceColumn": "parent_id",
			"targetTable": "#__categories",
			"targetColumn": "id",
			"displayColumn": "title"
		}]
	}',

	'{
		"special":
		{
			"dbtable": "#__categories",
			"key": "id",
			"type": "Category",
			"prefix": "Table",
			"config": "array()"
		},
		"common":
		{
			"dbtable": "#__ucm_content",
			"key": "ucm_id",
			"type": "Corecontent",
			"prefix": "Table",
			"config": "array()"
		}
	}',

	'{
		"common":
		{
			"core_content_item_id": "id",
			"core_title": "title",
			"core_state": "published",
			"core_alias": "alias",
			"core_created_time": "created_time",
			"core_modified_time": "modified_time",
			"core_body": "description",
			"core_hits": "hits",
			"core_publish_up": "null",
			"core_publish_down": "null",
			"core_access": "access",
			"core_params": "params",
			"core_featured": "null",
			"core_metadata": "metadata",
			"core_language": "language",
			"core_images": "null",
			"core_urls": "null",
			"core_version": "version",
			"core_ordering": "null",
			"core_metakey": "metakey",
			"core_metadesc": "metadesc",
			"core_catid": "parent_id",
			"core_xreference": "null",
			"asset_id": "asset_id"
		},
		"special":
		{
			"parent_id": "parent_id",
			"lft": "lft",
			"rgt": "rgt",
			"level": "level",
			"path": "path",
			"extension": "extension",
			"note": "note"
		}
	}',

	'QwhelloworldHelperRoute::getCategoryRoute'
);