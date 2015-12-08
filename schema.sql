CREATE DATABASE IF NOT EXISTS `Pazymejimai`;
USE `Pazymejimai`;
CREATE TABLE IF NOT EXISTS `Pazymejimai`.`prasymas`
(
    `id` char(10) NOT NULL,
    `vardas` char(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
    `pavarde` char(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
    `data` char(20) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
    `nuotrauka` char(255) NOT NULL,
    `lytis` char(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
    `kaklaraistis` char(200) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
    `pareigos` char(200) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
    `krastas` char(200) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
    `tuntas` char(200) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
    `draugove` char(200) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
    `adresas` char(200) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
    `el_pastas` char(200) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL,
    `telefono_numeris` char(20) NULL)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `Pazymejimai`.`bukle`
(
    `id` char(10) NOT NULL,
    `pagamintas` int(1) NULL,
    `nuotrauka` char(50) NULL,
    `mokestis` char(50) NULL,
    `data` char(20) NULL
)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `Pazymejimai`.`paskyra`
(
    `id` int(10) NOT NULL KEY AUTO_INCREMENT,
    `prisijungimas` char(50) NOT NULL,
    `slaptazodis` char(255) NOT NULL
)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;