CREATE TABLE IF NOT EXISTS `plate_singlePartCosting_image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(64) NOT NULL,
  `costing_name` varchar(32) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `plate_warehouse`
ADD `synced` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `plate_warehouse_memo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sheetcode` varchar(64) NOT NULL,
  `memo` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2 AUTO_INCREMENT=1;