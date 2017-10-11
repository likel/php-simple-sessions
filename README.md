# php-simple-sessions
Include this package in any PHP project to store encrypted $_SESSION variables in your MySQL database

## Getting Started

This package is designed to store PHP sessions in a MySQL database so that you can encrypt and access your site's active sessions.

### Prerequisites

* MySQL database
* PHP 5.1 and above for use of PDO

### Installing on your server

Create a session table in your MySQL database by running [install/setup.sql](install/setup.sql)

```
CREATE TABLE `likel_sessions` (
    `id` char(128) NOT NULL DEFAULT '',
    `set_time` char(10) NOT NULL,
    `data` text NOT NULL,
    `session_key` char(128) NOT NULL,
    `iv` varchar(16) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

Step 2

```
Example
```

Run [src/example.php](src/example.php) and check your database

## Running the tests

Run [test/SessionHandlerTest.php](test/SessionHandlerTest.php) and [test/SessionDBTest.php](test/SessionDBTest.php) with PHPUnit

```
$ phpunit test/SessionHandlerTest.php
$ phpunit test/SessionDBTest.php
```

## Author

**Liam Kelly** - [likel](https://github.com/likel)

## License

This project is licensed under the MIT - see the [LICENSE.md](LICENSE.md) file for details
