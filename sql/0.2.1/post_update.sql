DROP TABLE plate_multiPartDetails;
DROP TABLE plate_multiPartPrograms;
DROP TABLE plate_multiPartProgramsPart;

ALTER TABLE plate_multiPartDetails ADD COLUMN dirId int(11) DEFAULT NULL AFTER name;