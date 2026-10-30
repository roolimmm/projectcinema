USE `cinema`;

ALTER TABLE `screenings`
  MODIFY `screening_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `screening_seats`
  ADD PRIMARY KEY (`screening_seat_id`),
  MODIFY `screening_seat_id` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `screenings` (`movie_id`, `cinema`, `screening_date`, `screening_time`)
SELECT schedule.movie_id, schedule.cinema, schedule.screening_date, schedule.screening_time
FROM (
  SELECT 1 AS movie_id, 'Orchard' AS cinema, '2026-11-01' AS screening_date, '13:30:00' AS screening_time
  UNION ALL SELECT 1, 'Orchard', '2026-11-01', '19:15:00'
  UNION ALL SELECT 1, 'Harbour', '2026-11-02', '18:30:00'
  UNION ALL SELECT 1, 'Yishun', '2026-11-04', '19:00:00'
  UNION ALL SELECT 1, 'Harbour', '2026-11-06', '16:00:00'
  UNION ALL SELECT 1, 'Yishun', '2026-11-07', '20:00:00'
  UNION ALL SELECT 2, 'Harbour', '2026-11-01', '16:00:00'
  UNION ALL SELECT 2, 'Yishun', '2026-11-02', '20:15:00'
  UNION ALL SELECT 2, 'Orchard', '2026-11-03', '18:45:00'
  UNION ALL SELECT 2, 'Harbour', '2026-11-04', '20:30:00'
  UNION ALL SELECT 2, 'Yishun', '2026-11-06', '17:00:00'
  UNION ALL SELECT 2, 'Orchard', '2026-11-07', '20:30:00'
  UNION ALL SELECT 5, 'Yishun', '2026-11-01', '11:30:00'
  UNION ALL SELECT 5, 'Orchard', '2026-11-02', '20:00:00'
  UNION ALL SELECT 5, 'Harbour', '2026-11-03', '17:30:00'
  UNION ALL SELECT 5, 'Yishun', '2026-11-05', '19:45:00'
  UNION ALL SELECT 5, 'Orchard', '2026-11-06', '16:30:00'
  UNION ALL SELECT 5, 'Harbour', '2026-11-07', '20:15:00'
  UNION ALL SELECT 8, 'Orchard', '2026-11-01', '20:00:00'
  UNION ALL SELECT 8, 'Harbour', '2026-11-02', '19:30:00'
  UNION ALL SELECT 8, 'Yishun', '2026-11-03', '18:00:00'
  UNION ALL SELECT 8, 'Orchard', '2026-11-04', '20:30:00'
  UNION ALL SELECT 8, 'Harbour', '2026-11-06', '19:45:00'
  UNION ALL SELECT 8, 'Yishun', '2026-11-07', '20:30:00'
  UNION ALL SELECT 9, 'Harbour', '2026-11-01', '10:30:00'
  UNION ALL SELECT 9, 'Yishun', '2026-11-02', '14:00:00'
  UNION ALL SELECT 9, 'Orchard', '2026-11-03', '15:30:00'
  UNION ALL SELECT 9, 'Harbour', '2026-11-05', '11:00:00'
  UNION ALL SELECT 9, 'Yishun', '2026-11-06', '14:30:00'
  UNION ALL SELECT 9, 'Orchard', '2026-11-07', '11:30:00'
) AS schedule
WHERE NOT EXISTS (
  SELECT 1
  FROM `screenings` existing
  WHERE existing.movie_id = schedule.movie_id
    AND existing.cinema = schedule.cinema
    AND existing.screening_date = schedule.screening_date
    AND existing.screening_time = schedule.screening_time
);

INSERT INTO `screening_seats` (`screening_id`, `seat_label`, `status`)
SELECT
  s.screening_id,
  CONCAT(CHAR(64 + row_numbers.row_number), seat_numbers.seat_number),
  CASE
    WHEN MOD(s.screening_id + row_numbers.row_number + seat_numbers.seat_number * 3, 11) = 0
      OR (row_numbers.row_number = 4 AND seat_numbers.seat_number = 5)
    THEN 'unavailable'
    ELSE 'available'
  END
FROM `screenings` s
CROSS JOIN (
  SELECT 1 AS row_number UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
  UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
) AS row_numbers
CROSS JOIN (
  SELECT 1 AS seat_number UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
  UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
) AS seat_numbers
LEFT JOIN `screening_seats` existing
  ON existing.screening_id = s.screening_id
  AND existing.seat_label = CONCAT(CHAR(64 + row_numbers.row_number), seat_numbers.seat_number)
WHERE existing.screening_seat_id IS NULL;
