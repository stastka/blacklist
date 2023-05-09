--
-- Database: `databasename`
--
CREATE DATABASE IF NOT EXISTS `databasename` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `databasename`;

-- --------------------------------------------------------

--
-- Table structure for table `blacklist`
--

CREATE TABLE `blacklist` (
  `id_ip` bigint(20) UNSIGNED NOT NULL,
  `ip` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `fqdn` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `dt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blacklist_log`
--

CREATE TABLE `blacklist_log` (
  `id_log` int(11) NOT NULL,
  `action` text NOT NULL,
  `ip` text NOT NULL,
  `fqdn` text NOT NULL,
  `dt` text NOT NULL,
  `user` text NOT NULL,
  `remoteip` text NOT NULL,
  `dt_log` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blacklist`
--
ALTER TABLE `blacklist`
  ADD PRIMARY KEY (`id_ip`);

--
-- Indexes for table `blacklist_log`
--
ALTER TABLE `blacklist_log`
  ADD PRIMARY KEY (`id_log`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blacklist`
--
ALTER TABLE `blacklist`
  MODIFY `id_ip` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blacklist_log`
--
ALTER TABLE `blacklist_log`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;


CREATE USER 'restusername'@'localhost' IDENTIFIED BY 'restpassword';
GRANT ALL PRIVILEGES ON `databasename`.* TO 'restusername'@'localhost';
FLUSH PRIVILEGES;