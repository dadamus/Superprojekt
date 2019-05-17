CREATE TABLE IF NOT EXISTS `plate_warehouse_log` (
  `sheetcode` varchar(128) NOT NULL,
  `type` int(11) NOT NULL,
  `text` varchar(256) NOT NULL,
  `date` datetime NOT NULL,
  `user` int(11) NOT NULL,
  `system` int(11) NOT NULL,
  PRIMARY KEY (`sheetcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;