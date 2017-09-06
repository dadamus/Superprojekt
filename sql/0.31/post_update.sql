ALTER TABLE `comments` MODIFY COLUMN `type` enum('detailView','costing','plateMultiCosting') NOT NULL;

CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `order_id` INT NOT NULL,
  `state` ENUM(  'oczekuje' ) NOT NULL,
  `deadline` date DEFAULT NULL,
  `deadline_on` tinyint(1) NOT NULL,
  `priority` int(2) NOT NULL DEFAULT '2',
  `created_at` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;