ALTER TABLE  `plate_multiPartCostingDetailsSettings` CHANGE  `p_factor`  `p_factor` FLOAT( 11 ) NULL DEFAULT NULL;

CREATE TABLE IF NOT EXISTS `warehouse_remnant_check` (
  `plate_warehouse_id` int(11) NOT NULL UNIQUE,
  `remnant_check` tinyint(1),
  `text` text,
  `operator_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE  `cutting_queue` ADD  `modified_at` DATETIME NOT NULL AFTER  `created_at`;
ALTER TABLE  `cutting_queue_details` ADD  `waste` INT NOT NULL AFTER  `cutting`;
