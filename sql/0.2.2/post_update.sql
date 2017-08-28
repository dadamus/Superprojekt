ALTER TABLE  `details` CHANGE  `img`  `img` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `plate_multiPartCostingDetailsSettings` ADD  `price` FLOAT NOT NULL AFTER  `p_factor`;
