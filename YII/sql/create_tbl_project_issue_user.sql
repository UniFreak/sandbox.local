DROP TABLE IF EXISTS `tbl_project`;
CREATE TABLE `tbl_project`
(
`id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
`name` VARCHAR(128),
`description` TEXT,
`create_time` DATETIME,
`create_user_id` INTEGER,
`update_time` DATETIME,
`update_user_id` INTEGER
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `tbl_issue`
(
`id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
`name` VARCHAR(256) NOT NULL,
`description` VARCHAR(2000),
`project_id` INTEGER,
`type_id` INTEGER,
`status_id` INTEGER,
`owner_id` INTEGER,
`requester_id` INTEGER,
`create_time` DATETIME,
`create_user_id` INTEGER,
`update_time` DATETIME,
`update_user_id` INTEGER,
INDEX (`project_id`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `tbl_user`
(
`id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
`email` VARCHAR(256) NOT NULL,
`username` VARCHAR(256),
`password` VARCHAR(256),
`last_login_time` DATETIME,
`create_time` DATETIME,
`create_user_id` INTEGER,
`update_time` DATETIME,
`update_user_id` INTEGER
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `tbl_project_user_assignment`
(
`project_id` INT(11) NOT NULL,
`user_id` INT(11) NOT NULL,
`create_time` DATETIME,
`create_user_id` INTEGER,
`update_time` DATETIME,
`update_user_id` INTEGER,
PRIMARY KEY (`project_id`,`user_id`)
) ENGINE = InnoDB;

-- The Relationships

ALTER TABLE `tbl_issue` ADD CONSTRAINT `FK_issue_project`
FOREIGN KEY (`project_id`) REFERENCES `tbl_project` (`id`)
ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `tbl_issue` ADD CONSTRAINT `FK_issue_owner`
FOREIGN KEY (`owner_id`) REFERENCES `tbl_user` (`id`)
ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `tbl_issue` ADD CONSTRAINT `FK_issue_requester`
FOREIGN KEY (`requester_id`) REFERENCES `tbl_user` (`id`)
ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `tbl_project_user_assignment`
ADD CONSTRAINT `FK_project_user` FOREIGN KEY (`project_id`)
REFERENCES `tbl_project` (`id`)
ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `tbl_project_user_assignment`
ADD CONSTRAINT `FK_user_project` FOREIGN KEY (`user_id`)
REFERENCES `tbl_user` (`id`)
ON DELETE CASCADE ON UPDATE RESTRICT;

-- Insert some seed data so we can just begin using the database

INSERT INTO `tbl_user`
(`email`, `username`, `password`)
VALUES
('test1@notanaddress.com','Test_User_One', MD5('test1')),
('test2@notanaddress.com','Test_User_Two', MD5('test2'));