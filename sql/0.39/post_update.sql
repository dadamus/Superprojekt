CREATE TABLE IF NOT EXISTS `warehouse_adjustments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `detail_id` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `comment` text,
  `user_id` INT NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2 AUTO_INCREMENT=1 ;

ALTER TABLE  `mpw` ADD  `plate_multiDirectory` INT NULL AFTER  `plate_multiDirectory`;
ALTER TABLE  `mpw` ADD  `cutting_conditions_name_id` INT NULL AFTER  `plate_multiDirectory`;
