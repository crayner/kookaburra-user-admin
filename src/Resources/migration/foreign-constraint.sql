ALTER TABLE `__prefix__Person` ADD CONSTRAINT FOREIGN KEY (`primary_role`) REFERENCES `__prefix__Role` (`id`),
    ADD CONSTRAINT FOREIGN KEY (`house`) REFERENCES `__prefix__House` (`id`),
    ADD CONSTRAINT FOREIGN KEY (`class_of_academic_year`) REFERENCES `__prefix__AcademicYear` (`id`),
    ADD CONSTRAINT FOREIGN KEY (`application_form`) REFERENCES `__prefix__ApplicationForm` (`__prefix__ApplicationFormID`),
    ADD CONSTRAINT FOREIGN KEY (`personal_theme`) REFERENCES `__prefix__Theme` (`__prefix__ThemeID`),
    ADD CONSTRAINT FOREIGN KEY (`personal_i18n`) REFERENCES `__prefix__i18n` (`__prefix__i18nID`);
ALTER TABLE `__prefix__FamilyAdult` ADD CONSTRAINT FOREIGN KEY (`family`) REFERENCES `__prefix__Family` (`id`),
    ADD CONSTRAINT FOREIGN KEY (`person`) REFERENCES `__prefix__Person` (`id`);
ALTER TABLE `__prefix__FamilyChild` ADD CONSTRAINT FOREIGN KEY (`family`) REFERENCES `__prefix__Family` (`id`),
    ADD CONSTRAINT FOREIGN KEY (`person`) REFERENCES `__prefix__Person` (`id`);
ALTER TABLE `__prefix__FamilyRelationship` ADD CONSTRAINT FOREIGN KEY (`family`) REFERENCES `__prefix__Family` (`id`),
    ADD CONSTRAINT FOREIGN KEY (`adult`) REFERENCES `__prefix__Person` (`id`),
    ADD CONSTRAINT FOREIGN KEY (`child`) REFERENCES `__prefix__Person` (`id`);
ALTER TABLE `__prefix__FamilyUpdate` ADD CONSTRAINT FOREIGN KEY (`academic_year`) REFERENCES __prefix__AcademicYear (id),
    ADD CONSTRAINT FOREIGN KEY (`family`) REFERENCES `__prefix__Family` (`id`),
    ADD CONSTRAINT FOREIGN KEY (`updater`) REFERENCES `__prefix__Person` (`id`);
ALTER TABLE `__prefix__PersonReset` ADD CONSTRAINT FOREIGN KEY (`person`) REFERENCES `__prefix__Person` (`id`);
