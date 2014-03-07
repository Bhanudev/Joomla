DROP TABLE IF EXISTS `#__helloworld`;

CREATE TABLE `#__helloworld` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`TemplateName` VARCHAR(25) NOT NULL,
	`Subject` VARCHAR(32),
	`SenderName` VARCHAR(32),
	`SenderEmail` VARCHAR(32),
	`Body` TEXT,
	PRIMARY KEY (`id`),UNIQUE(`TemplateName`)
)
	ENGINE =MyISAM
	AUTO_INCREMENT =0
	DEFAULT CHARSET =utf8;

INSERT INTO `#__helloworld`( `TemplateName`, `Subject`, `SenderName`, `SenderEmail`, `Body`) VALUES ('Basic Template','Welcome To Our Website','JoomlaDeveloper','JoomlaDeveloper@joomla.org','Hi User, Welcome to Our Website.Hope you enjoy the previleges of a registered user.');

INSERT INTO `#__helloworld`( `TemplateName`, `Subject`, `SenderName`, `SenderEmail`, `Body`) VALUES ('Basic Template2','Good Bye User','JoomlaDeveloper','JoomlaDeveloper@joomla.org','Hi User, We are unhappy that you are leaving our website.Hope you come back. :(');
