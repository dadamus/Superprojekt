DROP TABLE plate_multiPartDirectories;
DROP TABLE plate_multiPartDetails;
DROP TABLE plate_multiPartCostingDetails;
DROP TABLE plate_multiPartPrograms;
DROP TABLE plate_multiPartProgramsPart;
DROP TABLE plate_multiPartCostingMaterial;

CREATE TABLE IF NOT EXISTS `plate_multiPartDirectories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dir_name` varchar(32),
  `created_at` datetime,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `plate_multiPartDetails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(32) DEFAULT null,
  `mpw` int(11) DEFAULT null,
  `did` int(11) DEFAULT null,
  `src` varchar(32) DEFAULT null,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `plate_multiPartCostingDetails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `did` int(11) DEFAULT null,
  `LaserMatName` varchar(64) DEFAULT null,
  `PreTime` varchar(16) DEFAULT null,
  `upload_date` datetime DEFAULT null,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `plate_multiPartPrograms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `SheetName` varchar(64) DEFAULT null,
  `materialId` int(11) DEFAULT null,
  `UsedSheetNum` int(11) DEFAULT null,
  `CreateDate` datetime DEFAULT null,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `plate_multiPartProgramsPart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `DetailId` INT(11) DEFAULT null,
  `PartNo` int(11) DEFAULT null,
  `PartName` varchar(64) DEFAULT null,
  `PartCount` float DEFAULT null,
  `UnfoldXSize` float DEFAULT null,
  `UnfoldYSize` float DEFAULT null,
  `RectangleArea` float DEFAULT null,
  `RectangleAreaW` float DEFAULT null,
  `RectangleAreaWO` float DEFAULT null,
  `Weight` float DEFAULT null,
  `LaserMatName` varchar(64) DEFAULT null,
  `ProgramId` int(11) DEFAULT null,
  `CreateDate` datetime DEFAULT null,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `plate_multiPartCostingMaterial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `SheetCode` varchar(65) DEFAULT null,
  `UsedSheetNum` int(11)  DEFAULT null,
  `MatName` varchar(32) DEFAULT null,
  `thickness` float DEFAULT null,
  `SheetSize` varchar(32) DEFAULT null,
  `density` float DEFAULT null,
  `price` float DEFAULT null,
  `prgSheetPrice` float DEFAULT null,
  `created_ad` datetime DEFAULT null,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `plate_costingFrame` CHANGE type type ENUM('singePartCosting', 'multiPartCosting');
ALTER TABLE `plate_costingFrame` ADD `programId` INT(11)  ;
ALTER TABLE `plate_singlePartCosting_image` RENAME `plate_CostingImage`;
ALTER TABLE `plate_CostingImage` CHANGE `plate_costingType` `plate_costingType` ENUM('singePartCosting', 'multiPartCosting');
