/**
 * Author:  Derek
 * Created: 2-Mar-2018
 */

CREATE TABLE `hbstudio`.`hb_dic_en_zh` (
  `dic_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `en` VARCHAR(32) NOT NULL,
  `zh` VARCHAR(1024) NOT NULL,
  PRIMARY KEY (`dic_id`),
  UNIQUE INDEX `en_UNIQUE` (`en` ASC));

CREATE TABLE `hbstudio`.`hb_history` (
  `his_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `en` VARCHAR(32) NOT NULL,
  `zh` VARCHAR(1024) NOT NULL,
  `count` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`his_id`),
  INDEX `user_en` (`user_id` ASC, `en` ASC),
  INDEX `user_count` (`user_id` ASC, `count` DESC));

CREATE TABLE `hbstudio`.`hb_user` (
  `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL,
  `password` VARCHAR(64) NOT NULL,
  `email` VARCHAR(128) NOT NULL,
  `mobile` VARCHAR(32) NOT NULL DEFAULT '',
  `regtime` DATETIME NOT NULL,
  `birthday` DATE NULL,
  PRIMARY KEY (`user_id`));

CREATE TABLE `hbstudio`.`hb_session` (
  `sess_id` INT NOT NULL,
  `sessionid` VARCHAR(64) NOT NULL,
  `userid` INT UNSIGNED NOT NULL,
  `data` VARCHAR(256) NOT NULL DEFAULT '',
  `lasttime` DATETIME NOT NULL,
  `lastip` VARCHAR(45) NOT NULL DEFAULT '',
  `dev_type` VARCHAR(32) NOT NULL DEFAULT '',
  `dev_id` VARCHAR(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`sess_id`),
  INDEX `sess_user` (`sessionid` ASC, `userid` ASC));
