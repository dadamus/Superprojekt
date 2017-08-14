CREATE TABLE IF NOT EXISTS `plate_multiPartDirectories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dir_name` varchar(32) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `plate_multiPartDetails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mpw` int(11) NOT NULL,
  `did` int(11) NOT NULL,
  `src` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `plate_multiPartCostingDetails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `did` int(11) NOT NULL,
  `LaserMatName` varchar(64) NOT NULL,
  `PreTime` varchar(16) NOT NULL,
  `upload_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `plate_multiPartPrograms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `SheetName` varchar(64) NOT NULL,
  `materialId` int(11) NOT NULL,
  `UsedSheetNum` int(11) NOT NULL,
  `CreateDate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `plate_multiPartProgramsPart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `PartNo` int(11) NOT NULL,
  `PartName` varchar(64) NOT NULL,
  `PartCount` float NOT NULL,
  `UnfoldXSize` float NOT NULL,
  `UnfoldYSize` float NOT NULL,
  `RectangleArea` float NOT NULL,
  `RectangleAreaW` float NOT NULL,
  `RectangleAreaWO` float NOT NULL,
  `Weight` float NOT NULL,
  `LaserMatName` varchar(64) NOT NULL,
  `ProgramId` int(11) NOT NULL,
  `CreateDate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `plate_multiPartCostingMaterial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `SheetCode` varchar(65) NOT NULL,
  `UsedSheetNum` int(11) NOT NULL,
  `MatName` varchar(32) NOT NULL,
  `thickness` float NOT NULL,
  `SheetSize` varchar(32) NOT NULL,
  `density` float NOT NULL,
  `price` float NOT NULL,
  `prgSheetPrice` float NOT NULL,
  `created_ad` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;