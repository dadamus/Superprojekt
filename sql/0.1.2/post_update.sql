ALTER TABLE `mpw` ADD  `frame` INT DEFAULT NULL AFTER `mcb`;
ALTER TABLE `plate_costingFrame` CHANGE  `plate_costingId`  `imgId` INT( 11 ) NOT NULL;
ALTER TABLE `plate_costingFrame` ADD  `points` TEXT NOT NULL AFTER  `height`;
ALTER TABLE `plate_costingFrame` ADD  `value` FLOAT NOT NULL AFTER  `points`;
ALTER TABLE  `plate_costingFrame` DROP  `width` , DROP  `height` ;

ALTER TABLE  `plate_singlePartCosting` ADD  `cut_path_time` TIME NOT NULL AFTER  `sheet_weight` ,
ADD  `move_time` TIME NOT NULL AFTER  `cut_path_time` ,
ADD  `sh_cut_time` TIME NOT NULL AFTER  `move_time` ,
ADD  `pierce_time` TIME NOT NULL AFTER  `sh_cut_time`;

CREATE TABLE plate_singlePartCostingCalculate(
	id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	plate_singlePartCosting INT(11) UNSIGNED,
	details_all_unf float,
  details_all_unf_per float,
  details_ext_unf float,
  details_ext_unf_per float,
  details_int_unf float,
  details_int_unf_per float,
  details_real_unf float,
	details_real_unf_per float,
	remnant_unf_per float,
	remnant_unf float,
	remnant_unf_value float,
	detail_mat_price float,
	cut_time float,
	clean_cut float,
	cut_komp_n float,
	cut_detal_n float,
	price_kom_n float,
	price_kom_b float,
	price_det_n float,
	price_det_b float
);

CREATE TABLE IF NOT EXISTS `plate_singlePartCostingMaterial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plate_singlePartCosting` int(11) NOT NULL,
  `materialPrice` float NOT NULL,
  `weightPrice` float NOT NULL,
  `scrapFactor` float NOT NULL,
  `scrapPrice` float NOT NULL,
  `cutPrice` float NOT NULL,
  `pFactor` float NOT NULL,
  `oTime` float NOT NULL,
  `oCost` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `plate_singlePartCostingAttribute` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plate_singlePartCosting` int(11) NOT NULL,
  `a1` tinyint(1) NOT NULL,
  `a2` tinyint(1) NOT NULL,
  `a3` tinyint(1) NOT NULL,
  `a4` tinyint(1) NOT NULL,
  `a5` tinyint(1) NOT NULL,
  `a6` tinyint(1) NOT NULL,
  `a7` tinyint(1) NOT NULL,
  `a1_value` float NOT NULL,
  `a2_value` float NOT NULL,
  `a3_value` float NOT NULL,
  `a4_value` float NOT NULL,
  `a5_value` float NOT NULL,
  `a6_value` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2 AUTO_INCREMENT=1 ;