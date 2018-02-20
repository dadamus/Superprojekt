ALTER TABLE  `cutting_queue_details` CHANGE  `qantity`  `quantity` INT( 11 ) NOT NULL;

CREATE TABLE IF NOT EXISTS `details_cutted_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cutting_queue_detail_id` int(11) NOT NULL,
  `details_cutted` float NOT NULL,
  `details_remnant` float NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
);