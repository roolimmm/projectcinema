USE `cinema`;

CREATE TABLE `orders` (
  `order_id` char(32) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `order_tickets` (
  `order_ticket_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` char(32) NOT NULL,
  `screening_id` int(11) NOT NULL,
  `seat_label` varchar(4) NOT NULL,
  `ticket_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`order_ticket_id`),
  UNIQUE KEY `screening_seat_ordered` (`screening_id`, `seat_label`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_tickets_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `order_tickets_ibfk_2` FOREIGN KEY (`screening_id`) REFERENCES `screenings` (`screening_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
