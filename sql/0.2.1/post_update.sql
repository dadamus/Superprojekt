
CREATE TABLE IF NOT EXISTS `costingAttributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `type` enum('plateMultiPart'),
  `a1` tinyint(1),
  `a2` tinyint(1),
  `a3` tinyint(1),
  `a4` tinyint(1),
  `a5` tinyint(1),
  `a6` tinyint(1),
  `a7` tinyint(1),
  `a1_value` float,
  `a2_value` float,
  `a3_value` float,
  `a4_value` float,
  `a5_value` float,
  `a6_value` float,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin2 AUTO_INCREMENT=2 ;

CREATE TABLE plate_multiPartCostingDetailsSettings (
	id int(11) AUTO_INCREMENT NOT NULL,
        directory_id int(11),
        detaild_id int(11),
        p_factor int(11),
        PRIMARY KEY (id)
);

CREATE TABLE plate_multiPartProgramsSettings (
	id int(11) AUTO_INCREMENT NOT NULL,
        program_id int(11) NOT NULL,
        o_time int (11),
        mat_price float,
        prg_min_price float,
        PRIMARY KEY (`id`)
);