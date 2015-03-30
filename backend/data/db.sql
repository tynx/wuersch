DROP DATABASE `wuersch`;

CREATE DATABASE `wuersch` DEFAULT CHARACTER SET UTF8;

CREATE TABLE `wuersch`.`user`(
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_md5` VARCHAR(32) NULL,
  `secret` VARCHAR(1024) NULL,
  `name` VARCHAR(2048) NULL,
  `id_fb` BIGINT NULL,
  `fb_access_token` VARCHAR(1024) NULL,
  `is_male` TINYINT(1) NOT NULL DEFAULT 0,
  `is_female` TINYINT(1) NOT NULL DEFAULT 0,
  `interested_in_male` TINYINT(1) NOT NULL DEFAULT 0,
  `interested_in_female` TINYINT(1) NOT NULL DEFAULT 0,
  `fetch_time` BIGINT NOT NULL DEFAULT 0,
  `register_time` BIGINT NOT NULL DEFAULT 0,
  `authenticated_time` BIGINT NOT NULL DEFAULT 0,
  `last_seen` BIGINT NOT NULL DEFAULT 0,
  `last_ip` VARCHAR(45) NULL
) ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8
  COLLATE utf8_general_ci;

CREATE TABLE `wuersch`.`would`(
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_user_would` INT NOT NULL,
  `id_user` INT NOT NULL,
  `would` TINYINT(1) DEFAULT 0 NOT NULL,
  `time` BIGINT NOT NULL DEFAULT 0
) ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8
  COLLATE utf8_general_ci;

CREATE TABLE `wuersch`.`picture`(
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_md5` VARCHAR(32) NULL,
  `id_user` INT NOT NULL,
  `id_fb` BIGINT NULL,
  `default` TINYINT(1) DEFAULT 0 NOT NULL,
  `time` BIGINT NOT NULL DEFAULT 0
) ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8
  COLLATE utf8_general_ci;

CREATE TABLE `wuersch`.`match`(
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_user_1` INT NOT NULL,
  `id_user_2` INT NOT NULL,
  `time` BIGINT NOT NULL DEFAULT 0
) ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8
  COLLATE utf8_general_ci;

ALTER TABLE `wuersch`.`would` ADD CONSTRAINT `fk_would_user1` FOREIGN KEY (`id_user_would`) REFERENCES `wuersch`.`user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `wuersch`.`would` ADD CONSTRAINT `fk_would_user2` FOREIGN KEY (`id_user`) REFERENCES `wuersch`.`user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `wuersch`.`picture` ADD CONSTRAINT `fk_picture_user` FOREIGN KEY (`id_user`) REFERENCES `wuersch`.`user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `wuersch`.`match` ADD CONSTRAINT `fk_match_user1` FOREIGN KEY (`id_user_1`) REFERENCES `wuersch`.`user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `wuersch`.`match` ADD CONSTRAINT `fk_match_user2` FOREIGN KEY (`id_user_2`) REFERENCES `wuersch`.`user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
