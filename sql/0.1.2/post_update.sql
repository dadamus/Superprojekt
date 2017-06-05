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