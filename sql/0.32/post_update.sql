ALTER TABLE  `tickets` CHANGE  `state`  `state` ENUM(  'oczekuje',  'do zaprogramowania',  'w produkcji' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

CREATE TABLE IF NOT EXISTS `cutting_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oitem_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `sheet_name` varchar(32) NOT NULL,
  `created_at` datetime NOT NULL,
  `cut_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;