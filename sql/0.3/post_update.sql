ALTER TABLE  `plate_warehouse` CHANGE  `Priority`  `Priority` INT( 11 ) DEFAULT '5';
UPDATE  `plate_warehouse` SET Priority = 5;
ALTER TABLE  `plate_warehouse` ADD  `StartQty` INT AFTER  `QtyAvailable`;
ALTER TABLE  `plate_warehouse` ADD  `old` TINYINT( 1 ) DEFAULT  '0' AFTER  `modifyDate`;
UPDATE  `plate_warehouse` SET old = 1;
ALTER TABLE  `plate_warehouse` ADD  `OwnerID` INT  AFTER  `modifyDate`;
ALTER TABLE  `plate_warehouse` ADD  `UserID` INT AFTER  `old`;

ALTER TABLE  `plate_warehouse` ADD  `Price_kg` FLOAT  AFTER  `Price`;
ALTER TABLE  `plate_warehouse` ADD  `costs` FLOAT  AFTER  `Price`;
ALTER TABLE  `plate_warehouse` ADD  `actual_weight` FLOAT  AFTER  `Price`;
ALTER TABLE  `plate_warehouse` ADD  `program_weight` FLOAT  AFTER  `Price`;
ALTER TABLE  `plate_warehouse` ADD  `sheet_weight` FLOAT  AFTER  `Price`;
ALTER TABLE  `plate_warehouse` ADD  `difference_weight` FLOAT  AFTER  `Price`;
ALTER TABLE  `plate_warehouse` ADD  `sheet_actual_price` FLOAT  AFTER  `Price`;

ALTER TABLE  `plate_multiPartDirectories` ADD  `blocked` TINYINT( 1 ) NULL DEFAULT  '0' AFTER  `created_at`;

CREATE TABLE IF NOT EXISTS `designers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` enum('plateMulti') NOT NULL,
  `item_id` int(11) NOT NULL,
  `added_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `oitems` ADD  `did` INT NULL AFTER  `mpw`;

