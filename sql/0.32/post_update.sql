ALTER TABLE  `tickets` CHANGE  `state`  `state` ENUM(  'oczekuje',  'do zaprogramowania',  'w produkcji' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;


