ALTER TABLE cutting_queue_details RENAME COLUMN cutting_queue_id TO cutting_queue_list_id;
ALTER TABLE `cutting_queue_details` ADD `RectangleAreaW` FLOAT NULL AFTER `LaserMatName`;
ALTER TABLE oitems ADD COLUMN wz_item_id int(11) NULL;
ALTER TABLE oitems ADD COLUMN price float(11) NULL;
INSERT INTO clients` (`id`, `type`, `name`, `address`, `phone`, `person`, `email`) VALUES (0, '2', 'ABL-Tech', 'Test 123,32-123 Test', '', '', '');