CREATE TABLE wz_address (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `address_name` varchar(128),
    `address1` VARCHAR(128),
    `address2` VARCHAR(128),
    `nip` VARCHAR(64),
    PRIMARY KEY (`id`)
);

CREATE TABLE wz (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `seller_address_id` int(11),
    `buyer_address_id` int(11),
    `rows` VARCHAR(64),
    `create_date` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE wz_item (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wz_id` int(11) NOT NULL,
  `oitem_id` int(11) NOT NULL,
  `code` VARCHAR(64),
  `name` VARCHAR(64),
  `price` FLOAT(11),
  `quantity` INT(11),
  PRIMARY KEY (`id`)
);

INSERT INTO wz_address (`address_name`, `address1`, `address2`, `nip`) VALUES ('ABL-Tech', 'test', 'test', '');
INSERT INTO settings (`name`, `value`) VALUES ('wz_seller_address_id', 1);