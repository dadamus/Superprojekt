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
  `name` VARCHAR(32),
  `mpw` int(11),
  `did` int(11),
  `src` varchar(32),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `plate_multiPartCostingDetails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `did` int(11),
  `LaserMatName` varchar(64),
  `PreTime` varchar(16),
  `upload_date` datetime,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `plate_multiPartPrograms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `SheetName` varchar(64),
  `materialId` int(11),
  `UsedSheetNum` int(11)  ,
  `CreateDate` datetime  ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `plate_multiPartProgramsPart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `DetailId` INT(11) ,
  `PartNo` int(11)  ,
  `PartName` varchar(64)  ,
  `PartCount` float  ,
  `UnfoldXSize` float  ,
  `UnfoldYSize` float  ,
  `RectangleArea` float  ,
  `RectangleAreaW` float  ,
  `RectangleAreaWO` float  ,
  `Weight` float  ,
  `LaserMatName` varchar(64)  ,
  `ProgramId` int(11)  ,
  `CreateDate` datetime  ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `plate_multiPartCostingMaterial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `SheetCode` varchar(65)  ,
  `UsedSheetNum` int(11)  ,
  `MatName` varchar(32)  ,
  `thickness` float  ,
  `SheetSize` varchar(32)  ,
  `density` float  ,
  `price` float  ,
  `prgSheetPrice` float  ,
  `created_ad` datetime  ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `plate_costingFrame` CHANGE type type ENUM('singePartCosting', 'multiPartCosting');
ALTER TABLE `plate_costingFrame` ADD `programId` INT(11)  ;
ALTER TABLE `plate_singlePartCosting_image` RENAME `plate_CostingImage`;
ALTER TABLE `plate_CostingImage` CHANGE `plate_costingType` `plate_costingType` ENUM('singePartCosting', 'multiPartCosting');
