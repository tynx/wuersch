DROP DATABASE `wuersch`;

CREATE DATABASE `wuersch` DEFAULT CHARACTER SET UTF8;

CREATE TABLE `wuersch`.`user`(
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_md5` VARCHAR(32) NULL,
  `secret` VARCHAR(1024) NULL,
  `name` VARCHAR(2048) NULL,
  `fb_id` BIGINT NULL,
  `fb_access_token` VARCHAR(1024) NULL,
  `is_male` TINYINT(1) NOT NULL DEFAULT 0,
  `is_female` TINYINT(1) NOT NULL DEFAULT 0,
  `interested_in_male` TINYINT(1) NOT NULL DEFAULT 0,
  `interested_in_female` TINYINT(1) NOT NULL DEFAULT 0,
  `setup_time` TINYINT(1) NOT NULL DEFAULT 0,
  `register_time` BIGINT NOT NULL DEFAULT 0,
  `authenticated_time` BIGINT NOT NULL DEFAULT 0,
  `last_seen` BIGINT NULL,
  `last_ip` VARCHAR(45) NULL
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

CREATE TABLE `wuersch`.`picture`(
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_md5` VARCHAR(32) NULL,
  `id_user` INT NOT NULL,
  `fb_id` BIGINT NULL,
  `default` TINYINT(1) DEFAULT 0 NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8
  COLLATE utf8_general_ci;
