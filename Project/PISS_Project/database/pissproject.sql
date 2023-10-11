SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `documenttable` (
  `id` varchar(20) NOT NULL,
  `class` varchar(50) NOT NULL,
  `innervalue` varchar(255) NOT NULL,
  `owner` varchar(30) NOT NULL,
  `style` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(30) NOT NULL,
  `type` enum('RegisteredUser','Administrator') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



INSERT INTO `users` (`id`, `username`, `password`, `email`, `type`) VALUES
(1, 'user1', '$2y$10$Z3E2Plnr0oipbaGn/NzyqOlSW7VlHpKFYxPTm/zOMNuZm1eUx.b3G', 'asdf@a.a', 'RegisteredUser'),
(2, 'user', '$2y$10$KmT1GI2WVuUDRTkxKqxNP.fgMXjnwSu1sifZpM.LYo9IuK7XGI7K2', 'asdf@a.a', 'RegisteredUser'),
(3, 'user3', '$2y$10$pD2cL3RL0sUgsbo18OSIfu5yqkYcgh9JsUrNbanUe3oIpuxKIn1ky', 'asdf@a.a', 'Administrator'),
(6, 'admin', '$2y$10$iIH1U2LEurc.oL/m.kUMjeDBfq1jbY0WytVfKESOvcxMEkD0uyhti', 'asdf@a.a', 'Administrator');

ALTER TABLE `documenttable`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;