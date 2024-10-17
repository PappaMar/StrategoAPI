-- --------------------------------------------------------
-- Διακομιστής:                  127.0.0.1
-- Έκδοση διακομιστή:            10.4.28-MariaDB - mariadb.org binary distribution
-- Λειτ. σύστημα διακομιστή:     Win64
-- HeidiSQL Έκδοση:              12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for procedure stratego.attack_on_same_piece
DROP PROCEDURE IF EXISTS `attack_on_same_piece`;
DELIMITER //
CREATE PROCEDURE `attack_on_same_piece`(
	IN `x1` TINYINT,
	IN `y1` TINYINT,
	IN `x2` TINYINT,
	IN `y2` TINYINT
)
BEGIN
DECLARE p1, p_color1 CHAR;
SELECT piece, piece_color INTO p1, p_color1
FROM `board` WHERE x=x1 AND y=y1;
UPDATE board
SET piece=NULL, piece_color=NULL
WHERE x=x2 AND y=y2;
UPDATE board
SET piece=NULL,piece_color=NULL
WHERE x=x1 AND y=y1;

UPDATE game_status SET p_turn = if(p_color1 = 'R', 'B', 'R');
END//
DELIMITER ;

-- Dumping structure for procedure stratego.attack_on_stronger_piece
DROP PROCEDURE IF EXISTS `attack_on_stronger_piece`;
DELIMITER //
CREATE PROCEDURE `attack_on_stronger_piece`(
	IN `x1` TINYINT,
	IN `y1` TINYINT,
	IN `x2` TINYINT,
	IN `y2` TINYINT
)
BEGIN
DECLARE p2, p_color2 CHAR;
SELECT piece, piece_color INTO p2, p_color2
FROM `board` WHERE x=x1 AND y=y1;
UPDATE board
SET piece=NULL,piece_color=NULL
WHERE x=x1 AND y=y1;

UPDATE game_status SET p_turn = if(p_color2 = 'R', 'B', 'R');
END//
DELIMITER ;

