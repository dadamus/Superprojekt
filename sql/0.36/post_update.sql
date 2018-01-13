ALTER TABLE `plate_warehouse` ADD `parentId` INT NULL AFTER `UserID`;
ALTER TABLE `cutting_queue` ADD `parent_synced` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `status`;