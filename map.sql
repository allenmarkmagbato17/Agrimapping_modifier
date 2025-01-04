CREATE TABLE `sensor_data` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `temperature` FLOAT DEFAULT NULL,
  `humidity` FLOAT DEFAULT NULL,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `moisture1` INT(11) DEFAULT NULL,
  `moisture2` INT(11) DEFAULT NULL,
  `moisture3` INT(11) DEFAULT NULL,
  `moisture4` INT(11) DEFAULT NULL,
  `moisture5` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `lands` (
  `land_id` bigint(20) NOT NULL,
  `land_name` varchar(100) DEFAULT NULL,
  `land_added` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `farmer_id` bigint(20) DEFAULT NULL,
  `bar_id` int(11) DEFAULT NULL,
  `sts` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `barangays` (
  `bar_id` int(11) NOT NULL,
  `bar_name` varchar(200) DEFAULT NULL,
  `mun_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `farmers` (
  `farmer_id` bigint(20) NOT NULL,
  `farmer_name` varchar(100) DEFAULT NULL,
  `farmer_address` varchar(100) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `contact_no` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
