ALTER TABLE programs
DROP COLUMN multiplier,
DROP COLUMN material_name,
DROP COLUMN material_type,
DROP COLUMN pierceposition,
DROP COLUMN pipelength,
DROP COLUMN widthheightdiameter,
DROP COLUMN widthheight,
DROP COLUMN diameter,
DROP COLUMN cpipecornerr,
DROP COLUMN utilization,
DROP COLUMN timestudy;

CREATE TABLE IF NOT EXISTS `sheet_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plate_warehouse_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `src` varchar(256) NOT NULL,
  `upload_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2 AUTO_INCREMENT=1;