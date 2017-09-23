ALTER TABLE  `tickets` CHANGE  `state`  `state` ENUM(  'oczekuje',  'do zaprogramowania',  'w produkcji' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

CREATE TABLE IF NOT EXISTS `cutting_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sheet_count` int(11) NOT NULL,
  `sheet_name` varchar(32) NOT NULL,
  `created_at` datetime NOT NULL,
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `cutting_queue_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cutting_queue_id` int(11) NOT NULL,
  `oitem_id` int(11) NOT NULL,
  `qantity` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2 AUTO_INCREMENT=1;

ALTER TABLE  `programs` ADD  `new_cutting_queue_id` INT NOT NULL AFTER  `timestudy`;