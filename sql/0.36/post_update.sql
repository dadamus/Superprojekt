ALTER TABLE `plate_warehouse` ADD `parentId` INT NULL AFTER `UserID`;
ALTER TABLE `cutting_queue` ADD `parent_synced` TINYINT( 1 )DEFAULT '0';