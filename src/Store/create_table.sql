CREATE TABLE `bchubbwebToucanTokens` (
    `toucan_tokensId` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `relation` VARCHAR(32) NOT NULL FOREIGN KEY,
    `provider` VARCHAR(16) NOT NULL,
    `access_token` VARCHAR(4096) NOT NULL,
    `refresh_token` VARCHAR(2048) NOT NULL,
    `last_refresh` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `meta` longtext
)
