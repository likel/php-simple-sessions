CREATE TABLE `likel_sessions` (
    `id` char(128) NOT NULL DEFAULT '',
    `set_time` char(10) NOT NULL,
    `data` text NOT NULL,
    `session_key` char(128) NOT NULL,
    `iv` varchar(16) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
