CREATE TEMPORARY TABLE IF NOT EXISTS `test` (
    `id_z` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title_z` VARCHAR(100) NOT NULL,
    `a` INT NULL DEFAULT NULL,
    `b_z` INT NULL DEFAULT NULL,
    PRIMARY KEY (`id_z`)
) ENGINE=Memory;

TRUNCATE TABLE `test`;

INSERT INTO `test` (`id_z`,`title_z`,`a`,`b_z`) VALUES
    (1, "one", 1, 10),
    (2, "two", 1, 8),
    (3, "three", 1, 6),
    (4, "four", 2, 4),
    (5, "five", 2, 2);
