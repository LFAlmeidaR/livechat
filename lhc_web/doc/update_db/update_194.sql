CREATE TABLE `lh_generic_bot_tr_group` ( `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(50) NOT NULL, PRIMARY KEY (`id`)) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `lh_generic_bot_tr_item` ( `id` int(11) NOT NULL AUTO_INCREMENT, `group_id` int(11) NOT NULL, `identifier` varchar(50) NOT NULL, `translation` text NOT NULL, PRIMARY KEY (`id`), KEY `identifier` (`identifier`), KEY `group_id` (`group_id`)) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;