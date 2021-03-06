CREATE TABLE `__prefix__Country` (
                                     `id` int(4) UNSIGNED AUTO_INCREMENT,
                                     `printable_name` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
                                     `iddCountryCode` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
                                     PRIMARY KEY (`id`),
                                     UNIQUE KEY `printable_name` (`printable_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT = 1;

CREATE TABLE `__prefix__Person` (
      `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
      `title` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `surname` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `firstName` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `preferredName` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `officialName` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `nameInCharacters` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `gender` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Unspecified',
      `username` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `password` varchar(191) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `passwordForceReset` varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'Force user to reset password on next login.',
      `status` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Full',
      `canLogin` varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
      `all_roles` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '(DC2Type:simple_array)',
      `dob` date DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
      `email` varchar(75) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `emailAlternate` varchar(75) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `image_240` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `lastIPAddress` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `lastTimestamp` datetime DEFAULT NULL,
      `lastFailIPAddress` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `lastFailTimestamp` datetime DEFAULT NULL,
      `failCount` int(1) DEFAULT NULL,
      `address1` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `address1District` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `address1Country` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `address2` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `address2District` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `address2Country` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `phone1Type` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `phone1CountryCode` int(4) UNSIGNED DEFAULT NULL,
      `phone1` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `phone2Type` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `phone2CountryCode` int(4) UNSIGNED DEFAULT NULL,
      `phone2` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `phone3Type` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `phone3CountryCode` int(4) UNSIGNED DEFAULT NULL,
      `phone3` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `phone4Type` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `phone4CountryCode` int(4) UNSIGNED DEFAULT NULL,
      `phone4` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `website` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `languageFirst` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `languageSecond` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `languageThird` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `countryOfBirth` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `birthCertificateScan` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `ethnicity` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `citizenship1` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `citizenship1Passport` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `citizenship1PassportScan` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `citizenship2` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `citizenship2Passport` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `religion` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `nationalIDCardNumber` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `nationalIDCardScan` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `residencyStatus` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `visaExpiryDate` date DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
      `profession` varchar(90) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `employer` varchar(90) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `jobTitle` varchar(90) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `emergency1Name` varchar(90) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `emergency1Number1` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `emergency1Number2` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `emergency1Relationship` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `emergency2Name` varchar(90) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `emergency2Number1` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `emergency2Number2` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `emergency2Relationship` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `studentID` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `dateStart` date DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
      `dateEnd` date DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
      `lastSchool` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `nextSchool` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `departureReason` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `transport` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `transportNotes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `calendarFeedPersonal` varchar(192) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `viewCalendarSchool` varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
      `viewCalendarPersonal` varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
      `viewCalendarSpaceBooking` varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
      `lockerNumber` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `vehicleRegistration` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `personalBackground` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `messengerLastBubble` date DEFAULT NULL,
      `privacy` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `dayType` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Student day type, as specified in the application form.',
      `studentAgreements` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `googleAPIRefreshToken` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `receiveNotificationEmails` varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
      `fields` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'Serialised array of custom field values(DC2Type:array)',
      `primary_role` int(3) UNSIGNED DEFAULT NULL,
      `house` int(3) UNSIGNED DEFAULT NULL,
      `class_of_academic_year` int(3) UNSIGNED DEFAULT NULL,
      `application_form` int(12) UNSIGNED DEFAULT NULL,
      `personal_theme` int(4) UNSIGNED DEFAULT NULL,
      `personal_i18n` int(4) UNSIGNED DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `username` (`username`),
      KEY `username_2` (`username`,`email`) USING BTREE,
      KEY `primaryRole` (`primary_role`) USING BTREE,
      KEY `house` (`house`) USING BTREE,
      KEY `classOfAcademicYear` (`class_of_academic_year`) USING BTREE,
      KEY `applicationForm` (`application_form`) USING BTREE,
      KEY `personalTheme` (`personal_theme`) USING BTREE,
      KEY `personalI18n` (`personal_i18n`) USING BTREE,
      KEY `phone_code_1` (`phone1CountryCode`) USING BTREE,
      KEY `phone_code_2` (`phone2CountryCode`) USING BTREE,
      KEY `phone_code_3` (`phone3CountryCode`) USING BTREE,
      KEY `phone_code_4` (`phone4CountryCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `__prefix__District` (
    `id` INT(6) UNSIGNED AUTO_INCREMENT,
    `name` VARCHAR(30) NOT NULL,
    `territory` VARCHAR(30) NULL DEFAULT NULL,
    `post_code` VARCHAR(10) NULL DEFAULT NULL,
    PRIMARY KEY(`id`)
) DEFAULT CHARACTER SET=utf8 COLLATE=utf8_unicode_ci ENGINE=InnoDB AUTO_INCREMENT=1;

CREATE TABLE `__prefix__FamilyUpdate` (
    `id` INT(9) UNSIGNED AUTO_INCREMENT,
    `status` VARCHAR(8) DEFAULT 'Pending' NOT NULL, 
    `nameAddress` VARCHAR(100) NOT NULL, 
    `homeAddress` LONGTEXT NOT NULL, 
    `homeAddressDistrict` VARCHAR(255) NOT NULL, 
    `homeAddressCountry` VARCHAR(255) NOT NULL, 
    `languageHomePrimary` VARCHAR(30) NOT NULL, 
    `languageHomeSecondary` VARCHAR(30) NOT NULL, 
    `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, 
    `academic_year` INT(3) UNSIGNED,
    `family` INT(7) UNSIGNED,
    `updater` INT(10) UNSIGNED,
    INDEX `academicYear` (`academic_year`), 
    INDEX `family` (`family`), 
    INDEX `updater` (`updater`), 
    INDEX `familyYear` (`family`, `academic_year`), 
    PRIMARY KEY(`id`)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB AUTO_INCREMENT = 1;

CREATE TABLE `__prefix__Family` (
  `id` int(7) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `nameAddress` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The formal name to be used for addressing the family (e.g. Mr. & Mrs. Smith)',
  `homeAddress` longtext COLLATE utf8_unicode_ci NOT NULL,
  `homeAddressDistrict` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `homeAddressCountry` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `languageHomePrimary` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `languageHomeSecondary` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `familySync` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `familySync` (`familySync`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT = 1;
    
CREATE TABLE `__prefix__FamilyAdult` (
  `id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `comment` longtext COLLATE utf8_unicode_ci NOT NULL,
  `childDataAccess` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `contactPriority` int(2) DEFAULT NULL,
  `contactCall` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `contactSMS` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `contactEmail` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `contactMail` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `family` int(7) UNSIGNED DEFAULT NULL,
  `person` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `familyMember` (`family`,`person`),
  KEY `family` (`family`),
  KEY `person` (`person`),
  KEY `familyContactPriority` (`family`,`contactPriority`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT = 1;
    
CREATE TABLE `__prefix__FamilyChild` (
  `id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `comment` longtext COLLATE utf8_unicode_ci NOT NULL,
  `family` int(7) UNSIGNED DEFAULT NULL,
  `person` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `familyMember` (`family`,`person`),
  KEY `family` (`family`),
  KEY `person` (`person`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT = 1;

CREATE TABLE `__prefix__FamilyRelationship` (
  `id` int(9) UNSIGNED NOT NULL AUTO_INCREMENT,
  `relationship` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `family` int(7) UNSIGNED DEFAULT NULL,
  `adult` int(10) UNSIGNED DEFAULT NULL,
  `child` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `family` (`family`),
  KEY `adult` (`adult`),
  KEY `student` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT = 1;

CREATE TABLE `__prefix__PersonField` (
    id INT(3) UNSIGNED AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL, 
    active VARCHAR(1) DEFAULT 'Y' NOT NULL, 
    description VARCHAR(255) NOT NULL, 
    type VARCHAR(10) NOT NULL, 
    `options` LONGTEXT NOT NULL COMMENT 'Field length for varchar, rows for text, comma-separate list for select/checkbox.', 
    required VARCHAR(1) DEFAULT 'N' NOT NULL, 
    activePersonStudent TINYINT(1) DEFAULT '0' NOT NULL, 
    activePersonStaff TINYINT(1) DEFAULT '0' NOT NULL, 
    activePersonParent TINYINT(1) DEFAULT '0' NOT NULL, 
    activePersonOther TINYINT(1) DEFAULT '0' NOT NULL, 
    activeApplicationForm TINYINT(1) DEFAULT '0' NOT NULL, 
    activeDataUpdater TINYINT(1) DEFAULT '0' NOT NULL, 
    activePublicRegistration TINYINT(1) DEFAULT '0' NOT NULL, 
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB AUTO_INCREMENT = 1;

CREATE TABLE `__prefix__PersonReset` (
    `id` INT(12) UNSIGNED AUTO_INCREMENT,
    `key` VARCHAR(40) NOT NULL,
    `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, 
    `person` INT(10) UNSIGNED NOT NULL,
    INDEX `person` (`person`), 
    PRIMARY KEY(`id`)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB AUTO_INCREMENT = 1;

CREATE TABLE `__prefix__StaffAbsenceType` (
    id INT(6) UNSIGNED AUTO_INCREMENT,
    name VARCHAR(60) DEFAULT NULL, 
    nameShort VARCHAR(10) DEFAULT NULL, 
    active VARCHAR(1) DEFAULT 'Y', 
    requiresApproval VARCHAR(1) DEFAULT 'N', 
    reasons LONGTEXT DEFAULT NULL, 
    sequenceNumber INT(3), 
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB AUTO_INCREMENT = 1;

CREATE TABLE `__prefix__StudentNoteCategory` (
    id INT(5) UNSIGNED AUTO_INCREMENT,
    name VARCHAR(30) NOT NULL, 
    template LONGTEXT NOT NULL, 
    active VARCHAR(1) DEFAULT 'Y' NOT NULL, 
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB AUTO_INCREMENT = 1;

CREATE TABLE __prefix__UsernameFormat (
    id INT(3) UNSIGNED AUTO_INCREMENT,
    gibbonRoleIDList VARCHAR(255) DEFAULT NULL,
    format VARCHAR(255) DEFAULT NULL,
    isDefault VARCHAR(1) DEFAULT 'N' NOT NULL,
    isNumeric VARCHAR(1) DEFAULT 'N' NOT NULL,
    numericValue INT(12) UNSIGNED,
    numericIncrement INT(3) UNSIGNED,
    numericSize INT(3) UNSIGNED,
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB AUTO_INCREMENT = 1;
