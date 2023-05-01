-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema ecommerce
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema ecommerce
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `ecommerce` DEFAULT CHARACTER SET utf8 ;
USE `ecommerce` ;

-- -----------------------------------------------------
-- Table `ecommerce`.`usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`usuarios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(101) NOT NULL,
  `email` VARCHAR(101) NOT NULL,
  `password` VARCHAR(100) NOT NULL,
  `rol` VARCHAR(45) NOT NULL,
  `token` VARCHAR(100) NULL DEFAULT NULL,
  `direccion` VARCHAR(110) NOT NULL,
  `telefono` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`caja`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`caja` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `precio` FLOAT(10,2) NOT NULL,
  `id_usuario` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_caja_idusuario_idx` (`id_usuario` ASC),
  CONSTRAINT `fk_caja_idusuario`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `ecommerce`.`usuarios` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`chocolate`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`chocolate` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `precio` FLOAT(10,2) NOT NULL,
  `imagen` VARCHAR(100) NOT NULL,
  `marca` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`cajachocolates`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`cajachocolates` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_caja` INT(11) NOT NULL,
  `id_chocolate` INT(11) NOT NULL,
  `cantidad` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_cajachocolates_caja_idx` (`id_caja` ASC),
  INDEX `fk_cajachocoaltes_chocolate_idx` (`id_chocolate` ASC),
  CONSTRAINT `fk_cajachocoaltes_chocolate`
    FOREIGN KEY (`id_chocolate`)
    REFERENCES `ecommerce`.`chocolate` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_cajachocolates_caja`
    FOREIGN KEY (`id_caja`)
    REFERENCES `ecommerce`.`caja` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`compra`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`compra` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` INT(11) NOT NULL,
  `fecha_compra` DATETIME NOT NULL,
  `precio` FLOAT(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_comprausuario_idx` (`id_usuario` ASC),
  CONSTRAINT `fk_comprausuario`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `ecommerce`.`usuarios` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`compracaja`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`compracaja` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_compra` INT(11) NOT NULL,
  `id_caja` INT(11) NOT NULL,
  `cantidad` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_comprarcaja_compra_idx` (`id_compra` ASC),
  INDEX `fk_comprarcaja_caja_idx` (`id_caja` ASC),
  CONSTRAINT `fk_comprarcaja_caja`
    FOREIGN KEY (`id_caja`)
    REFERENCES `ecommerce`.`caja` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_comprarcaja_compra`
    FOREIGN KEY (`id_compra`)
    REFERENCES `ecommerce`.`compra` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`comprachocolates`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`comprachocolates` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_compra` INT(11) NOT NULL,
  `id_chocolate` INT(11) NOT NULL,
  `cantidad` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_comprarchocolates_chocolates_idx` (`id_chocolate` ASC),
  INDEX `fk_comprarchocolates_compra_idx` (`id_compra` ASC),
  CONSTRAINT `fk_comprarchocolates_chocolates`
    FOREIGN KEY (`id_chocolate`)
    REFERENCES `ecommerce`.`chocolate` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_comprarchocolates_compra`
    FOREIGN KEY (`id_compra`)
    REFERENCES `ecommerce`.`compra` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`favoritosusuariochocolates`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`favoritosusuariochocolates` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` INT(11) NOT NULL,
  `id_chocolate` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_favoritos_usuario_idx` (`id_usuario` ASC),
  INDEX `fk_favoritos_chocolates_idx` (`id_chocolate` ASC),
  CONSTRAINT `fk_favoritos_chocolates`
    FOREIGN KEY (`id_chocolate`)
    REFERENCES `ecommerce`.`chocolate` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_favoritos_usuario`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `ecommerce`.`usuarios` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`feedback`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`feedback` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` INT(11) NOT NULL,
  `comentario` VARCHAR(200) NOT NULL,
  `rating` INT(2) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_feedbackusuario_idx` (`id_usuario` ASC),
  CONSTRAINT `fk_feedbackusuario`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `ecommerce`.`usuarios` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
