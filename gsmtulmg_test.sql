-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.20-log - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             9.4.0.5174
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for procedure gsmtulmg_test.makeTransaction
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `makeTransaction`(
	IN `debet_user_id` INT,
	IN `credit_user_id` INT,
	IN `summ` DECIMAL(10,2)





)
BEGIN
DECLARE cnt INT;
DECLARE remainder DECIMAL(10,2);

IF debet_user_id = credit_user_id THEN 
	SIGNAL SQLSTATE '45001' SET MESSAGE_TEXT='Users are the same';
END IF;

IF summ <= 0 THEN
	SIGNAL SQLSTATE '45002' SET MESSAGE_TEXT='Wrong summ';
END IF;

SELECT count(*) INTO cnt
FROM `users` 
WHERE (`id` = debet_user_id OR `id` = credit_user_id)
	AND `is_active` = 1;
IF cnt != 2 THEN 
	SIGNAL SQLSTATE '45003' SET MESSAGE_TEXT='User not found';
END IF;

SELECT `balance`-summ INTO remainder
FROM `users`
WHERE `id`=debet_user_id;
IF remainder < 0 THEN
	SIGNAL SQLSTATE '45004' SET MESSAGE_TEXT='Not enough money on the balance';
END IF;


START TRANSACTION;

INSERT INTO `transactions`
(`debet_user_id`, `credit_user_id`, `summ`)
VALUES
(debet_user_id, credit_user_id, summ);

UPDATE `users`
SET `balance` = `balance` + summ
WHERE `id`=credit_user_id;

UPDATE `users`
SET `balance` = remainder
WHERE `id`=debet_user_id;

COMMIT;

END//
DELIMITER ;

-- Dumping structure for table gsmtulmg_test.transactions
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `debet_user_id` int(11) NOT NULL,
  `credit_user_id` int(11) NOT NULL,
  `summ` decimal(10,2) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `debet_user_id` (`debet_user_id`),
  KEY `credit_user_id` (`credit_user_id`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `fk_credit_user_id` FOREIGN KEY (`credit_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_debet_user_id` FOREIGN KEY (`debet_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- Dumping data for table gsmtulmg_test.transactions: ~2 rows (approximately)
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` (`id`, `debet_user_id`, `credit_user_id`, `summ`, `created_at`) VALUES
	(12, 1, 2, 1000.00, '2019-11-26 19:34:43'),
	(13, 2, 1, 1000.00, '2019-11-26 19:35:47');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;

-- Dumping structure for table gsmtulmg_test.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(64) NOT NULL,
  `balance` decimal(10,2) unsigned NOT NULL DEFAULT '10000.00',
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_name` (`user_name`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- Dumping data for table gsmtulmg_test.users: ~4 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `user_name`, `balance`, `is_active`) VALUES
	(1, '1', 10000.00, b'1'),
	(2, '2', 10000.00, b'1');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
