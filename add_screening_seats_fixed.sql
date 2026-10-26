-- Run this once against an existing cinema database.

CREATE TABLE IF NOT EXISTS `screening_seats` (
  `screening_seat_id` int(11) NOT NULL AUTO_INCREMENT,
  `screening_id` int(11) NOT NULL,
  `seat_label` varchar(4) NOT NULL,
  `status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  PRIMARY KEY (`screening_seat_id`),
  UNIQUE KEY `screening_seat` (`screening_id`, `seat_label`),
  KEY `screening_id` (`screening_id`),
  CONSTRAINT `screening_seats_ibfk_1`
    FOREIGN KEY (`screening_id`) REFERENCES `screenings` (`screening_id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP PROCEDURE IF EXISTS seed_screening_seats;

DELIMITER $$
CREATE PROCEDURE seed_screening_seats(IN target_screening_id INT)
BEGIN
    DECLARE seat_row_number INT DEFAULT 1;
    DECLARE seat_column_number INT DEFAULT 1;
    DECLARE seat_status VARCHAR(11);

    IF NOT EXISTS (
        SELECT 1 FROM screening_seats
        WHERE screening_id = target_screening_id
        LIMIT 1
    ) THEN
        WHILE seat_row_number <= 8 DO
            SET seat_column_number = 1;

            WHILE seat_column_number <= 10 DO
                SET seat_status = 'available';

                IF MOD(target_screening_id + seat_row_number + seat_column_number * 3, 11) = 0
                    OR (seat_row_number = 4 AND seat_column_number = 5)
                THEN
                    SET seat_status = 'unavailable';
                END IF;

                INSERT INTO screening_seats (screening_id, seat_label, status)
                VALUES (
                    target_screening_id,
                    CONCAT(CHAR(64 + seat_row_number), seat_column_number),
                    seat_status
                );

                SET seat_column_number = seat_column_number + 1;
            END WHILE;

            SET seat_row_number = seat_row_number + 1;
        END WHILE;
    END IF;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS seed_all_missing_screening_seats;

DELIMITER $$
CREATE PROCEDURE seed_all_missing_screening_seats()
BEGIN
    DECLARE finished INT DEFAULT 0;
    DECLARE current_screening_id INT;
    DECLARE screening_cursor CURSOR FOR
        SELECT screening_id FROM screenings;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET finished = 1;

    OPEN screening_cursor;

    screening_loop: LOOP
        FETCH screening_cursor INTO current_screening_id;
        IF finished = 1 THEN
            LEAVE screening_loop;
        END IF;
        CALL seed_screening_seats(current_screening_id);
    END LOOP;

    CLOSE screening_cursor;
END$$
DELIMITER ;

CALL seed_all_missing_screening_seats();
DROP PROCEDURE seed_all_missing_screening_seats;

DROP TRIGGER IF EXISTS create_screening_seats;

DELIMITER $$
CREATE TRIGGER create_screening_seats
AFTER INSERT ON screenings
FOR EACH ROW
BEGIN
    CALL seed_screening_seats(NEW.screening_id);
END$$
DELIMITER ;
