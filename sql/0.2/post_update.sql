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