-- Dumping structure for πίνακας stratego.board
DROP TABLE IF EXISTS `board`;
CREATE TABLE IF NOT EXISTS `board` (
  `x` tinyint(1) NOT NULL,
  `y` tinyint(1) NOT NULL,
  `b_color` enum('GN','GY') NOT NULL,
  `piece_color` enum('B','R') DEFAULT NULL,
  `piece` enum('1','2','3','4','5','6','7','8','9','B','F','S') DEFAULT NULL,
  PRIMARY KEY (`x`,`y`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table stratego.board: ~100 rows (approximately)
INSERT INTO `board` (`x`, `y`, `b_color`, `piece_color`, `piece`) VALUES
	(1, 1, 'GN', NULL, NULL),
	(1, 2, 'GN', NULL, NULL),
	(1, 3, 'GN', NULL, NULL),
	(1, 4, 'GN', NULL, NULL),
	(1, 5, 'GN', NULL, NULL),
	(1, 6, 'GN', NULL, NULL),
	(1, 7, 'GN', NULL, NULL),
	(1, 8, 'GN', NULL, NULL),
	(1, 9, 'GN', NULL, NULL),
	(1, 10, 'GN', NULL, NULL),
	(2, 1, 'GN', NULL, NULL),
	(2, 2, 'GN', NULL, NULL),
	(2, 3, 'GN', NULL, NULL),
	(2, 4, 'GN', NULL, NULL),
	(2, 5, 'GN', NULL, NULL),
	(2, 6, 'GN', NULL, NULL),
	(2, 7, 'GN', NULL, NULL),
	(2, 8, 'GN', NULL, NULL),
	(2, 9, 'GN', NULL, NULL),
	(2, 10, 'GN', NULL, NULL),
	(3, 1, 'GN', NULL, NULL),
	(3, 2, 'GN', NULL, NULL),
	(3, 3, 'GN', NULL, NULL),
	(3, 4, 'GN', NULL, NULL),
	(3, 5, 'GN', NULL, NULL),
	(3, 6, 'GN', NULL, NULL),
	(3, 7, 'GN', NULL, NULL),
	(3, 8, 'GN', NULL, NULL),
	(3, 9, 'GN', NULL, NULL),
	(3, 10, 'GN', NULL, NULL),
	(4, 1, 'GN', NULL, NULL),
	(4, 2, 'GN', NULL, NULL),
	(4, 3, 'GN', NULL, NULL),
	(4, 4, 'GN', NULL, NULL),
	(4, 5, 'GN', NULL, NULL),
	(4, 6, 'GN', NULL, NULL),
	(4, 7, 'GN', NULL, NULL),
	(4, 8, 'GN', NULL, NULL),
	(4, 9, 'GN', NULL, NULL),
	(4, 10, 'GN', NULL, NULL),
	(5, 1, 'GN', NULL, NULL),
	(5, 2, 'GN', NULL, NULL),
	(5, 3, 'GY', NULL, NULL),
	(5, 4, 'GY', NULL, NULL),
	(5, 5, 'GN', NULL, NULL),
	(5, 6, 'GN', NULL, NULL),
	(5, 7, 'GY', NULL, NULL),
	(5, 8, 'GY', NULL, NULL),
	(5, 9, 'GN', NULL, NULL),
	(5, 10, 'GN', NULL, NULL),
	(6, 1, 'GN', NULL, NULL),
	(6, 2, 'GN', NULL, NULL),
	(6, 3, 'GY', NULL, NULL),
	(6, 4, 'GY', NULL, NULL),
	(6, 5, 'GN', NULL, NULL),
	(6, 6, 'GN', NULL, NULL),
	(6, 7, 'GY', NULL, NULL),
	(6, 8, 'GY', NULL, NULL),
	(6, 9, 'GN', NULL, NULL),
	(6, 10, 'GN', NULL, NULL),
	(7, 1, 'GN', NULL, NULL),
	(7, 2, 'GN', NULL, NULL),
	(7, 3, 'GN', NULL, NULL),
	(7, 4, 'GN', NULL, NULL),
	(7, 5, 'GN', NULL, NULL),
	(7, 6, 'GN', NULL, NULL),
	(7, 7, 'GN', NULL, NULL),
	(7, 8, 'GN', NULL, NULL),
	(7, 9, 'GN', NULL, NULL),
	(7, 10, 'GN', NULL, NULL),
	(8, 1, 'GN', NULL, NULL),
	(8, 2, 'GN', NULL, NULL),
	(8, 3, 'GN', NULL, NULL),
	(8, 4, 'GN', NULL, NULL),
	(8, 5, 'GN', NULL, NULL),
	(8, 6, 'GN', NULL, NULL),
	(8, 7, 'GN', NULL, NULL),
	(8, 8, 'GN', NULL, NULL),
	(8, 9, 'GN', NULL, NULL),
	(8, 10, 'GN', NULL, NULL),
	(9, 1, 'GN', NULL, NULL),
	(9, 2, 'GN', NULL, NULL),
	(9, 3, 'GN', NULL, NULL),
	(9, 4, 'GN', NULL, NULL),
	(9, 5, 'GN', NULL, NULL),
	(9, 6, 'GN', NULL, NULL),
	(9, 7, 'GN', NULL, NULL),
	(9, 8, 'GN', NULL, NULL),
	(9, 9, 'GN', NULL, NULL),
	(9, 10, 'GN', NULL, NULL),
	(10, 1, 'GN', NULL, NULL),
	(10, 2, 'GN', NULL, NULL),
	(10, 3, 'GN', NULL, NULL),
	(10, 4, 'GN', NULL, NULL),
	(10, 5, 'GN', NULL, NULL),
	(10, 6, 'GN', NULL, NULL),
	(10, 7, 'GN', NULL, NULL),
	(10, 8, 'GN', NULL, NULL),
	(10, 9, 'GN', NULL, NULL),
	(10, 10, 'GN', NULL, NULL);

-- Dumping structure for πίνακας stratego.board_full
DROP TABLE IF EXISTS `board_full`;
CREATE TABLE IF NOT EXISTS `board_full` (
  `x` tinyint(1) NOT NULL,
  `y` tinyint(1) NOT NULL,
  `b_color` enum('GN','GY') NOT NULL,
  `piece_color` enum('B','R') DEFAULT NULL,
  `piece` enum('F','B','S','1','2','3','4','5','6','7','8','9') DEFAULT NULL,
  PRIMARY KEY (`x`,`y`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table stratego.board_full: ~100 rows (approximately)
INSERT INTO `board_full` (`x`, `y`, `b_color`, `piece_color`, `piece`) VALUES
	(1, 1, 'GN', 'R', '9'),
	(1, 2, 'GN', 'R', 'B'),
	(1, 3, 'GN', 'R', '4'),
	(1, 4, 'GN', 'R', '3'),
	(1, 5, 'GN', 'R', '3'),
	(1, 6, 'GN', 'R', 'S'),
	(1, 7, 'GN', 'R', '1'),
	(1, 8, 'GN', 'R', '2'),
	(1, 9, 'GN', 'R', 'B'),
	(1, 10, 'GN', 'R', 'F'),
	(2, 1, 'GN', 'R', 'B'),
	(2, 2, 'GN', 'R', 'B'),
	(2, 3, 'GN', 'R', '4'),
	(2, 4, 'GN', 'R', '4'),
	(2, 5, 'GN', 'R', '5'),
	(2, 6, 'GN', 'R', '5'),
	(2, 7, 'GN', 'R', '5'),
	(2, 8, 'GN', 'R', '5'),
	(2, 9, 'GN', 'R', 'B'),
	(2, 10, 'GN', 'R', 'B'),
	(3, 1, 'GN', 'R', '6'),
	(3, 2, 'GN', 'R', '7'),
	(3, 3, 'GN', 'R', '7'),
	(3, 4, 'GN', 'R', '7'),
	(3, 5, 'GN', 'R', '7'),
	(3, 6, 'GN', 'R', '8'),
	(3, 7, 'GN', 'R', '8'),
	(3, 8, 'GN', 'R', '6'),
	(3, 9, 'GN', 'R', '6'),
	(3, 10, 'GN', 'R', '6'),
	(4, 1, 'GN', 'R', '9'),
	(4, 2, 'GN', 'R', '9'),
	(4, 3, 'GN', 'R', '9'),
	(4, 4, 'GN', 'R', '8'),
	(4, 5, 'GN', 'R', '9'),
	(4, 6, 'GN', 'R', '9'),
	(4, 7, 'GN', 'R', '8'),
	(4, 8, 'GN', 'R', '8'),
	(4, 9, 'GN', 'R', '9'),
	(4, 10, 'GN', 'R', '9'),
	(5, 1, 'GN', NULL, NULL),
	(5, 2, 'GN', NULL, NULL),
	(5, 3, 'GY', NULL, NULL),
	(5, 4, 'GY', NULL, NULL),
	(5, 5, 'GN', NULL, NULL),
	(5, 6, 'GN', NULL, NULL),
	(5, 7, 'GY', NULL, NULL),
	(5, 8, 'GY', NULL, NULL),
	(5, 9, 'GN', NULL, NULL),
	(5, 10, 'GN', NULL, NULL),
	(6, 1, 'GN', NULL, NULL),
	(6, 2, 'GN', NULL, NULL),
	(6, 3, 'GY', NULL, NULL),
	(6, 4, 'GY', NULL, NULL),
	(6, 5, 'GN', NULL, NULL),
	(6, 6, 'GN', NULL, NULL),
	(6, 7, 'GY', NULL, NULL),
	(6, 8, 'GY', NULL, NULL),
	(6, 9, 'GN', NULL, NULL),
	(6, 10, 'GN', NULL, NULL),
	(7, 1, 'GN', 'B', '9'),
	(7, 2, 'GN', 'B', '9'),
	(7, 3, 'GN', 'B', '9'),
	(7, 4, 'GN', 'B', '8'),
	(7, 5, 'GN', 'B', '9'),
	(7, 6, 'GN', 'B', '9'),
	(7, 7, 'GN', 'B', '8'),
	(7, 8, 'GN', 'B', '8'),
	(7, 9, 'GN', 'B', '9'),
	(7, 10, 'GN', 'B', '9'),
	(8, 1, 'GN', 'B', '6'),
	(8, 2, 'GN', 'B', '7'),
	(8, 3, 'GN', 'B', '7'),
	(8, 4, 'GN', 'B', '7'),
	(8, 5, 'GN', 'B', '7'),
	(8, 6, 'GN', 'B', '8'),
	(8, 7, 'GN', 'B', '8'),
	(8, 8, 'GN', 'B', '6'),
	(8, 9, 'GN', 'B', '6'),
	(8, 10, 'GN', 'B', '6'),
	(9, 1, 'GN', 'B', 'B'),
	(9, 2, 'GN', 'B', 'B'),
	(9, 3, 'GN', 'B', '4'),
	(9, 4, 'GN', 'B', '4'),
	(9, 5, 'GN', 'B', '5'),
	(9, 6, 'GN', 'B', '5'),
	(9, 7, 'GN', 'B', '5'),
	(9, 8, 'GN', 'B', '5'),
	(9, 9, 'GN', 'B', 'B'),
	(9, 10, 'GN', 'B', 'B'),
	(10, 1, 'GN', 'B', 'F'),
	(10, 2, 'GN', 'B', 'B'),
	(10, 3, 'GN', 'B', '4'),
	(10, 4, 'GN', 'B', '3'),
	(10, 5, 'GN', 'B', '3'),
	(10, 6, 'GN', 'B', 'S'),
	(10, 7, 'GN', 'B', '1'),
	(10, 8, 'GN', 'B', '2'),
	(10, 9, 'GN', 'B', 'B'),
	(10, 10, 'GN', 'B', '9');

-- Dumping structure for πίνακας stratego.board_start
DROP TABLE IF EXISTS `board_start`;
CREATE TABLE IF NOT EXISTS `board_start` (
  `x` tinyint(1) NOT NULL,
  `y` tinyint(1) NOT NULL,
  `b_color` enum('GN','GY') NOT NULL,
  `piece_color` enum('B','R') DEFAULT NULL,
  `piece` enum('F','B','S','1','2','3','4','5','6','7','8','9') DEFAULT NULL,
  PRIMARY KEY (`x`,`y`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table stratego.board_start: ~100 rows (approximately)
INSERT INTO `board_start` (`x`, `y`, `b_color`, `piece_color`, `piece`) VALUES
	(1, 1, 'GN', NULL, NULL),
	(1, 2, 'GN', NULL, NULL),
	(1, 3, 'GN', NULL, NULL),
	(1, 4, 'GN', NULL, NULL),
	(1, 5, 'GN', NULL, NULL),
	(1, 6, 'GN', NULL, NULL),
	(1, 7, 'GN', NULL, NULL),
	(1, 8, 'GN', NULL, NULL),
	(1, 9, 'GN', NULL, NULL),
	(1, 10, 'GN', NULL, NULL),
	(2, 1, 'GN', NULL, NULL),
	(2, 2, 'GN', NULL, NULL),
	(2, 3, 'GN', NULL, NULL),
	(2, 4, 'GN', NULL, NULL),
	(2, 5, 'GN', NULL, NULL),
	(2, 6, 'GN', NULL, NULL),
	(2, 7, 'GN', NULL, NULL),
	(2, 8, 'GN', NULL, NULL),
	(2, 9, 'GN', NULL, NULL),
	(2, 10, 'GN', NULL, NULL),
	(3, 1, 'GN', NULL, NULL),
	(3, 2, 'GN', NULL, NULL),
	(3, 3, 'GN', NULL, NULL),
	(3, 4, 'GN', NULL, NULL),
	(3, 5, 'GN', NULL, NULL),
	(3, 6, 'GN', NULL, NULL),
	(3, 7, 'GN', NULL, NULL),
	(3, 8, 'GN', NULL, NULL),
	(3, 9, 'GN', NULL, NULL),
	(3, 10, 'GN', NULL, NULL),
	(4, 1, 'GN', NULL, NULL),
	(4, 2, 'GN', NULL, NULL),
	(4, 3, 'GN', NULL, NULL),
	(4, 4, 'GN', NULL, NULL),
	(4, 5, 'GN', NULL, NULL),
	(4, 6, 'GN', NULL, NULL),
	(4, 7, 'GN', NULL, NULL),
	(4, 8, 'GN', NULL, NULL),
	(4, 9, 'GN', NULL, NULL),
	(4, 10, 'GN', NULL, NULL),
	(5, 1, 'GN', NULL, NULL),
	(5, 2, 'GN', NULL, NULL),
	(5, 3, 'GY', NULL, NULL),
	(5, 4, 'GY', NULL, NULL),
	(5, 5, 'GN', NULL, NULL),
	(5, 6, 'GN', NULL, NULL),
	(5, 7, 'GY', NULL, NULL),
	(5, 8, 'GY', NULL, NULL),
	(5, 9, 'GN', NULL, NULL),
	(5, 10, 'GN', NULL, NULL),
	(6, 1, 'GN', NULL, NULL),
	(6, 2, 'GN', NULL, NULL),
	(6, 3, 'GY', NULL, NULL),
	(6, 4, 'GY', NULL, NULL),
	(6, 5, 'GN', NULL, NULL),
	(6, 6, 'GN', NULL, NULL),
	(6, 7, 'GY', NULL, NULL),
	(6, 8, 'GY', NULL, NULL),
	(6, 9, 'GN', NULL, NULL),
	(6, 10, 'GN', NULL, NULL),
	(7, 1, 'GN', NULL, NULL),
	(7, 2, 'GN', NULL, NULL),
	(7, 3, 'GN', NULL, NULL),
	(7, 4, 'GN', NULL, NULL),
	(7, 5, 'GN', NULL, NULL),
	(7, 6, 'GN', NULL, NULL),
	(7, 7, 'GN', NULL, NULL),
	(7, 8, 'GN', NULL, NULL),
	(7, 9, 'GN', NULL, NULL),
	(7, 10, 'GN', NULL, NULL),
	(8, 1, 'GN', NULL, NULL),
	(8, 2, 'GN', NULL, NULL),
	(8, 3, 'GN', NULL, NULL),
	(8, 4, 'GN', NULL, NULL),
	(8, 5, 'GN', NULL, NULL),
	(8, 6, 'GN', NULL, NULL),
	(8, 7, 'GN', NULL, NULL),
	(8, 8, 'GN', NULL, NULL),
	(8, 9, 'GN', NULL, NULL),
	(8, 10, 'GN', NULL, NULL),
	(9, 1, 'GN', NULL, NULL),
	(9, 2, 'GN', NULL, NULL),
	(9, 3, 'GN', NULL, NULL),
	(9, 4, 'GN', NULL, NULL),
	(9, 5, 'GN', NULL, NULL),
	(9, 6, 'GN', NULL, NULL),
	(9, 7, 'GN', NULL, NULL),
	(9, 8, 'GN', NULL, NULL),
	(9, 9, 'GN', NULL, NULL),
	(9, 10, 'GN', NULL, NULL),
	(10, 1, 'GN', NULL, NULL),
	(10, 2, 'GN', NULL, NULL),
	(10, 3, 'GN', NULL, NULL),
	(10, 4, 'GN', NULL, NULL),
	(10, 5, 'GN', NULL, NULL),
	(10, 6, 'GN', NULL, NULL),
	(10, 7, 'GN', NULL, NULL),
	(10, 8, 'GN', NULL, NULL),
	(10, 9, 'GN', NULL, NULL),
	(10, 10, 'GN', NULL, NULL);

-- Dumping structure for procedure stratego.clean_board
DROP PROCEDURE IF EXISTS `clean_board`;
DELIMITER //
CREATE PROCEDURE `clean_board`()
BEGIN
REPLACE INTO board SELECT * FROM board_start;
END//
DELIMITER ;

-- Dumping structure for procedure stratego.fill_board
DROP PROCEDURE IF EXISTS `fill_board`;
DELIMITER //
CREATE PROCEDURE `fill_board`()
BEGIN
REPLACE INTO board SELECT * FROM board_full;
END//
DELIMITER ;

-- Dumping structure for πίνακας stratego.game_status
DROP TABLE IF EXISTS `game_status`;
CREATE TABLE IF NOT EXISTS `game_status` (
  `status` enum('not active','initialized','piece_positioning','started','ended','aborded') NOT NULL DEFAULT 'not active',
  `p_turn` enum('B','R') DEFAULT NULL,
  `result` enum('B','R') DEFAULT NULL,
  `last_change` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table stratego.game_status: ~0 rows (approximately)
INSERT INTO `game_status` (`status`, `p_turn`, `result`, `last_change`) VALUES
	('not active', NULL, NULL, '2024-01-06 16:24:24');

-- Dumping structure for procedure stratego.move_piece
DROP PROCEDURE IF EXISTS `move_piece`;
DELIMITER //
CREATE PROCEDURE `move_piece`(
	IN `x1` TINYINT,
	IN `y1` TINYINT,
	IN `x2` TINYINT,
	IN `y2` TINYINT
)
BEGIN
DECLARE p, p_color CHAR;
SELECT piece, piece_color INTO p, p_color FROM board WHERE x=x1 AND y=y1;

UPDATE board
SET piece=p, piece_color=p_color
WHERE x=x2 AND y=y2;

UPDATE board
SET piece=NULL,piece_color=NULL
WHERE x=x1 AND y=y1;

UPDATE game_status SET p_turn = if(p_color = 'R', 'B', 'R');
END//
DELIMITER ;

-- Dumping structure for procedure stratego.place_piece
DROP PROCEDURE IF EXISTS `place_piece`;
DELIMITER //
CREATE PROCEDURE `place_piece`(x1 TINYINT, y1 TINYINT, pi_color CHAR, piece_type CHAR)
BEGIN
DECLARE row_count INT;
-- Check if the cell is empty and can have a piece placed
SELECT COUNT(*) INTO row_count FROM board WHERE x = x1 AND y = y1 AND (piece_color IS NULL OR piece_color = pi_color);
IF row_count > 0 THEN
  UPDATE board SET piece_color = pi_color, piece = piece_type WHERE x = x1 AND y = y1;
ELSE
  -- Cell is occupied or cannot have the piece placed
  SIGNAL SQLSTATE '45000'
  SET MESSAGE_TEXT = 'Cannot place piece in this cell';
END IF;
END//
DELIMITER ;

-- Dumping structure for πίνακας stratego.players
DROP TABLE IF EXISTS `players`;
CREATE TABLE IF NOT EXISTS `players` (
  `username` varchar(20) DEFAULT NULL,
  `piece_color` enum('B','R') NOT NULL,
  `token` varchar(32) DEFAULT NULL,
  `last_action` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`piece_color`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table stratego.players: ~2 rows (approximately)
INSERT INTO `players` (`username`, `piece_color`, `token`, `last_action`) VALUES
	(NULL, 'B', NULL, '2024-01-06 16:03:22'),
	(NULL, 'R', NULL, '2024-01-06 16:03:22');

-- Dumping structure for procedure stratego.update_result
DROP PROCEDURE IF EXISTS `update_result`;
DELIMITER //
CREATE PROCEDURE `update_result`(
	IN `p_color3` CHAR
)
BEGIN
/* SELECT status, p_turn, result FROM `game_status`; */
UPDATE game_status
SET status='ended', p_turn=NULL, result=p_color3;
END//
DELIMITER ;

-- Dumping structure for trigger stratego.game_status_update
DROP TRIGGER IF EXISTS `game_status_update`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `game_status_update` BEFORE UPDATE ON `game_status` FOR EACH ROW BEGIN
SET NEW.last_change = NOW();
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
