-- ============================================================
-- BHMS migration: split resident names + emergency contact
-- Run once against an existing `bhms_db`. Safe to keep for reference.
-- ============================================================
USE `bhms_db`;

-- --- residents: add separated name columns -------------------
ALTER TABLE `residents`
  ADD COLUMN `first_name`  VARCHAR(50) NOT NULL DEFAULT '' AFTER `id`,
  ADD COLUMN `middle_name` VARCHAR(50) NOT NULL DEFAULT '' AFTER `first_name`,
  ADD COLUMN `last_name`   VARCHAR(50) NOT NULL DEFAULT '' AFTER `middle_name`;

-- Backfill existing rows from the current full_name values.
UPDATE `residents` SET `first_name`='David',  `middle_name`='',      `last_name`='Martinez' WHERE `id`=1;
UPDATE `residents` SET `first_name`='Pearl',  `middle_name`='',      `last_name`='Douglas'  WHERE `id`=2;
UPDATE `residents` SET `first_name`='Juan',   `middle_name`='',      `last_name`='Dela Cruz' WHERE `id`=3;
UPDATE `residents` SET `first_name`='Lucy',   `middle_name`='Gomez', `last_name`='Martinez' WHERE `id`=4;
UPDATE `residents` SET `first_name`='John',   `middle_name`='Lloyd', `last_name`='Cruz'     WHERE `id`=5;
UPDATE `residents` SET `first_name`='Angela', `middle_name`='Lopez', `last_name`='Mendoza'  WHERE `id`=6;
UPDATE `residents` SET `first_name`='Samantha',`middle_name`='Panelo',`last_name`='Santos'  WHERE `id`=7;

-- Best-effort split for any rows not covered above (word 1 = first,
-- last word = last, anything in between = middle).
UPDATE `residents`
SET `first_name`  = SUBSTRING_INDEX(`full_name`, ' ', 1),
    `last_name`   = SUBSTRING_INDEX(`full_name`, ' ', -1),
    `middle_name` = TRIM(SUBSTRING(
        `full_name`,
        LENGTH(SUBSTRING_INDEX(`full_name`, ' ', 1)) + 2,
        GREATEST(LENGTH(`full_name`)
                 - LENGTH(SUBSTRING_INDEX(`full_name`, ' ', 1))
                 - LENGTH(SUBSTRING_INDEX(`full_name`, ' ', -1)) - 2, 0)))
WHERE `first_name` = '' AND `full_name` <> '';

-- --- survey_responses: add emergency contact columns ---------
ALTER TABLE `survey_responses`
  ADD COLUMN `ec_first_name`     VARCHAR(50) DEFAULT NULL AFTER `health_notes`,
  ADD COLUMN `ec_middle_name`    VARCHAR(50) DEFAULT NULL AFTER `ec_first_name`,
  ADD COLUMN `ec_last_name`      VARCHAR(50) DEFAULT NULL AFTER `ec_middle_name`,
  ADD COLUMN `ec_contact_number` VARCHAR(15) DEFAULT NULL AFTER `ec_last_name`,
  ADD COLUMN `ec_relationship`   VARCHAR(30) DEFAULT NULL AFTER `ec_contact_number`;
