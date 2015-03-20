DROP DATABASE `wuersch`;

CREATE DATABASE `wuersch` DEFAULT CHARACTER SET UTF8;

CREATE TABLE `wuersch`.`user`(
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_md5` VARCHAR(32) NULL,
  `secret` VARCHAR(1024) NULL,
  `name` VARCHAR(2048) NULL,
  `fb_id` BIGINT NULL,
  `isMale` TINYINT(1) NOT NULL DEFAULT 0,
  `isFemale` TINYINT(1) NOT NULL DEFAULT 0,
  `authenticated` TINYINT(1) NOT NULL DEFAULT 0,
  `registered` BIGINT NULL,
  `last_seen` BIGINT NULL,
  `last_ip` BIGINT NULL
) ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8
  COLLATE utf8_general_ci;

CREATE TABLE `wuersch`.`would`(
  `id_user_would` INT NOT NULL,
  `id_user` INT NOT NULL,
  `would` TINYINT(1) DEFAULT 0 NOT NULL,
  `time` BIGINT NULL
) ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8
  COLLATE utf8_general_ci;

