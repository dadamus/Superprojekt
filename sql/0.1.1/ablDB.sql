CREATE TABLE `T_material` (
  `MaterialName` VARCHAR (50),
  `Thickness` FLOAT,
  `MaterialTypeName` VARCHAR(50),
  `Clearance` FLOAT,
  `Comment` text,
  `synced` INTEGER
);

ALTER TABLE `plate_warehouse` CHANGE  `Width`  `Width` FLOAT( 11 ) NOT NULL;
ALTER TABLE `plate_warehouse` CHANGE  `Height`  `Height` FLOAT( 11 ) NOT NULL;
ALTER TABLE `plate_warehouse` CHANGE  `MaterialName`  `MaterialName` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `plate_warehouse` CHANGE  `date`  `createDate` DATETIME NOT NULL;
ALTER TABLE `plate_warehouse` ADD     `modifyDate` DATETIME NOT NULL;
ALTER TABLE `plate_warehouse` DROP COLUMN `Thickness`;
ALTER TABLE `plate_warehouse` DROP `MaterialTypeName`;

CREATE TABLE IF NOT EXISTS `plate_costingFrame` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plate_costingId` INT NOT NULL,
  `type` enum('singePartCosting') NOT NULL,
  `width` float NOT NULL,
  `height` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `plate_singlePartCosting_image` ADD  `plate_costingId` INT NOT NULL AFTER  `id`;
ALTER TABLE  `plate_singlePartCosting_image` ADD  `plate_costingType` INT NOT NULL AFTER  `plate_costingId`;